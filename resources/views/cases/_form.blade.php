@csrf
@if ($isEdit)
    @method('PUT')
@endif

<div class="form-grid">
    <!-- Case Number -->
    <div class="form-group">
        <label for="case_number" class="form-label">
            Case Number <span style="color: var(--danger);">*</span>
        </label>
        <input
            type="text"
            name="case_number"
            id="case_number"
            class="form-control @error('case_number') is-invalid @enderror"
            value="{{ old('case_number', $case->case_number) }}"
            required
            placeholder="e.g. CAS-10023"
        >
        @error('case_number')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <!-- Patient Reference -->
    <div class="form-group">
        <label for="patient_reference" class="form-label">
            Patient Reference <span style="color: var(--danger);">*</span>
            <small style="color: var(--text-muted); font-weight: normal;">(Dummy ID only)</small>
        </label>
        <input
            type="text"
            name="patient_reference"
            id="patient_reference"
            class="form-control @error('patient_reference') is-invalid @enderror"
            value="{{ old('patient_reference', $case->patient_reference) }}"
            required
            placeholder="e.g. PAT-4821"
        >
        @error('patient_reference')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <!-- Surgeon Name -->
    <div class="form-group">
        <label for="surgeon_name" class="form-label">
            Surgeon Name <span style="color: var(--danger);">*</span>
        </label>
        <input
            type="text"
            name="surgeon_name"
            id="surgeon_name"
            class="form-control @error('surgeon_name') is-invalid @enderror"
            value="{{ old('surgeon_name', $case->surgeon_name) }}"
            required
            placeholder="e.g. Dr. Eleanor Vance"
        >
        @error('surgeon_name')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <!-- Implant Type -->
    <div class="form-group">
        <label for="implant_type" class="form-label">
            Implant Type <span style="color: var(--danger);">*</span>
        </label>
        <input
            type="text"
            name="implant_type"
            id="implant_type"
            class="form-control @error('implant_type') is-invalid @enderror"
            value="{{ old('implant_type', $case->implant_type) }}"
            required
            placeholder="e.g. Titanium Hip Stem 12mm"
        >
        @error('implant_type')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <!-- Status -->
    <div class="form-group">
        <label for="status" class="form-label">
            Status <span style="color: var(--danger);">*</span>
        </label>
        <select
            name="status"
            id="status"
            class="form-control @error('status') is-invalid @enderror"
            required
        >
            @foreach ($statuses as $statusOption)
                <option
                    value="{{ $statusOption }}"
                    {{ old('status', $case->status ?? 'Draft') === $statusOption ? 'selected' : '' }}
                >
                    {{ $statusOption }}
                </option>
            @endforeach
        </select>
        @error('status')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <!-- Priority -->
    <div class="form-group">
        <label for="priority" class="form-label">
            Priority <span style="color: var(--danger);">*</span>
        </label>
        <select
            name="priority"
            id="priority"
            class="form-control @error('priority') is-invalid @enderror"
            required
        >
            @foreach ($priorities as $priorityOption)
                <option
                    value="{{ $priorityOption }}"
                    {{ old('priority', $case->priority ?? 'Medium') === $priorityOption ? 'selected' : '' }}
                >
                    {{ $priorityOption }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <!-- Surgery Date -->
    <div class="form-group full-width">
        <label for="surgery_date" class="form-label">
            Surgery Date <small style="color: var(--text-muted); font-weight: normal;">(Optional)</small>
        </label>
        <input
            type="date"
            name="surgery_date"
            id="surgery_date"
            class="form-control @error('surgery_date') is-invalid @enderror"
            value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}"
        >
        @error('surgery_date')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div style="margin-top: 1.75rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
    <a href="{{ $isEdit ? route('cases.show', $case) : route('cases.index') }}" class="btn btn-secondary">
        Cancel
    </a>
    <button type="submit" class="btn btn-primary" id="btn-save-case">
        {{ $isEdit ? 'Update Case' : 'Create Case' }}
    </button>
</div>
