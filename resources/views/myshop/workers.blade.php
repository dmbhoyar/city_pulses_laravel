@extends('layouts.app')

@section('content')
<div class="myshop-workers">
  <h2>Shop Workers</h2>

  <h3>Add Worker</h3>
  <form action="{{ route('create_worker_myshop') }}" method="POST">
    @csrf
    <div>
      <label>First name</label><br />
      <input type="text" name="worker[first_name]" required>
    </div>
    <div>
      <label>Last name</label><br />
      <input type="text" name="worker[last_name]">
    </div>
    <div>
      <label>Email</label><br />
      <input type="email" name="worker[email]" required>
    </div>
    <div>
      <label>Mobile</label><br />
      <input type="tel" name="worker[mobile_number]">
    </div>
    <p>
      <button type="submit" class="button primary">Create worker</button>
      <a href="{{ route('myshop') }}" class="button">Back</a>
    </p>
  </form>

  <h3>Existing Workers</h3>
  @if(isset($workers) && count($workers))
    <table class="workers-table" style="width:100%;border-collapse:collapse">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Role</th>
          <th>Experience</th>
          <th>Tags</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($workers as $w)
          <tr>
            <td>{{ $w->full_name }}</td>
            <td><a href="mailto:{{ $w->email }}">{{ $w->email }}</a></td>
            <td>{{ $w->mobile_number }}</td>
            <td>{{ $w->role }}</td>
            <td>{{ truncate_text($w->experience ?? '', 80) }}</td>
            <td>{{ $w->tags }}</td>
            <td>
              <form action="{{ route('update_worker_myshop') }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="worker[id]" value="{{ $w->id }}">
                <div style="margin-bottom:6px">
                  <label>Experience</label><br />
                  <textarea name="worker[experience]" rows="2" style="width:100%">{{ $w->experience }}</textarea>
                </div>
                <div style="margin-bottom:6px">
                  <label>Tags (comma separated)</label><br />
                  <input type="text" name="worker[tags]" value="{{ $w->tags }}" style="width:100%">
                </div>
                <div>
                  <button type="submit" class="button">Save</button>
                  <a href="{{ route('worker_experience_myshop', ['id' => $w->id]) }}" class="button" target="_blank">Experience Letter</a>
                </div>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p>No workers added yet.</p>
  @endif
</div>
@endsection
