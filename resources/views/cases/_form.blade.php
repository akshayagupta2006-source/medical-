@csrf
@if ($isEdit)
    @method('PUT')
@endif

<div class="form-grid">
    <!-- Case Number -->
    <div class="form-group">
        <label for="case_number" class="form-label">
            Case Number <span class="req">*</span>
        </label>
        <input
            type="text"
            name="case_number"
            id="case_number"
            class="form-control @error('case_number') has-error @enderror"
            value="{{ old('case_number', $case->case_number) }}"
            required
            placeholder="e.g. CAS-10045"
        >
        @error('case_number')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <!-- Patient Reference -->
    <div class="form-group">
        <label for="patient_reference" class="form-label">
            Patient Reference <span class="req">*</span>
            <span class="form-hint">(Dummy ID, e.g. PAT-1234)</span>
        </label>
        <input
            type="text"
            name="patient_reference"
            id="patient_reference"
            class="form-control @error('patient_reference') has-error @enderror"
            value="{{ old('patient_reference', $case->patient_reference) }}"
            required
            placeholder="e.g. PAT-8492"
        >
        @error('patient_reference')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <!-- Surgeon Name -->
    <div class="form-group">
        <label for="surgeon_name" class="form-label">
            Surgeon Name <span class="req">*</span>
        </label>
        <input
            type="text"
            name="surgeon_name"
            id="surgeon_name"
            class="form-control @error('surgeon_name') has-error @enderror"
            value="{{ old('surgeon_name', $case->surgeon_name) }}"
            required
            placeholder="e.g. Dr. Eleanor Vance"
        >
        @error('surgeon_name')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <!-- Implant Type -->
    <div class="form-group">
        <label for="implant_type" class="form-label">
            Implant Type <span class="req">*</span>
        </label>
        <input
            type="text"
            name="implant_type"
            id="implant_type"
            class="form-control @error('implant_type') has-error @enderror"
            value="{{ old('implant_type', $case->implant_type) }}"
            required
            placeholder="e.g. Titanium Hip Stem 12mm"
        >
        @error('implant_type')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <!-- Status -->
    <div class="form-group">
        <label for="status" class="form-label">
            Workflow Status <span class="req">*</span>
        </label>
        <select
            name="status"
            id="status"
            class="form-control @error('status') has-error @enderror"
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
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <!-- Priority -->
    <div class="form-group">
        <label for="priority" class="form-label">
            Priority <span class="req">*</span>
        </label>
        <select
            name="priority"
            id="priority"
            class="form-control @error('priority') has-error @enderror"
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
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <!-- Surgery Date -->
    <div class="form-group col-span-2">
        <label for="surgery_date" class="form-label">
            Surgery Date
            <span class="form-hint">(Optional scheduled date)</span>
        </label>
        <input
            type="date"
            name="surgery_date"
            id="surgery_date"
            class="form-control @error('surgery_date') has-error @enderror"
            value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}"
            style="max-width: 320px;"
        >
        @error('surgery_date')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a href="{{ $isEdit ? route('cases.show', $case) : route('cases.index') }}" class="btn btn-default">
        Cancel
    </a>
    <button type="submit" class="btn btn-primary" id="btn-save-case">
        {{ $isEdit ? 'Save Changes' : 'Create Case' }}
    </button>
</div>
