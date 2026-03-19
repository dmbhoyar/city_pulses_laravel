@extends('layouts.app')

@section('content')
<h2>Edit Job</h2>
@include('jobs._form', ['job' => $job, 'formAction' => route('jobs.update', $job->id), 'method' => 'PATCH'])
@endsection
