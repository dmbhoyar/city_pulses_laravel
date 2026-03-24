@extends('layouts.app')

@php
  $cfg = is_array($service->page_config) ? $service->page_config : [];
  $templateContent = is_array($cfg['template_content'] ?? null) ? $cfg['template_content'] : [];
  $heroTitle = trim((string) ($templateContent['hero_title'] ?? '')) ?: ($service->name ?: 'सेवा प्रदाता');
  $heroDescription = trim((string) ($templateContent['hero_description'] ?? ''));
  $providerName = trim((string) ($cfg['provider_name'] ?? '')) ?: ($service->name ?: __('ui.provider'));
  $providerContact = trim((string) ($cfg['provider_contact'] ?? ''));
  $services = is_array($cfg['services'] ?? null) ? $cfg['services'] : [];
  $services = array_values(array_filter($services, fn ($value) => is_string($value) && trim($value) !== ''));
@endphp

@section('title', $heroTitle)

@section('content')
<style>
  .service-star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 3px;
  }

  .service-review-form input,
  .service-review-form textarea {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
  }

  @media (max-width: 560px) {
    .service-review-grid {
      grid-template-columns: 1fr !important;
    }
  }

  .service-star-rating input {
    display: none;
  }

  .service-star-rating label {
    cursor: pointer;
    font-size: 24px;
    line-height: 1;
    color: #c2cfdf;
    user-select: none;
  }

  .service-star-rating label::before {
    content: '★';
  }

  .service-star-rating input:checked ~ label,
  .service-star-rating label:hover,
  .service-star-rating label:hover ~ label {
    color: #d28a00;
  }
</style>

