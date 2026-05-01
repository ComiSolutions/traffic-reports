<?php

namespace App\Livewire\Admin;

use App\Enums\ReportStatus;
use App\Models\TrafficReport;
use App\Notifications\ReportStatusUpdated;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    public string $status = 'all';

    public function approve(int $reportId): void
    {
        $this->updateStatus($reportId, ReportStatus::Approved);
    }

    public function reject(int $reportId): void
    {
        $this->updateStatus($reportId, ReportStatus::Rejected);
    }

    private function updateStatus(int $reportId, ReportStatus $status): void
    {
        $report = TrafficReport::query()
            ->with('reporter')
            ->findOrFail($reportId);

        $report->forceFill([
            'status' => $status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        $report->reporter?->notify(new ReportStatusUpdated($report));

        session()->flash('status', __('Report status updated.'));
    }

    public function render(): View
    {
        $statusValues = collect(ReportStatus::cases())->map->value;
        $selectedStatus = $statusValues->contains($this->status) ? $this->status : 'all';

        $reports = TrafficReport::query()
            ->with('reporter')
            ->when($selectedStatus !== 'all', fn ($query) => $query->where('status', $selectedStatus))
            ->latest()
            ->get();

        $statusCounts = TrafficReport::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('livewire.admin.dashboard', [
            'reports' => $reports,
            'statuses' => ReportStatus::cases(),
            'statusCounts' => $statusCounts,
        ]);
    }
}
