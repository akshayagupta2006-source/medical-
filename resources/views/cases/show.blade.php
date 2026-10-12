@extends('layouts.app', ['title' => 'Case ' . $case->case_number])

@section('content')
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.25rem;">
            <h1 class="page-title">{{ $case->case_number }}</h1>
            @php
                $statusSlug = strtolower(str_replace(' ', '-', $case->status));
            @endphp
            <span class="status-pill status-{{ $statusSlug }}">{{ $case->status }}</span>
            <span class="priority-tag priority-{{ strtolower($case->priority) }}">{{ $case->priority }} Priority</span>
        </div>
        <p class="page-subtitle">Patient Ref: {{ $case->patient_reference }} &bull; Recorded on {{ $case->created_at->format('M d, Y') }}</p>
    </div>

    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('cases.index') }}" class="btn btn-default">&larr; Back to Cases</a>

        @can('update', $case)
            <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary" id="btn-edit-case">
                Edit Case
            </a>
        @endcan

        @can('delete', $case)
            <form action="{{ route('cases.destroy', $case) }}" method="POST" onsubmit="return confirm('Permanently delete case {{ $case->case_number }}?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" id="btn-delete-case">
                    Delete Case
                </button>
            </form>
        @endcan
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Clinical & Surgical Details</span>
    </div>
    <div class="panel-body">
        <div class="detail-grid">
            <div class="detail-row">
                <span class="detail-label">Case Identifier</span>
                <span class="detail-value" style="font-weight: 700; color: var(--navy-primary);">
                    {{ $case->case_number }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Patient Reference (Dummy Code)</span>
                <span class="detail-value" style="font-family: monospace;">
                    {{ $case->patient_reference }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Lead Surgeon</span>
                <span class="detail-value">{{ $case->surgeon_name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Implant Specification</span>
                <span class="detail-value">{{ $case->implant_type }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Workflow Status</span>
                <span class="detail-value">
                    <span class="status-pill status-{{ $statusSlug }}">{{ $case->status }}</span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Priority Level</span>
                <span class="detail-value">
                    <span class="priority-tag priority-{{ strtolower($case->priority) }}">{{ $case->priority }}</span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Scheduled Surgery Date</span>
                <span class="detail-value">
                    {{ $case->surgery_date ? $case->surgery_date->format('F d, Y') : 'Not scheduled' }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Registered By</span>
                <span class="detail-value">
                    {{ $case->creator ? $case->creator->name : 'System' }}
                    @if ($case->creator)
                        <span class="role-tag {{ $case->creator->role }}" style="margin-left: 0.35rem;">
                            {{ $case->creator->role }}
                        </span>
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Created At</span>
                <span class="detail-value" style="font-size: 0.85rem; color: var(--text-secondary);">
                    {{ $case->created_at->format('Y-m-d H:i:s') }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Last Updated At</span>
                <span class="detail-value" style="font-size: 0.85rem; color: var(--text-secondary);">
                    {{ $case->updated_at->format('Y-m-d H:i:s') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
