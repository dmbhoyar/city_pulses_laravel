<!DOCTYPE html>
<html>
<head>
  <title>Experience Letter</title>
  <style>body{font-family:Arial;margin:20px}</style>
</head>
<body>
  <h1>Experience Letter</h1>
  @if(isset($shop) && $shop)
    <p>To whom it may concern,</p>
    <p>This is to certify that <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong> has been associated with <strong>{{ $shop->name }}</strong> as a service provider.</p>
    <p>Service address: {{ $shop->address }}</p>
    <p>Issued on {{ \Carbon\Carbon::today()->format('d M Y') }}</p>
    <p><button onclick="window.print()" class="toggle-btn">Print / Save as PDF</button></p>
  @else
    <p>No service page found.</p>
  @endif
</body>
</html>