<section style="display:flex;flex-direction:column;gap:16px;">
  <div style="border:1px solid #42648f;background:linear-gradient(135deg,#2e456a,#3d5d89);border-radius:16px;padding:16px;box-shadow:0 12px 24px rgba(20,34,58,.24);color:#fff;">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
      <div>
        <p style="margin:0;font-size:11px;letter-spacing:.9px;text-transform:uppercase;color:#d8e8ff;font-weight:700;">{{ __('ui.service_provider_id_details') }}</p>
        <h1 style="margin:4px 0 0;font-size:30px;line-height:1.1;color:#fff;font-weight:800;">{{ $providerName }}</h1>
        @if($heroDescription !== '')
          <p style="margin:8px 0 0;font-size:13px;color:#e2ecff;max-width:700px;">{{ $heroDescription }}</p>
        @endif
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('shops.idcard.public', $service->public_page_slug) }}" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.35);background:rgba(255,255,255,.12);font-size:13px;font-weight:700;color:#fff;text-decoration:none;">{{ __('ui.view_id_card') }}</a>
        <a href="{{ route('shops.public', $service->public_page_slug) }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 12px;border-radius:10px;border:1px solid #fff;background:#fff;color:#2c4870;font-size:13px;font-weight:800;text-decoration:none;">{{ __('ui.open_public_page') }}</a>
      </div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr;gap:14px;">
    <article style="border:1px solid #d7deec;border-radius:14px;background:#fff;padding:14px;">
      <h2 style="margin:0;color:#263f63;font-size:18px;font-weight:800;">{{ __('ui.provider_details') }}</h2>
      <div style="margin-top:10px;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:10px;font-size:13px;color:#304a70;">
        <p style="margin:0;"><strong style="color:#1f3658;">{{ __('ui.name') }}:</strong> {{ $providerName }}</p>
        @if($providerContact !== '')
          <p style="margin:0;"><strong style="color:#1f3658;">{{ __('ui.contact') }}:</strong> {{ $providerContact }}</p>
        @endif
        @if($service->city)
          <p style="margin:0;"><strong style="color:#1f3658;">{{ __('ui.base_city') }}:</strong> {{ city_display_name($service->city->name) }}</p>
        @endif
        @if($serviceCities->isNotEmpty())
          <p style="margin:0;"><strong style="color:#1f3658;">{{ __('ui.service_cities') }}:</strong> {{ $serviceCities->map(fn ($cityName) => city_display_name($cityName))->implode(', ') }}</p>
        @endif
      </div>

      @if(!empty($services))
        <h3 style="margin:14px 0 8px;color:#2f4e74;font-size:12px;letter-spacing:.8px;text-transform:uppercase;">{{ __('ui.services_offered') }}</h3>
        <div style="display:flex;flex-wrap:wrap;gap:7px;">
          @foreach($services as $serviceName)
            <span style="padding:5px 10px;border-radius:999px;background:#eef3ff;border:1px solid #d5e0f6;color:#2f4f77;font-size:12px;font-weight:700;">{{ $serviceName }}</span>
          @endforeach
        </div>
      @endif
    </article>

    <aside style="border:1px solid #d7deec;border-radius:14px;background:#fff;padding:14px;">
      <h2 style="margin:0;color:#263f63;font-size:18px;font-weight:800;">{{ __('ui.ratings') }}</h2>
      <p style="margin:8px 0 0;color:#1f3658;font-size:34px;font-weight:800;line-height:1;">{{ number_format($reviewStats['average'], 1) }}</p>
      <p style="margin:6px 0 0;color:#4f6584;font-size:13px;">{{ trans_choice('ui.review_count', $reviewStats['count'], ['count' => $reviewStats['count']]) }}</p>
    </aside>
  </div>

  <section style="border:1px solid #d7deec;border-radius:14px;background:#fff;padding:14px;">
    <h2 style="margin:0;color:#263f63;font-size:18px;font-weight:800;">{{ __('ui.customer_reviews') }}</h2>

    <div style="margin-top:12px;display:flex;flex-direction:column;gap:10px;">
      @forelse($reviews as $review)
        @php
          $reviewerDisplayName = trim((string) ($review->user?->full_name ?? ''))
            ?: trim((string) ($review->reviewer_name ?? ''))
            ?: trim((string) ($review->reviewer_email ?? ''))
            ?: __('ui.anonymous_reviewer');
        @endphp
        <article style="border:1px solid #e1e7f2;border-radius:12px;background:#f8fbff;padding:10px 12px;">
          <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;">
            <p style="margin:0;font-size:13px;color:#1f3658;font-weight:800;">{{ $reviewerDisplayName }}</p>
            <p style="margin:0;font-size:11px;color:#6b7f9d;">{{ optional($review->updated_at)->format('d M Y') }}</p>
          </div>
          <p style="margin:5px 0 0;color:#c77a00;font-size:14px;">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</p>
          @if($review->review)
            <p style="margin:6px 0 0;font-size:13px;color:#324d74;line-height:1.45;">{{ $review->review }}</p>
          @endif
        </article>
      @empty
        <p style="margin:0;font-size:13px;color:#5d7394;">{{ __('ui.no_reviews_yet_service') }}</p>
      @endforelse
    </div>

    @auth
      <form method="POST" action="{{ route('services.reviews.store', $service->public_page_slug) }}" class="service-review-form" style="margin-top:14px;display:flex;flex-direction:column;gap:10px;border:1px solid #d2e0f4;border-radius:12px;background:#f1f6ff;padding:12px;">
        @csrf
        <h3 style="margin:0;color:#1f3658;font-size:14px;font-weight:800;">{{ $myReview ? __('ui.update_your_review') : __('ui.add_your_review') }}</h3>

        <div>
          <label for="rating" style="display:block;font-size:12px;color:#304c75;font-weight:700;">{{ __('ui.rating') }}</label>
          @php $selectedRating = (int) old('rating', $myReview?->rating ?? 5); @endphp
          <div id="rating" class="service-star-rating" style="margin-top:6px;">
            @for($rating = 5; $rating >= 1; $rating--)
              <input type="radio" id="auth_rating_{{ $rating }}" name="rating" value="{{ $rating }}" {{ $selectedRating === $rating ? 'checked' : '' }}>
              <label for="auth_rating_{{ $rating }}" title="{{ trans_choice('ui.star_count', $rating, ['count' => $rating]) }}"></label>
            @endfor
          </div>
          @error('rating')<p style="margin:4px 0 0;color:#b91c1c;font-size:11px;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="review" style="display:block;font-size:12px;color:#304c75;font-weight:700;">{{ __('ui.review') }}</label>
          <textarea id="review" name="review" rows="4" style="margin-top:4px;width:100%;border:1px solid #bfd0ea;border-radius:10px;padding:8px 10px;font-size:13px;" placeholder="{{ __('ui.share_experience_optional') }}">{{ old('review', $myReview?->review) }}</textarea>
          @error('review')<p style="margin:4px 0 0;color:#b91c1c;font-size:11px;">{{ $message }}</p>@enderror
        </div>

        <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;border:none;border-radius:10px;background:#2f4f77;color:#fff;padding:9px 13px;font-size:13px;font-weight:800;cursor:pointer;">
          {{ $myReview ? __('ui.update_review') : __('ui.submit_review') }}
        </button>
      </form>
    @else
      <form method="POST" action="{{ route('services.reviews.store', $service->public_page_slug) }}" class="service-review-form" style="margin-top:14px;display:flex;flex-direction:column;gap:10px;border:1px solid #d2e0f4;border-radius:12px;background:#f1f6ff;padding:12px;">
        @csrf
        <h3 style="margin:0;color:#1f3658;font-size:14px;font-weight:800;">{{ __('ui.add_your_review') }}</h3>

        <div class="service-review-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(0,1fr));gap:10px;">
          <div>
            <label for="reviewer_name" style="display:block;font-size:12px;color:#304c75;font-weight:700;">{{ __('ui.your_name') }}</label>
            <input id="reviewer_name" name="reviewer_name" type="text" value="{{ old('reviewer_name') }}" required style="margin-top:4px;width:100%;border:1px solid #bfd0ea;border-radius:10px;padding:8px 10px;font-size:13px;" placeholder="{{ __('ui.enter_full_name') }}">
            @error('reviewer_name')<p style="margin:4px 0 0;color:#b91c1c;font-size:11px;">{{ $message }}</p>@enderror
          </div>
          <div>
            <label for="reviewer_email" style="display:block;font-size:12px;color:#304c75;font-weight:700;">{{ __('ui.your_email') }}</label>
            <input id="reviewer_email" name="reviewer_email" type="email" value="{{ old('reviewer_email') }}" required style="margin-top:4px;width:100%;border:1px solid #bfd0ea;border-radius:10px;padding:8px 10px;font-size:13px;" placeholder="{{ __('ui.enter_email') }}">
            @error('reviewer_email')<p style="margin:4px 0 0;color:#b91c1c;font-size:11px;">{{ $message }}</p>@enderror
          </div>
        </div>

        <div>
          <label for="rating" style="display:block;font-size:12px;color:#304c75;font-weight:700;">{{ __('ui.rating') }}</label>
          @php $guestSelectedRating = (int) old('rating', 5); @endphp
          <div id="rating" class="service-star-rating" style="margin-top:6px;">
            @for($rating = 5; $rating >= 1; $rating--)
              <input type="radio" id="guest_rating_{{ $rating }}" name="rating" value="{{ $rating }}" {{ $guestSelectedRating === $rating ? 'checked' : '' }}>
              <label for="guest_rating_{{ $rating }}" title="{{ trans_choice('ui.star_count', $rating, ['count' => $rating]) }}"></label>
            @endfor
          </div>
          @error('rating')<p style="margin:4px 0 0;color:#b91c1c;font-size:11px;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="review" style="display:block;font-size:12px;color:#304c75;font-weight:700;">{{ __('ui.review') }}</label>
          <textarea id="review" name="review" rows="4" style="margin-top:4px;width:100%;border:1px solid #bfd0ea;border-radius:10px;padding:8px 10px;font-size:13px;" placeholder="{{ __('ui.write_review_optional') }}">{{ old('review') }}</textarea>
          @error('review')<p style="margin:4px 0 0;color:#b91c1c;font-size:11px;">{{ $message }}</p>@enderror
        </div>

        <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;border:none;border-radius:10px;background:#2f4f77;color:#fff;padding:9px 13px;font-size:13px;font-weight:800;cursor:pointer;">{{ __('ui.submit_review') }}</button>
      </form>
    @endauth
  </section>

  <div>
    <a href="{{ route('services.index') }}" style="font-size:13px;font-weight:700;color:#2e4f77;text-decoration:none;">← {{ __('ui.back_to_all_services') }}</a>
  </div>
</section>
@endsection
