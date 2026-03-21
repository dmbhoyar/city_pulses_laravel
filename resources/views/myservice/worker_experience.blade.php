<!DOCTYPE html>
<html>
<head>
  <title>Experience Certificate</title>
  <style>body{font-family:Arial;margin:20px}</style>
</head>
<body>
<div class="experience-letter" style="max-width:800px;margin:20px auto;padding:20px;border:1px solid #ddd;background:#fff">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div>
      <h2>{{ $shop->name }}</h2>
      <div>{{ $shop->address }}</div>
      <div>Phone: {{ $shop->phone }}</div>
    </div>
    <div style="text-align:right">
      @if($shop->template)
        <div style="width:120px;height:60px;background:#f5f5f5;display:flex;align-items:center;justify-content:center">Logo</div>
      @endif
    </div>
  </div>

  <hr />

  <h3>Experience Certificate</h3>
  <p>Date: {{ \Carbon\Carbon::today()->format('d M Y') }}</p>

  <p>To whom it may concern,</p>

  <p>
    This is to certify that <strong>{{ $worker->full_name }}</strong> has worked with <strong>{{ $shop->name }}</strong> as a <strong>{{ $worker->tags ? explode(',', $worker->tags)[0] : 'Service Worker' }}</strong>.
  </p>

  @if($worker->experience)
    <p><strong>Experience details:</strong></p>
    <p>{!! simple_format($worker->experience) !!}</p>
  @endif

  <p>
    We wish them success in their future endeavors.
  </p>

  <div style="margin-top:40px;display:flex;justify-content:space-between;align-items:center">
    <div>
      <p>_______________________</p>
      <p><strong>{{ $shop->name }}</strong></p>
      <p>Authorized signatory</p>
    </div>
    <div style="text-align:right">
      <p>Signature</p>
    </div>
  </div>

  <p style="margin-top:20px;font-size:12px;color:#666">This is a system generated letter from AajchaOffer.</p>
</div>
</body>
</html>
