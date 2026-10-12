@extends('layouts.app', ['title' => 'Edit Case ' . $case->case_number])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Case: {{ $case->case_number }}</h1>
        <p class="page-subtitle">Update clinical information, workflow status, or priority</p>
    </div>
    <a href="{{ route('cases.show', $case) }}" class="btn btn-default">&larr; Back to Case Details</a>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Update Case Information</span>
    </div>
    <div class="panel-body">
        <form action="{{ route('cases.update', $case) }}" method="POST">
            @include('cases._form', ['isEdit' => true])
        </form>
    </div>
</div>
@endsection
