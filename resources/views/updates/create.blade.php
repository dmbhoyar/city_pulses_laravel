@extends('layouts.app')

@section('content')
<h2>New Update</h2>
@include('updates._form', ['update' => $update, 'formAction' => route('updates.store'), 'method' => 'POST'])
@endsection
