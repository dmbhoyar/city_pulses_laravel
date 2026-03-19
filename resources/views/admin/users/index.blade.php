@extends('layouts.app')

@section('content')
<div class="panel">
  <h1>Users</h1>
  <table style="width:100%;border-collapse:collapse">
    <tr><th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Email</th><th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Role</th><th style="text-align:left;padding:8px;border-bottom:1px solid #ddd">Actions</th></tr>
    @foreach($users as $u)
      <tr>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $u->email }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">{{ $u->role }}</td>
        <td style="padding:8px;border-bottom:1px solid #eee">
          <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Remove user?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="button danger">Remove</button>
          </form>
        </td>
      </tr>
    @endforeach
  </table>
</div>
@endsection
