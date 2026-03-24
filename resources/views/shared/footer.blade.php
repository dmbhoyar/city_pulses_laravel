<footer class="site-footer">
  <div class="footer-glow"></div>

  <div class="footer-inner">

    <!-- Brand column -->
    <div class="footer-col footer-brand-col">
      <div class="footer-logo">
        @include('shared.brand_logo', ['className' => 'footer-logo-svg', 'title' => 'AajchaOffer logo'])
        <span class="footer-logo-name">Aajcha<span class="footer-logo-accent">Offer</span></span>
      </div>
      <p class="footer-tagline">Aajcha bhav, aajcha offer.<br>{{ __('ui.footer_tagline_line2') }}</p>
      <div class="footer-social">
        <a href="https://www.instagram.com/aajchaoffer?igsh=YW51dTNrMXJicDNh" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Instagram" title="Instagram">
          <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" fill="#1a1a2e"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="#1a1a2e" stroke-width="2"/></svg>
        </a>
        <a href="#" class="social-btn" aria-label="YouTube" title="YouTube">
          <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#1a1a2e"/></svg>
        </a>
      </div>
    </div>

    <!-- Our Services -->
    <div class="footer-col">
      <h4 class="footer-heading"><span class="footer-heading-dot"></span>{{ __('ui.footer_our_services') }}</h4>
      <ul class="footer-links">
        <li><a href="{{ route('home') }}"><span class="fl-icon">&#127968;</span> {{ __('ui.footer_home_city_feed') }}</a></li>
        <li><a href="{{ route('updates.index') }}"><span class="fl-icon">&#128240;</span> {{ __('ui.footer_news_updates') }}</a></li>
        <li><a href="{{ route('jobs.index') }}"><span class="fl-icon">&#128188;</span> {{ __('ui.footer_jobs_board') }}</a></li>
        <li><a href="{{ route('farming.index') }}"><span class="fl-icon">&#127807;</span> {{ __('ui.footer_farming_market_rates') }}</a></li>
        <li><a href="{{ route('rents.index') }}"><span class="fl-icon">&#127968;</span> {{ __('ui.footer_rental_listings') }}</a></li>
        <li><a href="{{ route('buy.index') }}"><span class="fl-icon">&#128722;</span> {{ __('ui.buy_sell') }}</a></li>
        <li><a href="{{ route('services.index') }}"><span class="fl-icon">&#128295;</span> {{ __('ui.footer_local_services') }}</a></li>
        <li><a href="{{ route('shops.index') }}"><span class="fl-icon">&#127978;</span> {{ __('ui.footer_local_shops') }}</a></li>
        <li><a href="{{ route('offers') }}"><span class="fl-icon">&#127881;</span> {{ __('ui.offers_benefits') }}</a></li>
      </ul>
    </div>

    <!-- Quick Links -->
    <div class="footer-col">
      <h4 class="footer-heading"><span class="footer-heading-dot"></span>{{ __('ui.footer_quick_links') }}</h4>
      <ul class="footer-links">
        <li><a href="{{ route('about') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_about_aajchaoffer') }}</a></li>
        <li><a href="{{ route('about') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_how_it_works') }}</a></li>
        <li><a href="{{ route('offers') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_advertise_with_us') }}</a></li>
        <li><a href="{{ route('shops.create') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_list_your_shop') }}</a></li>
        <li><a href="{{ route('jobs.create') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_post_a_job') }}</a></li>
        <li><a href="{{ route('register') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_become_partner') }}</a></li>
        <li><a href="{{ route('updates.index') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_blog_articles') }}</a></li>
        <li><a href="{{ route('sitemap') }}"><span class="fl-icon">&#10003;</span> {{ __('ui.footer_sitemap') }}</a></li>
      </ul>
    </div>

    <!-- Support & Legal -->
    <div class="footer-col">
      <h4 class="footer-heading"><span class="footer-heading-dot"></span>{{ __('ui.footer_support_legal') }}</h4>
      <ul class="footer-links">
        <li><a href="mailto:contact@aajchaoffer.com"><span class="fl-icon">&#128241;</span> {{ __('ui.footer_contact_us') }}</a></li>
        <li><a href="{{ route('about') }}"><span class="fl-icon">&#128222;</span> {{ __('ui.footer_help_faq') }}</a></li>
        <li><a href="mailto:contact@aajchaoffer.com?subject=Issue%20Report"><span class="fl-icon">&#128276;</span> {{ __('ui.footer_report_issue') }}</a></li>
        <li><a href="{{ route('about') }}"><span class="fl-icon">&#128274;</span> {{ __('ui.footer_privacy_policy') }}</a></li>
        <li><a href="{{ route('about') }}"><span class="fl-icon">&#128203;</span> {{ __('ui.footer_terms_service') }}</a></li>
        <li><a href="{{ route('about') }}"><span class="fl-icon">&#127381;</span> {{ __('ui.footer_cookie_policy') }}</a></li>
        <li><a href="{{ route('about') }}"><span class="fl-icon">&#127381;</span> {{ __('ui.footer_disclaimer') }}</a></li>
      </ul>
    </div>

  </div><!-- /.footer-inner -->

  <!-- Newsletter strip -->
  <div class="footer-newsletter">
    <div class="footer-newsletter-inner">
      <div class="footer-newsletter-text">
        <strong>{{ __('ui.footer_stay_in_pulse') }}</strong>
        <span>{{ __('ui.footer_newsletter_copy') }}</span>
      </div>
    </div>
  </div>

  <!-- Bottom bar -->
  <div class="footer-bottom">
    <div class="footer-bottom-inner">
      <span class="footer-copy">&copy; {{ date('Y') }} AajchaOffer. {{ __('ui.footer_rights_reserved') }} {{ __('ui.footer_made_for_local') }}</span>
      <div class="footer-bottom-links">
        <a href="{{ route('about') }}">{{ __('ui.privacy') }}</a>
        <a href="{{ route('about') }}">{{ __('ui.terms') }}</a>
        <a href="{{ route('about') }}">{{ __('ui.cookies') }}</a>
        <a href="{{ route('about') }}">{{ __('ui.accessibility') }}</a>
      </div>
    </div>
  </div>

</footer>
