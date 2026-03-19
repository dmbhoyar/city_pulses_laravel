@extends('layouts.app')

@section('content')
<h2>New Job</h2>
@include('jobs._form', ['job' => $job, 'formAction' => route('jobs.store'), 'method' => 'POST'])
@endsection
