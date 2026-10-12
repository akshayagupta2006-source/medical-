<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MedicalCase;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show dashboard with case metrics.
     */
    public function index(): View
    {
        $totalCases = MedicalCase::count();
        $planningCases = MedicalCase::where('status', MedicalCase::STATUS_PLANNING)->count();
        $inReviewCases = MedicalCase::where('status', MedicalCase::STATUS_IN_REVIEW)->count();
        $approvedCases = MedicalCase::where('status', MedicalCase::STATUS_APPROVED)->count();
        $completedCases = MedicalCase::where('status', MedicalCase::STATUS_COMPLETED)->count();
        $draftCases = MedicalCase::where('status', MedicalCase::STATUS_DRAFT)->count();
        $cancelledCases = MedicalCase::where('status', MedicalCase::STATUS_CANCELLED)->count();

        $recentCases = MedicalCase::with('creator:id,name')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalCases',
            'planningCases',
            'inReviewCases',
            'approvedCases',
            'completedCases',
            'draftCases',
            'cancelledCases',
            'recentCases'
        ));
    }
}
