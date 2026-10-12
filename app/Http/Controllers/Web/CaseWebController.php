<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalCaseRequest;
use App\Http\Requests\UpdateMedicalCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CaseWebController extends Controller
{
    /**
     * Display a listing of cases with search, filter, and pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MedicalCase::class);

        $query = MedicalCase::with('creator:id,name,role')->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('surgeon_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), MedicalCase::STATUSES)) {
            $query->where('status', $request->input('status'));
        }

        $cases = $query->paginate(10)->withQueryString();

        return view('cases.index', [
            'cases' => $cases,
            'statuses' => MedicalCase::STATUSES,
            'selectedStatus' => $request->input('status', ''),
            'searchQuery' => $request->input('search', ''),
        ]);
    }

    /**
     * Show form for creating a new case.
     */
    public function create(): View
    {
        Gate::authorize('create', MedicalCase::class);

        return view('cases.create', [
            'case' => new MedicalCase(),
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    /**
     * Store newly created case.
     */
    public function store(StoreMedicalCaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);

        return redirect()->route('cases.show', $case)
            ->with('status', "Case {$case->case_number} was successfully created.");
    }

    /**
     * Display the specified case details.
     */
    public function show(MedicalCase $case): View
    {
        Gate::authorize('view', $case);

        return view('cases.show', [
            'case' => $case->load('creator:id,name,email,role'),
        ]);
    }

    /**
     * Show form for editing the case.
     */
    public function edit(MedicalCase $case): View
    {
        Gate::authorize('update', $case);

        return view('cases.edit', [
            'case' => $case,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    /**
     * Update the specified case.
     */
    public function update(UpdateMedicalCaseRequest $request, MedicalCase $case): RedirectResponse
    {
        Gate::authorize('update', $case);

        $case->update($request->validated());

        return redirect()->route('cases.show', $case)
            ->with('status', "Case {$case->case_number} was successfully updated.");
    }

    /**
     * Remove the specified case.
     */
    public function destroy(MedicalCase $case): RedirectResponse
    {
        Gate::authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return redirect()->route('cases.index')
            ->with('status', "Case {$caseNumber} was successfully deleted.");
    }
}
