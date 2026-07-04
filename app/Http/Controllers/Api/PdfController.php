<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Measure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class PdfController extends Controller
{
    public function export(Request $request): JsonResponse
    {
        $days = (int) $request->query('days', 30);
        $days = in_array($days, [30, 90]) ? $days : 30;

        $user = $request->user();
        $from = now()->subDays($days);

        $measures = Measure::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $from)
            ->latest()
            ->get();

        $total = $measures->count();
        $avgSystolic  = $total > 0 ? round($measures->whereNotNull('systolic')->avg('systolic')) : '—';
        $avgDiastolic = $total > 0 ? round($measures->whereNotNull('diastolic')->avg('diastolic')) : '—';
        $avgPulse     = $total > 0 ? round($measures->whereNotNull('pulse')->avg('pulse')) : '—';

        $pdf = Pdf::loadView('pdf.measures', compact(
            'user', 'measures', 'days', 'total',
            'avgSystolic', 'avgDiastolic', 'avgPulse',
        ))->setPaper('a4');

        $pdfContent = $pdf->output();
        $filename = "health-report-{$days}d.pdf";

        // Відправляємо через бота
        Http::attach('document', $pdfContent, $filename)
            ->post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendDocument", [
                'chat_id' => $user->telegram_id,
                'caption' => "📊 Звіт за {$days} днів — {$total} замерів",
            ]);

        return response()->json([
            'message' => 'PDF надіслано в чат з ботом',
        ]);
    }
}