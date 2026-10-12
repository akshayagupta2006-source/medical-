@extends('layouts.app', ['title' => 'Cases List'])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Medical Cases Registry</h1>
        <p class="page-subtitle">Primary register of patient case files, surgical plans, and implant requisitions</p>
    </div>

    @can('create', App\Models\MedicalCase::class)
        <a href="{{ route('cases.create') }}" class="btn btn-teal" id="btn-create-case-list">
            + New Case
        </a>
    @endcan
</div>

<!-- Search & Filter Toolbar -->
<div class="toolbar">
    <form action="{{ route('cases.index') }}" method="GET" class="toolbar-form">
        <div style="flex: 1; min-width: 220px;">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search case # or surgeon..."
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

        <button type="submit" class="btn btn-primary" id="btn-filter">
            Filter
        </button>

        @if ($searchQuery || $selectedStatus)
            <a href="{{ route('cases.index') }}" class="btn btn-default">
                Clear
            </a>
        @endif
    </form>
</div>

<!-- Cases Table (Main Focus) -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 120px;">Case #</th>
                <th>Patient Ref</th>
                <th>Surgeon</th>
                <th>Implant Type</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Surgery Date</th>
                <th>Created By</th>
                <th style="text-align: right; width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cases as $case)
                <tr>
                    <td style="font-weight: 600;">
                        <a href="{{ route('cases.show', $case) }}" style="color: var(--navy-primary); text-decoration: none;">
                            {{ $case->case_number }}
                        </a>
                    </td>
                    <td>
                        <span style="font-family: monospace; font-size: 0.85rem; color: var(--text-secondary);">
                            {{ $case->patient_reference }}
                        </span>
                    </td>
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
                    <td>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">
                            {{ $case->creator ? $case->creator->name : 'System' }}
                        </span>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="{{ route('cases.show', $case) }}" class="btn btn-default btn-sm">
                            View
                        </a>

                        @can('update', $case)
                            <a href="{{ route('cases.edit', $case) }}" class="btn btn-default btn-sm" style="color: var(--navy-primary);">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $case)
                            <form action="{{ route('cases.destroy', $case) }}" method="POST" onsubmit="return confirm('Delete case {{ $case->case_number }}?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No medical cases found matching your search criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container">
    <div style="color: var(--text-muted); font-size: 0.8rem;">
        Showing {{ $cases->firstItem() ?? 0 }} to {{ $cases->lastItem() ?? 0 }} of {{ $cases->total() }} cases
    </div>
    <div>
        {{ $cases->links() }}
    </div>
</div>
@endsection
