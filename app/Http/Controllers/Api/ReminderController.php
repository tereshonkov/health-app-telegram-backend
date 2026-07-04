<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReminderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use App\Models\Reminder;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Resources\ReminderResource;

class ReminderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly ReminderService $reminderService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Reminder::class);
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);

        $paginator = $this->reminderService->getAllRemindersForUser((int)$request->user()->id, (int)$limit, (int)$page);

        $resourceCollection = ReminderResource::collection($paginator);

        return new JsonResponse(
            data: $resourceCollection->response()->getData(true),
            status: ResponseAlias::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReminderRequest $request): JsonResponse
    {
        $reminder = $this->reminderService->createReminder(
            $request->user()->id,
            $request->validated()
        );

        return new JsonResponse(
            data: ['data' => new ReminderResource($reminder)],
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
    public function destroy(Reminder $reminder): JsonResponse
    {
        $this->authorize('delete', $reminder);

        $this->reminderService->deleteReminder($reminder->id);

        return new JsonResponse(
            data: ['message' => 'Reminder deleted successfully.'],
            status: ResponseAlias::HTTP_OK
        );
    }

    /**
     * Toggle the specified resource in storage.
     */
    public function toggle(Reminder $reminder): JsonResponse
    {
        $this->authorize('toggle', $reminder);

        $updated = $this->reminderService->toggleReminder($reminder);

        return new JsonResponse(
            data: ['data' => new ReminderResource($updated)],
            status: ResponseAlias::HTTP_OK
        );
    }
}
