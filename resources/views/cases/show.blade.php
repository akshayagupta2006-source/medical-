@extends('layouts.app', ['title' => 'Case ' . $case->case_number])

@section('content')
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <h1 class="page-title">{{ $case->case_number }}</h1>
            @php
                $statusSlug = strtolower(str_replace(' ', '-', $case->status));
            @endphp
            <span class="badge badge-{{ $statusSlug }}" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                {{ $case->status }}
            </span>
            <span class="badge badge-{{ strtolower($case->priority) }}" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                {{ $case->priority }} Priority
            </span>
        </div>
        <p class="page-subtitle">Registered on {{ $case->created_at->format('M d, Y \a\t H:i') }}</p>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('cases.index') }}" class="btn btn-secondary">&larr; Back to List</a>

        @can('update', $case)
            <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary" id="btn-edit-case">
                Edit Case
            </a>
        @endcan

        @can('delete', $case)
            <form action="{{ route('cases.destroy', $case) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this case?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" id="btn-delete-case">
                    Delete Case
                </button>
            </form>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Clinical & Surgical Details</h2>
    </div>
    <div class="card-body">
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">Case Number</span>
                <span class="detail-value" style="font-weight: 700; color: var(--primary);">{{ $case->case_number }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Patient Reference (Dummy ID)</span>
                <span class="detail-value">{{ $case->patient_reference }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Lead Surgeon</span>
                <span class="detail-value">{{ $case->surgeon_name }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Implant Type</span>
                <span class="detail-value">{{ $case->implant_type }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="badge badge-{{ $statusSlug }}">{{ $case->status }}</span>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Priority</span>
                <span class="detail-value">
                    <span class="badge badge-{{ strtolower($case->priority) }}">{{ $case->priority }}</span>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Scheduled Surgery Date</span>
                <span class="detail-value">
                    {{ $case->surgery_date ? $case->surgery_date->format('F d, Y') : 'Not scheduled yet' }}
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Created By</span>
                <span class="detail-value">
                    {{ $case->creator ? $case->creator->name : 'System' }}
                    @if ($case->creator)
                        <span class="role-badge {{ $case->creator->role }}" style="margin-left: 0.35rem;">
                            {{ $case->creator->role }}
                        </span>
                    @endif
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Record Created At</span>
                <span class="detail-value">{{ $case->created_at->format('Y-m-d H:i:s') }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Last Updated At</span>
                <span class="detail-value">{{ $case->updated_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
