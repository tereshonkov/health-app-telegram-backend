<?php

namespace App\Console\Commands;

use App\Models\Birthday;
use App\Services\BirthdayGreetingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class CongratulateBirthdays extends Command
{
    protected $signature = 'birthdays:congratulate
    {--dry-run : Show messages without sending}
    {--date= : Pretend today is this date (Y-m-d), for testing}';

    protected $description = 'Send birthday greetings to the friends chat';

    public function handle(BirthdayGreetingService $greetings): int
    {
        $today = $this->option('date')
            ? \Carbon\Carbon::parse($this->option('date'))
            : now();

        $birthdays = Birthday::query()
            ->where(function ($q) use ($today) {
                $q->onDate($today);

                if (! $today->isLeapYear() && $today->month === 2 && $today->day === 28) {
                    $q->orWhere(fn($q) => $q->where('month', 2)->where('day', 29));
                }
            })
            ->notCongratulatedIn($today->year)
            ->get();

        if ($birthdays->isEmpty()) {
            $this->info('No birthdays today.');
            return self::SUCCESS;
        }

        foreach ($birthdays as $birthday) {
            $text = $greetings->buildMessage($birthday);

            if ($this->option('dry-run')) {
                $this->line("[dry-run] {$text}");
                continue;
            }

            try {
                Telegram::bot('birthday')->sendMessage([
                    'chat_id' => config('birthdays.chat_id'),
                    'text'    => $text,
                ]);

                $birthday->update(['last_congratulated_year' => $today->year]);

                Log::info('Birthday greeting sent', ['birthday_id' => $birthday->id]);
            } catch (\Throwable $e) {
                Log::error('Birthday greeting failed', [
                    'birthday_id' => $birthday->id,
                    'error'       => $e->getMessage(),
                ]);
                $this->error("Failed for {$birthday->name}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
