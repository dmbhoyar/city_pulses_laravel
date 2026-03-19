@extends('layouts.app')

@section('content')
<h2>New Service Record</h2>
@include('services._form', ['service' => $service, 'formAction' => route('services.store'), 'method' => 'POST'])
@endsection
