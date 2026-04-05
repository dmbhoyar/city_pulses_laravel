@extends('layouts.app')

@section('content')
@include('admin.partials.ui')
<div class="panel admin-wrap">
  <div class="admin-head">
    <h1>Jobs Management</h1>
    <a href="{{ route('admin.dashboard') }}" class="button">← Back to Admin</a>
  </div>

  @if(session('notice'))
    <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:10px 14px;color:#2e7d32;font-size:13px;">
      ✓ {{ session('notice') }}
    </div>
  @endif

  {{-- Add Job Form --}}
  <div class="admin-card">
    <h3>Add New Job</h3>
    <form action="{{ route('admin.jobs.store') }}" method="POST">
      @csrf
      <div class="admin-form-grid">
        <div><label>Title *</label><input type="text" name="title" required placeholder="Job title"></div>
        <div><label>Company</label><input type="text" name="company" placeholder="Company name"></div>
        <div><label>Category</label><input type="text" name="category" placeholder="e.g. IT, Government"></div>
        <div><label>Location</label><input type="text" name="location" placeholder="City or area"></div>
        <div>
          <label>City</label>
          <select name="city_id">
            <option value="">— None —</option>
            @foreach($cities as $city)
              <option value="{{ $city->id }}">{{ $city->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label>Assign to User</label>
          <select name="user_id">
            <option value="">— None —</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->email }}</option>
            @endforeach
          </select>
        </div>
        <div><label>External URL</label><input type="url" name="external_url" placeholder="https://..."></div>
        <div><label>Description</label><textarea name="description" placeholder="Job description..."></textarea></div>
      </div>
      <div class="admin-actions"><button type="submit" class="button">Create Job</button></div>
    </form>
  </div>

  {{-- Search + List --}}
  <div class="admin-card">
    <div class="admin-toolbar">
      <h3 style="margin:0">All Jobs <span class="admin-muted">({{ $jobs->total() }})</span></h3>
      <form method="GET" action="{{ route('admin.jobs.index') }}" class="admin-search">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search title, company, category…">
        <button type="submit" class="button">Search</button>
        @if($q)
          <a href="{{ route('admin.jobs.index') }}" class="button">Clear</a>
        @endif
      </form>
    </div>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Title / Company</th>
            <th>Category</th>
            <th>City</th>
            <th>User</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($jobs as $job)
            <tr>
              <td class="admin-muted">{{ $job->id }}</td>
              <td>
                <strong>{{ $job->title }}</strong>
                @if($job->company)
                  <div class="admin-muted">{{ $job->company }}</div>
                @endif
                @if($job->location)
                  <div class="admin-muted">📍 {{ $job->location }}</div>
                @endif
                @if($job->external_url)
                  <div><a href="{{ $job->external_url }}" target="_blank" style="font-size:11px;color:#2563eb">🔗 Link</a></div>
                @endif
              </td>
              <td>
                @if($job->category)
                  <span class="admin-tag">{{ $job->category }}</span>
                @else
                  <span class="admin-muted">—</span>
                @endif
              </td>
              <td>{{ $job->city->name ?? '—' }}</td>
              <td class="admin-muted">{{ $job->user->email ?? '—' }}</td>
              <td class="admin-muted" style="white-space:nowrap">{{ $job->created_at->format('d M Y') }}</td>
              <td>
                {{-- Inline Edit --}}
                <button type="button" class="button" style="font-size:11px;padding:4px 8px"
                  onclick="document.getElementById('edit-job-{{ $job->id }}').style.display=document.getElementById('edit-job-{{ $job->id }}').style.display==='none'?'':'none'">
                  Edit
                </button>
                <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" style="display:inline"
                      onsubmit="return confirm('Delete this job?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="button" style="font-size:11px;padding:4px 8px;background:#fee2e2;color:#b91c1c;border-color:#fca5a5">Delete</button>
                </form>

                {{-- Edit form row --}}
                <tr id="edit-job-{{ $job->id }}" style="display:none">
                  <td colspan="7" style="background:#f0f7ff;padding:12px">
                    <form action="{{ route('admin.jobs.update', $job) }}" method="POST">
                      @csrf @method('PATCH')
                      <div class="admin-inline">
                        <div class="row">
                          <div><label>Title *</label><input type="text" name="title" value="{{ $job->title }}" required></div>
                          <div><label>Company</label><input type="text" name="company" value="{{ $job->company }}"></div>
                          <div><label>Category</label><input type="text" name="category" value="{{ $job->category }}"></div>
                        </div>
                        <div class="row">
                          <div><label>Location</label><input type="text" name="location" value="{{ $job->location }}"></div>
                          <div>
                            <label>City</label>
                            <select name="city_id">
                              <option value="">— None —</option>
                              @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $job->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                              @endforeach
                            </select>
                          </div>
                          <div><label>External URL</label><input type="url" name="external_url" value="{{ $job->external_url }}"></div>
                        </div>
                        <div class="row2">
                          <div>
                            <label>Assign to User</label>
                            <select name="user_id">
                              <option value="">— None —</option>
                              @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $job->user_id == $u->id ? 'selected' : '' }}>{{ $u->email }}</option>
                              @endforeach
                            </select>
                          </div>
                          <div><label>Description</label><textarea name="description">{{ $job->description }}</textarea></div>
                        </div>
                      </div>
                      <div class="admin-actions" style="margin-top:8px">
                        <button type="submit" class="button">Save Changes</button>
                        <button type="button" class="button" onclick="document.getElementById('edit-job-{{ $job->id }}').style.display='none'">Cancel</button>
                      </div>
                    </form>
                  </td>
                </tr>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;color:#6d84a5;padding:20px">No jobs found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:12px">{{ $jobs->links() }}</div>
  </div>
</div>
@endsection
