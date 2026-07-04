<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Measure;
use App\DTOs\MeasureDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class MeasureService
{
    public function getAllMeasures(int $userId, int $limit = 10, int $page = 1): LengthAwarePaginator
    {
        return Measure::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function createMeasure(int $userId, array $data): Measure
    {
        $dto = MeasureDTO::fromArray($data, $userId);
        return Measure::create($dto->toArray());
    }

    public function deleteMeasure(int $id): bool
    {
        $deleted = (bool) Measure::findOrFail($id)->delete();
        return $deleted;
    }

    public function deleteAllMeasuresForUser(int $userId): int
    {
        return Measure::query()
            ->where('user_id', $userId)
            ->delete();
    }
}
