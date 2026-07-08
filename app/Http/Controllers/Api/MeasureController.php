<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeasureRequest;
use Illuminate\Http\Request;
use App\Services\MeasureService;
use App\Models\Measure;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use App\Http\Resources\MeasureResource;

class MeasureController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly MeasureService $measureService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Measure::class);
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $days = $request->query('days') ? (int) $request->query('days') : null;

        $paginator = $this->measureService->getAllMeasures((int)$request->user()->id, (int)$limit, (int)$page, $days);

        $resourceCollection = MeasureResource::collection($paginator);

        return new JsonResponse(
            data: $resourceCollection->response()->getData(true),
            status: ResponseAlias::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMeasureRequest $request): JsonResponse
    {
        $measure = $this->measureService->createMeasure(
            $request->user()->id,
            $request->validated()
        );

        return new JsonResponse(
            data: ['data' => new MeasureResource($measure)],
            status: ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // not implemented
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // not implemented
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Measure $measure): JsonResponse
    {
        $this->authorize('delete', $measure);

        $this->measureService->deleteMeasure($measure->id);

        return new JsonResponse(data: ['message' => 'Measure deleted successfully.'], status: ResponseAlias::HTTP_OK, json: false);
    }

    /**
     * Remove all resources for the authenticated user.
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $this->authorize('deleteAll', Measure::class);

        $userId = (int) $request->user()->id;

        $deletedCount = $this->measureService->deleteAllMeasuresForUser($userId);

        return new JsonResponse(data: ['message' => "Deleted {$deletedCount} measures for user {$userId}."], status: ResponseAlias::HTTP_OK, json: false);
    }
}
