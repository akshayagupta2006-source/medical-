@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Case Management Dashboard</h1>
        <p class="page-subtitle">Summary of current clinical cases and active surgical planning</p>
    </div>

    @can('create', App\Models\MedicalCase::class)
        <a href="{{ route('cases.create') }}" class="btn btn-teal" id="btn-create-case">
            + New Medical Case
        </a>
    @endcan
</div>

<!-- Compact Case Summary Row -->
<div class="stats-summary">
    <div class="stat-box">
        <div class="stat-box-title">Total Cases</div>
        <div class="stat-box-count" id="stat-total">{{ $totalCases }}</div>
    </div>

    <div class="stat-box planning">
        <div class="stat-box-title">Planning</div>
        <div class="stat-box-count" id="stat-planning">{{ $planningCases }}</div>
    </div>

    <div class="stat-box in-review">
        <div class="stat-box-title">In Review</div>
        <div class="stat-box-count" id="stat-in-review">{{ $inReviewCases }}</div>
    </div>

    <div class="stat-box approved">
        <div class="stat-box-title">Approved</div>
        <div class="stat-box-count" id="stat-approved">{{ $approvedCases }}</div>
    </div>

    <div class="stat-box completed">
        <div class="stat-box-title">Completed</div>
        <div class="stat-box-count" id="stat-completed">{{ $completedCases }}</div>
    </div>
</div>

<!-- Recent Cases Panel -->
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Recent Cases (Latest 6)</span>
        <a href="{{ route('cases.index') }}" class="btn btn-default btn-sm">View All Cases &rarr;</a>
    </div>

    <div class="table-wrapper" style="border: none;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Case #</th>
                    <th>Patient Ref</th>
                    <th>Lead Surgeon</th>
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
                            <a href="{{ route('cases.show', $case) }}" style="color: var(--navy-primary); text-decoration: none;">
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
                            <span class="status-pill status-{{ $statusSlug }}">{{ $case->status }}</span>
                        </td>
                        <td>
                            <span class="priority-tag priority-{{ strtolower($case->priority) }}">{{ $case->priority }}</span>
                        </td>
                        <td>{{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('cases.show', $case) }}" class="btn btn-default btn-sm">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">
                            No medical cases recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
