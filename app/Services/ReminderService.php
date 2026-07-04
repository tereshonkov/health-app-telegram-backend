<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reminder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\DTOs\ReminderDTO;

class ReminderService
{
    public function getAllRemindersForUser(int $userId, int $limit = 10, int $page = 1): LengthAwarePaginator
    {
        return Reminder::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function createReminder(int $userId, array $data): Reminder
    {
        $dto = ReminderDTO::fromArray($data, $userId);
        return Reminder::create($dto->toArray());
    }

    public function deleteReminder(int $id): bool
    {
        return (bool) Reminder::findOrFail($id)->delete();
    }

    public function toggleReminder(Reminder $reminder): Reminder
    {
        $reminder->update(['enabled' => !$reminder->enabled]);
        return $reminder;
    }
}
