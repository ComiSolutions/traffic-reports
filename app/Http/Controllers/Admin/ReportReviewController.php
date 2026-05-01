<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReportStatusRequest;
use App\Models\TrafficReport;
use App\Notifications\ReportStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index', [
            'reports' => TrafficReport::query()
                ->with('reporter')
                ->latest()
                ->get(),
        ]);
    }

    public function show(TrafficReport $report): View
    {
        $report->load(['reporter', 'reviewer', 'media']);

        return view('admin.reports.show', compact('report'));
    }

    public function approve(UpdateReportStatusRequest $request, TrafficReport $report): RedirectResponse
    {
        return $this->updateStatus($report, ReportStatus::Approved, $request->validated('review_note'));
    }

    public function reject(UpdateReportStatusRequest $request, TrafficReport $report): RedirectResponse
    {
        return $this->updateStatus($report, ReportStatus::Rejected, $request->validated('review_note'));
    }

    private function updateStatus(TrafficReport $report, ReportStatus $status, ?string $reviewNote = null): RedirectResponse
    {
        $report->forceFill([
            'status' => $status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $reviewNote,
        ])->save();

        $report->load('reporter');
        $report->reporter?->notify(new ReportStatusUpdated($report));

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('status', __('Report status updated.'));
    }
}
