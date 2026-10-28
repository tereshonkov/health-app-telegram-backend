<?php

namespace App\Console\Commands;

use App\Models\Anniversary;
use App\Models\Birthday;
use App\Services\BirthdayGreetingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class CongratulateBirthdays extends Command
{
    protected $signature = 'birthdays:congratulate
        {--dry-run : Show messages without sending}
        {--date= : Pretend today is this date (Y-m-d), for testing}';

    protected $description = 'Send birthday and anniversary greetings to the friends chat';

    public function handle(BirthdayGreetingService $greetings): int
    {
        $today = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now();

        $greetingsToSend = collect();

        Birthday::onDate($today)
            ->notCongratulatedIn($today->year)
            ->get()
            ->each(fn(Birthday $b) => $greetingsToSend->push([$b, $greetings->buildMessage($b)]));

        Anniversary::onDate($today)
            ->notCongratulatedIn($today->year)
            ->get()
            ->each(fn(Anniversary $a) => $greetingsToSend->push([$a, $greetings->buildAnniversaryMessage($a)]));

        if ($greetingsToSend->isEmpty()) {
            $this->info('Nothing to celebrate today.');
            return self::SUCCESS;
        }

        foreach ($greetingsToSend as [$model, $text]) {
            if ($this->option('dry-run')) {
                $this->line("[dry-run] {$text}");
                continue;
            }

            try {
                Telegram::bot('birthday')->sendMessage([
                    'chat_id' => config('birthdays.chat_id'),
                    'text'    => $text,
                ]);

                $model->update(['last_congratulated_year' => $today->year]);

                Log::info('Greeting sent', ['type' => class_basename($model), 'id' => $model->id]);
            } catch (\Throwable $e) {
                Log::error('Greeting failed', [
                    'type'  => class_basename($model),
                    'id'    => $model->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error('Failed for ' . class_basename($model) . " #{$model->id}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
