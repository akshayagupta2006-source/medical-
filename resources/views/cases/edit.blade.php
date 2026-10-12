@extends('layouts.app', ['title' => 'Edit Case: ' . $case->case_number])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Case: {{ $case->case_number }}</h1>
        <p class="page-subtitle">Update case details, priority, or clinical status</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('cases.show', $case) }}" class="btn btn-secondary">&larr; Back to Case Details</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Modify Case Information</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('cases.update', $case) }}" method="POST">
            @include('cases._form', ['isEdit' => true])
        </form>
    </div>
</div>
@endsection
