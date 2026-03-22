@extends('layouts.app')

@section('content')
<div class="panel" style="max-width:980px;margin:0 auto;">
	<div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px">
		<h2 style="margin:0">Add Job Listing</h2>
		<a href="{{ route('jobs.index') }}" class="button">← Back to Jobs</a>
	</div>
	@include('jobs._form', ['job' => $job, 'cities' => $cities, 'formAction' => route('jobs.store'), 'method' => 'POST'])
</div>
@endsection
