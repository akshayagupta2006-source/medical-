@extends('layouts.app', ['title' => 'Create Medical Case'])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create Medical Case</h1>
        <p class="page-subtitle">Register a new orthopedic or surgical case in the system</p>
    </div>
    <a href="{{ route('cases.index') }}" class="btn btn-default">&larr; Back to Cases</a>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Case Information</span>
    </div>
    <div class="panel-body">
        <form action="{{ route('cases.store') }}" method="POST">
            @include('cases._form', ['isEdit' => false])
        </form>
    </div>
</div>
@endsection
