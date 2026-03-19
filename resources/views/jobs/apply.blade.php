@extends('layouts.app')

@section('content')
<h2>Apply for: {{ $job->title }}</h2>

<p>If this job redirects externally you'll be sent there automatically. Otherwise use the form below.</p>

<form action="{{ route('jobs.submitApplication', $job->id) }}" method="POST">
  @csrf
  <div class="field">
    <label for="applicant_name">Your name</label>
    <input type="text" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}">
  </div>
  <div class="field">
    <label for="applicant_email">Your email</label>
    <input type="email" id="applicant_email" name="applicant_email" value="{{ old('applicant_email') }}">
  </div>
  <div class="field">
    <label for="applicant_phone">Phone</label>
    <input type="tel" id="applicant_phone" name="applicant_phone" value="{{ old('applicant_phone') }}">
  </div>
  <div class="field">
    <label for="message">Message / cover letter</label>
    <textarea id="message" name="message" rows="6">{{ old('message') }}</textarea>
  </div>
  <div class="field">
    <label for="resume_url">Resume URL (optional)</label>
    <input type="text" id="resume_url" name="resume_url" value="{{ old('resume_url') }}">
  </div>
  <div class="actions">
    <button type="submit" class="toggle-btn">Submit Application</button>
  </div>
</form>
@endsection
