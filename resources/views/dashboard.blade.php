@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Case Management Dashboard</h1>
        <p class="page-subtitle">Overview of surgical planning and case workflow metrics</p>
    </div>

    @can('create', App\Models\MedicalCase::class)
        <a href="{{ route('cases.create') }}" class="btn btn-primary" id="btn-create-case">
            <span>+</span>
            <span>Create New Case</span>
        </a>
    @endcan
</div>

<!-- Case Statistics Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Total Cases</span>
        <span class="stat-value" id="stat-total">{{ $totalCases }}</span>
    </div>

    <div class="stat-card planning">
        <span class="stat-label">Planning</span>
        <span class="stat-value" id="stat-planning">{{ $planningCases }}</span>
    </div>

    <div class="stat-card in-review">
        <span class="stat-label">In Review</span>
        <span class="stat-value" id="stat-in-review">{{ $inReviewCases }}</span>
    </div>

    <div class="stat-card approved">
        <span class="stat-label">Approved</span>
        <span class="stat-value" id="stat-approved">{{ $approvedCases }}</span>
    </div>

    <div class="stat-card completed">
        <span class="stat-label">Completed</span>
        <span class="stat-value" id="stat-completed">{{ $completedCases }}</span>
    </div>
</div>

<!-- Recent Cases Card -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Cases</h2>
        <a href="{{ route('cases.index') }}" class="btn btn-secondary btn-sm">View All Cases &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Case #</th>
                    <th>Patient Ref</th>
                    <th>Surgeon</th>
                    <th>Implant Type</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Surgery Date</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentCases as $case)
                    <tr>
                        <td style="font-weight: 600;">
                            <a href="{{ route('cases.show', $case) }}" style="color: var(--primary); text-decoration: none;">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td>{{ $case->patient_reference }}</td>
                        <td>{{ $case->surgeon_name }}</td>
                        <td>{{ $case->implant_type }}</td>
                        <td>
                            @php
                                $statusSlug = strtolower(str_replace(' ', '-', $case->status));
                            @endphp
                            <span class="badge badge-{{ $statusSlug }}">{{ $case->status }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ strtolower($case->priority) }}">{{ $case->priority }}</span>
                        </td>
                        <td>{{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('cases.show', $case) }}" class="btn btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No medical cases recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
