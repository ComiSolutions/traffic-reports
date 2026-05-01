<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TrafficReport;
use Illuminate\View\View;

class TrafficReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', [
            'reports' => auth()->user()
                ->trafficReports()
                ->latest()
                ->get(),
        ]);
    }

    public function show(TrafficReport $report): View
    {
        abort_unless($report->user_id === auth()->id() || auth()->user()?->isAdmin(), 403);

        $report->load(['reviewer', 'media']);

        return view('reports.show', compact('report'));
    }
}
