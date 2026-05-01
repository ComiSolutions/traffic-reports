<?php

namespace App\Livewire\User;

use App\Enums\ReportStatus;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render(): View
    {
        $reports = auth()->user()
            ->trafficReports()
            ->with('media')
            ->latest()
            ->get();

        return view('livewire.user.dashboard', [
            'reports' => $reports,
            'reportCounts' => [
                'total' => $reports->count(),
                'pending' => $reports->where('status', ReportStatus::Pending)->count(),
                'approved' => $reports->where('status', ReportStatus::Approved)->count(),
                'rejected' => $reports->where('status', ReportStatus::Rejected)->count(),
            ],
        ]);
    }
}
