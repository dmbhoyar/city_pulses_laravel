@extends('layouts.app')

@section('content')
<h2>Edit Service Record</h2>
@include('services._form', ['service' => $service, 'formAction' => route('services.update', $service->id), 'method' => 'PATCH'])
@endsection
