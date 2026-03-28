@extends('layouts.app')

@section('content')

<style>
  .profile-responsive-card {
    width: 100%;
    max-width: 900px;
    margin: 2.5rem auto 0 auto;
    background: linear-gradient(135deg, #23284a 60%, #181c2f 100%);
    border-radius: 32px;
    padding: 2.8rem 2.5rem 2.2rem 2.5rem;
    box-shadow: 0 4px 32px #0003;
    color: #fff;
    transition: box-shadow 0.2s;
    min-height: 80vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .profile-header {
    display: flex;
    align-items: center;
    gap: 32px;
    margin-bottom: 32px;
  }
  .profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg,#2f4e74,#4a90d9);
    color: #fff;
    font-size: 2.8em;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff2;
  }
  .profile-username {
    font-size: 2.1em;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.5px;
  }
  .profile-email {
    color: #b8c6e0;
    font-size: 1.1em;
    margin-top: 4px;
  }
  .profile-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 32px;
  }
  .profile-stat-card {
    background: #23284a;
    border-radius: 18px;
    padding: 22px 0 16px 0;
    text-align: center;
    box-shadow: 0 1px 4px #0001;
  }
  .profile-stat-value {
    font-size: 1.7em;
    font-weight: 700;
    margin-bottom: 2px;
  }
  .profile-stat-label {
    color: #b8c6e0;
    font-size: 1.08em;
    letter-spacing: 0.5px;
  }
  .profile-progress-label {
    font-weight: 600;
    margin-bottom: 2px;
    color: #fff;
    font-size: 1.1em;
  }
  .profile-progress-bar {
    background: #23284a;
    border-radius: 8px;
    height: 16px;
    margin-top: 8px;
    overflow: hidden;
    width: 100%;
  }
  .profile-progress-inner {
    background: linear-gradient(90deg,#c34141,#ffb347);
    height: 100%;
    transition: width 0.4s;
  }
  .profile-tiers-label {
    margin: 32px 0 16px 0;
    color: #fff;
    font-weight: 700;
    font-size: 1.18em;
    letter-spacing: 0.5px;
  }
  .profile-tiers-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }
  .profile-tier-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #23284a;
    padding: 16px 22px;
    border-radius: 16px;
    box-shadow: 0 1px 4px #0001;
  }
  .profile-tier-info {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .profile-tier-emoji {
    font-size: 2em;
  }
  .profile-tier-name {
    font-weight: 600;
    font-size: 1.1em;
  }
  .profile-tier-threshold {
    color: #b8c6e0;
    font-size: 1em;
  }
  .profile-tier-status {
    font-weight: 700;
    font-size: 1.1em;
  }
  .profile-tier-status.unlocked {
    color: #4ecdc4;
  }
  .profile-tier-status.locked {
    color: #888;
  }
  .profile-actions {
    margin-top: 32px;
    display: flex;
    gap: 18px;
  }
  .profile-actions a,
  .profile-actions button {
    flex: 1;
    padding: 18px 0;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1.18em;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
  }
  .profile-actions .edit-btn {
    background: linear-gradient(90deg,#ffb347,#c34141);
    color: #fff;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .profile-actions .edit-btn:hover {
    background: linear-gradient(90deg,#c34141,#ffb347);
  }
  .profile-actions .logout-btn {
    background: #fff0f0;
    color: #c0392b;
    border: 1px solid #f5c6c6;
  }
  .profile-actions .logout-btn:hover {
    background: #ffe0e0;
  }
  @media (max-width: 900px) {
    .profile-responsive-card {
      max-width: 98vw;
      padding: 1.2rem 0.3rem 1.2rem 0.3rem;
      border-radius: 0;
      margin: 0.5rem 0 0 0;
      box-shadow: none;
      min-height: unset;
    }
    .profile-header {
      flex-direction: column;
      gap: 10px;
      align-items: flex-start;
    }
    .profile-avatar {
      width: 60px;
      height: 60px;
      font-size: 1.5em;
    }
    .profile-stats {
      grid-template-columns: 1fr;
      gap: 10px;
    }
    .profile-actions a,
    .profile-actions button {
      font-size: 1em;
      padding: 10px 0;
    }
    .profile-tiers-list {
      gap: 8px;
    }
    .profile-tier-card {
      padding: 10px 10px;
      border-radius: 10px;
    }
    .profile-tier-emoji {
      font-size: 1.3em;
    }
  }
</style>

<div class="profile-responsive-card">
  <div class="profile-header">
    @if(!empty($user->avatar_url))
      <img src="{{ $user->avatar_url }}" alt="Avatar" class="profile-avatar">
    @else
      <div class="profile-avatar">{{ strtoupper(substr($user->first_name,0,1)) }}</div>
    @endif
    <div>
      <div class="profile-username">{{ $user->first_name }}</div>
      <div class="profile-email">{{ $user->email }}</div>
    </div>
  </div>

  <div class="profile-stats">
    <div class="profile-stat-card">
      <div class="profile-stat-value" style="color:#c34141;">{{ $ruby_points }}</div>
      <div class="profile-stat-label">RUBY PTS</div>
    </div>
    <div class="profile-stat-card">
      <div class="profile-stat-value" style="color:#ffd700;">{{ $subscribers }}</div>
      <div class="profile-stat-label">SUBSCRIBERS</div>
    </div>
    <div class="profile-stat-card">
      <div class="profile-stat-value" style="color:#e25555;">{{ $likes }}</div>
      <div class="profile-stat-label">LIKES</div>
    </div>
    <div class="profile-stat-card">
      <div class="profile-stat-value" style="color:#4ecdc4;">{{ $shares }}</div>
      <div class="profile-stat-label">REAL SHARES</div>
    </div>
  </div>

  <div class="profile-progress-label">
    @if($progress['next_tier'] ?? null)
      Progress to {{ $progress['next_tier_display'] }}:
      <span style="float:right;">{{ $progress['percentage'] }}%</span>
      <div class="profile-progress-bar">
        <div class="profile-progress-inner" style="width:{{ $progress['percentage'] }}%"></div>
      </div>
    @else
      Max Ruby Tier Achieved!
    @endif
  </div>

  <div class="profile-tiers-label">RUBY TIERS</div>
  <div class="profile-tiers-list">
    @foreach($tiers as $tier)
      <div class="profile-tier-card">
        <div class="profile-tier-info">
          <span class="profile-tier-emoji">{!! $tier['emoji'] !!}</span>
          <span class="profile-tier-name">{{ $tier['display_name'] }}</span>
          <span class="profile-tier-threshold">{{ number_format($tier['threshold']) }} pts required</span>
        </div>
        @if($tier['achieved'])
          <span class="profile-tier-status unlocked">Unlocked</span>
        @else
          <span class="profile-tier-status locked"><i class="fa fa-lock"></i> Locked</span>
        @endif
      </div>
    @endforeach
  </div>

  <div class="profile-actions">
    <a href="{{ route('profile.edit') }}" class="edit-btn">Edit Profile</a>
    <form action="{{ route('logout') }}" method="POST" style="flex:1;">
      @csrf
      <button type="submit" class="logout-btn" style="width:100%;">Sign Out</button>
    </form>
  </div>
</div>
@endsection
