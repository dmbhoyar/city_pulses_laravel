@extends('layouts.app')

@section('content')
<h2>Edit Update</h2>
@include('updates._form', ['update' => $update, 'formAction' => route('updates.update', $update->id), 'method' => 'PATCH'])
@endsection
