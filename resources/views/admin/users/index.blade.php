@extends('layouts.app')

@section('content')
@include('admin.partials.ui')
<div class="panel admin-wrap">
  <div class="admin-head">
    <h1>Users Management</h1>
    <a href="{{ route('admin.dashboard') }}" class="button">Back to Admin</a>
  </div>

  <div class="admin-card">
    <h3>Add New User</h3>
    <form action="{{ route('admin.users.store') }}" method="POST">
      @csrf
      <div class="admin-form-grid">
        <div><label>First Name</label><input type="text" name="first_name" required></div>
        <div><label>Last Name</label><input type="text" name="last_name"></div>
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Mobile</label><input type="text" name="mobile_number"></div>
        <div>
          <label>Role</label>
          <select name="role" required>
            <option value="user">user</option>
            <option value="shopowner">shopowner</option>
            <option value="service_provider">service_provider</option>
            <option value="shopworker">shopworker</option>
            <option value="superadmin">superadmin</option>
            <option value="admin">admin</option>
          </select>
        </div>
        <div><label>Password</label><input type="password" name="password" required></div>
      </div>
      <div class="admin-actions"><button type="submit" class="button">Create User</button></div>
    </form>
  </div>

  <div class="admin-card">
    <div class="admin-toolbar">
      <h3 style="margin:0">All Users</h3>
      <span class="admin-muted">Manage profile, role and password from one place.</span>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Contact</th>
            <th>Role</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td>
                <div><strong>{{ $u->full_name }}</strong></div>
                <div class="admin-muted">ID #{{ $u->id }}</div>
              </td>
              <td>
                <div>{{ $u->email }}</div>
                <div class="admin-muted">{{ $u->mobile_number ?: '—' }}</div>
              </td>
              <td><span class="admin-tag">{{ $u->role }}</span></td>
              <td>{{ optional($u->created_at)->format('d M Y') }}</td>
              <td>
                <form action="{{ route('admin.users.update', $u) }}" method="POST" class="admin-inline" id="user-update-{{ $u->id }}">
                  @csrf
                  @method('PATCH')
                  <div class="row">
                    <input type="text" name="first_name" value="{{ $u->first_name }}" required>
                    <input type="text" name="last_name" value="{{ $u->last_name }}">
                    <input type="email" name="email" value="{{ $u->email }}" required>
                  </div>
                  <div class="row2">
                    <input type="text" name="mobile_number" value="{{ $u->mobile_number }}" placeholder="Mobile">
                    <select name="role" required>
                      @foreach(['user','shopowner','service_provider','shopworker','superadmin','admin'] as $role)
                        <option value="{{ $role }}" {{ $u->role === $role ? 'selected' : '' }}>{{ $role }}</option>
                      @endforeach
                    </select>
                  </div>
                  <input type="password" name="password" placeholder="New password (optional)">
                  <div class="admin-actions">
                    <button type="submit" class="button">Update</button>
                  </div>
                </form>
                <div class="admin-actions">
                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="button danger">Delete</button>
                    </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5">No users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="margin-top:10px">{{ $users->links() }}</div>
  </div>
</div>
@endsection
