@extends('layouts.app', ['title' => 'Create Medical Case'])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create Medical Case</h1>
        <p class="page-subtitle">Register a new orthopedic or surgical case</p>
    </div>
    <a href="{{ route('cases.index') }}" class="btn btn-secondary">&larr; Back to Cases</a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Case Information</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('cases.store') }}" method="POST">
            @include('cases._form', ['isEdit' => false])
        </form>
    </div>
</div>
@endsection
