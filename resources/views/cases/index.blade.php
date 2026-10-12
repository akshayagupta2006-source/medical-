@extends('layouts.app', ['title' => 'Medical Cases'])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Medical Cases</h1>
        <p class="page-subtitle">Manage surgical cases, implant requisitions, and case statuses</p>
    </div>

    @can('create', App\Models\MedicalCase::class)
        <a href="{{ route('cases.create') }}" class="btn btn-primary" id="btn-create-case-list">
            <span>+</span>
            <span>New Case</span>
        </a>
    @endcan
</div>

<!-- Search & Status Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('cases.index') }}" method="GET" class="filter-form">
        <div style="flex: 1; min-width: 220px;">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search by Case # or Surgeon..."
                value="{{ $searchQuery }}"
                id="search-input"
            >
        </div>

        <div style="min-width: 170px;">
            <select name="status" class="form-control" id="status-filter">
                <option value="">All Statuses</option>
                @foreach ($statuses as $statusOption)
                    <option
                        value="{{ $statusOption }}"
                        {{ $selectedStatus === $statusOption ? 'selected' : '' }}
                    >
                        {{ $statusOption }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-secondary" id="btn-filter">
            Filter
        </button>

        @if ($searchQuery || $selectedStatus)
            <a href="{{ route('cases.index') }}" class="btn btn-secondary" style="color: var(--text-muted);">
                Reset
            </a>
        @endif
    </form>
</div>

<!-- Cases Table -->
<div class="card">
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
                    <th>Created By</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cases as $case)
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
                        <td>
                            <small>{{ $case->creator ? $case->creator->name : 'System' }}</small>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('cases.show', $case) }}" class="btn btn-secondary btn-sm">
                                View
                            </a>

                            @can('update', $case)
                                <a href="{{ route('cases.edit', $case) }}" class="btn btn-secondary btn-sm" style="color: var(--primary);">
                                    Edit
                                </a>
                            @endcan

                            @can('delete', $case)
                                <form action="{{ route('cases.destroy', $case) }}" method="POST" onsubmit="return confirm('Delete this case?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger);">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                            No medical cases matched your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Links -->
<div class="pagination-wrapper">
    {{ $cases->links() }}
</div>
@endsection
