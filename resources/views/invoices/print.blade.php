<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $invoice->status === 'draft' ? 'Estimate' : 'Invoice' }} {{ $invoice->invoice_number }}</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }
body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; background: #f4f4f4 }

@media screen {
  .pbar {
    background: #1a1208; color: #fff;
    padding: .6rem 1.5rem;
    display: flex; align-items: center; gap: .55rem; flex-wrap: wrap;
    position: sticky; top: 0; z-index: 99;
  }
  .pbar h1 { font-size: .88rem; font-weight: 700; margin-right: auto }
  .pbar a, .pbar button {
    padding: .35rem .8rem; border-radius: 6px; font-size: .76rem; font-weight: 700;
    text-decoration: none; cursor: pointer; border: none;
    display: inline-flex; align-items: center; gap: .3rem;
  }
  .pbtn-back  { background: rgba(255,255,255,.15); color: #fff }
  .pbtn-print { background: #fbbf24; color: #1a1208 }
  .pbtn-wa    { background: #25d366; color: #fff }
  .pbtn-wa:hover { background: #1da851 }
  .page { max-width: 860px; margin: 1.5rem auto 3rem; padding: 0 .75rem }
}
@media print {
  .pbar { display: none !important }
  .page { margin: 0; padding: 0 }
  body { font-size: 11px; background: #fff }
  .no-print { display: none !important }
  /* Always show full tax table when printing */
  .th-hide { display: table-cell !important }
  .th-mob-gst { display: none !important }
}

/* ── Document wrapper ── */
.doc { border: 1.5px solid #bbb; background: #fff; position: relative }

/* ── Header ── */
.doc-head {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 1rem 1.25rem; border-bottom: 2px solid #111; gap: 1rem;
}
.biz-left { display: flex; align-items: flex-start; gap: .85rem; flex: 1 }
.biz-logo  { width: 75px; height: 75px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; flex-shrink: 0 }
.biz-name  { font-size: 1.15rem; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; color: #111; margin-bottom: .25rem }
.biz-detail{ font-size: .76rem; color: #333; line-height: 1.7 }
.inv-box   { text-align: right; min-width: 155px; flex-shrink: 0 }
.inv-type  { font-size: 1.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #111; margin-bottom: .35rem }
.inv-no-label { font-size: .62rem; color: #777; text-transform: uppercase; letter-spacing: 1px }
.inv-no-val   { font-size: 1rem; font-weight: 800; font-family: 'Courier New', monospace; color: #111 }
.inv-dates { font-size: .76rem; color: #444; margin-top: .3rem; line-height: 1.7 }

/* ── Bill-to row ── */
.doc-subhead { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #bbb }
.subhead-cell { padding: .6rem 1.25rem }
.subhead-cell:first-child { border-right: 1px solid #bbb }
.sub-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #777; margin-bottom: .3rem }
.sub-name  { font-size: .95rem; font-weight: 700; color: #111; margin-bottom: .12rem }
.sub-detail{ font-size: .74rem; color: #444; line-height: 1.65 }

/* ── Items table ── */
.items-tbl { width: 100%; border-collapse: collapse }
.items-tbl thead th {
  background: #f4f4f4; border-top: 1px solid #bbb; border-bottom: 1px solid #bbb;
  padding: .42rem .6rem; font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .4px; color: #555; text-align: left;
}
.items-tbl thead th.r { text-align: right }
.items-tbl tbody td {
  padding: .5rem .6rem; border-bottom: 1px solid #ebebeb;
  font-size: .8rem; vertical-align: top; color: #222;
}
.items-tbl tbody td.r { text-align: right }
.items-tbl tfoot td {
  padding: .42rem .6rem; border-top: 2px solid #bbb;
  font-size: .8rem; font-weight: 700; color: #111;
}
.items-tbl tfoot td.r { text-align: right }
.item-main { font-weight: 600; color: #111 }
.item-sub  { font-size: .7rem; color: #777; margin-top: .08rem }
.gst-right { font-size: .75rem; text-align: right }
.gst-pct   { font-size: .65rem; color: #777; display: block }

/* ── Bottom: tax summary + totals ── */
.doc-bottom { display: grid; grid-template-columns: 1fr auto; border-top: 2px solid #111 }
.tax-col { padding: .9rem 1.25rem; border-right: 1px solid #bbb }
.tax-col-title { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #777; margin-bottom: .45rem }
.tax-tbl { width: 100%; border-collapse: collapse; font-size: .71rem }
.tax-tbl th {
  background: #f4f4f4; padding: .3rem .42rem;
  font-weight: 700; color: #555; border: 1px solid #ddd; text-align: left;
}
.tax-tbl td { padding: .28rem .42rem; border: 1px solid #e8e8e8; color: #333 }
.tax-tbl .total-row td { font-weight: 700; background: #f9f9f9 }
.totals-col { padding: .9rem 1.25rem; min-width: 210px }
.tot-row { display: flex; justify-content: space-between; padding: .3rem 0; font-size: .8rem; border-bottom: 1px solid #f0f0f0 }
.tot-row .lbl { color: #777 }
.tot-row .val { font-weight: 600 }
.tot-grand {
  background: #111; color: #fff; margin-top: .45rem; border-radius: 4px;
  padding: .55rem .75rem; display: flex; justify-content: space-between; align-items: center;
}
.tot-grand .lbl { font-size: .64rem; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,.6) }
.tot-grand .val { font-size: 1.05rem; font-weight: 800; color: #fbbf24 }
.tot-words { font-size: .71rem; font-style: italic; color: #777; margin-top: .35rem; text-align: right; line-height: 1.4 }

/* Desktop: mobile-only col hidden */
.th-mob-gst { display: none }

/* ── Footer: 3 columns ── */
.doc-footer { border-top: 1px solid #bbb; display: grid; grid-template-columns: 1fr 1fr 1fr }
.footer-cell { padding: .85rem 1.25rem }
.footer-cell + .footer-cell { border-left: 1px solid #bbb }
.footer-title { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #777; margin-bottom: .35rem }
.footer-text  { font-size: .74rem; color: #444; line-height: 1.7; white-space: pre-line }
.bank-row { display: flex; gap: .75rem; align-items: flex-start }
.bank-qr  { width: 72px; height: 72px; object-fit: contain; border: 1px solid #ddd; padding: 2px; flex-shrink: 0 }
.bank-dl dt { float: left; width: 58px; font-size: .68rem; color: #888; clear: left }
.bank-dl dd { margin-left: 58px; font-size: .74rem; font-weight: 600; color: #222; margin-bottom: .1rem }
.sig-wrap { margin-top: .35rem }
.sig-img  { height: 55px; max-width: 150px; object-fit: contain; display: block }
.sig-line { border-top: 1px solid #bbb; padding-top: .25rem; margin-top: .35rem; font-size: .72rem; color: #555 }

/* Paid stamp */
.paid-stamp {
  color: rgba(21,128,61,.2); border: 4px solid rgba(21,128,61,.15);
  padding: .1rem .4rem; transform: rotate(-18deg);
  font-size: 2rem; font-weight: 900; letter-spacing: 4px;
  position: absolute; top: 80px; right: 60px; border-radius: 6px; pointer-events: none;
}

/* ── Mobile responsive ── */
@media screen and (max-width: 640px) {
  /* Toolbar */
  .pbar { padding: .4rem .75rem; gap: .3rem; flex-wrap: wrap }
  .pbar h1 { font-size: .74rem; width: 100%; order: -1; margin-right: 0 }
  .pbar a, .pbar button { padding: .28rem .55rem; font-size: .67rem; flex: 1; justify-content: center }
  .page { margin: .5rem auto 2rem; padding: 0 .3rem }

  /* Header: biz info on top, invoice box below */
  .doc-head { flex-direction: column; padding: .8rem .9rem; gap: .6rem }
  .biz-left { gap: .6rem }
  .biz-logo { width: 50px; height: 50px }
  .biz-name { font-size: .92rem }
  .biz-detail { font-size: .69rem; line-height: 1.6 }
  .inv-box {
    text-align: left; min-width: unset; width: 100%;
    display: flex; flex-wrap: wrap; align-items: baseline; gap: .4rem .75rem;
    border-top: 1px solid #ddd; padding-top: .55rem;
  }
  .inv-type { font-size: 1rem; font-weight: 900; margin-bottom: 0 }
  .inv-no-label { font-size: .58rem }
  .inv-no-val { font-size: .88rem }
  .inv-dates { font-size: .69rem; width: 100% }

  /* Bill-to: single column */
  .doc-subhead { grid-template-columns: 1fr }
  .subhead-cell:first-child { border-right: none; border-bottom: 1px solid #bbb }
  .subhead-cell { padding: .5rem .9rem }
  .sub-name { font-size: .85rem }
  .sub-detail { font-size: .69rem }

  /* ── Items: card layout (no horizontal scroll) ── */
  .items-wrap { overflow-x: visible }
  .items-tbl, .items-tbl thead, .items-tbl tbody,
  .items-tbl tr, .items-tbl th, .items-tbl td { display: block; width: 100% }
  .items-tbl thead tr { display: none }   /* hide column headers */
  .items-tbl tbody tr {
    border: 1px solid #e0e0e0; border-radius: 7px;
    margin: .45rem 0; overflow: hidden; background: #fff;
  }
  .items-tbl tbody td {
    display: flex; justify-content: space-between; align-items: flex-start;
    border-bottom: 1px solid #f0f0f0; padding: .32rem .75rem;
    font-size: .78rem; text-align: left !important;
  }
  .items-tbl tbody td:last-child { border-bottom: none }
  .items-tbl tbody td[data-label="#"] { display: none }  /* hide row # */
  .items-tbl tbody td[data-label="Item"] {
    font-weight: 700; font-size: .82rem; color: #111;
    background: #f7f7f7; padding: .4rem .75rem;
    display: block;  /* full width, no flex label */
  }
  .items-tbl tbody td[data-label="Item"]::before { display: none }
  .items-tbl tbody td::before {
    content: attr(data-label);
    font-size: .61rem; font-weight: 700; color: #999;
    text-transform: uppercase; flex-shrink: 0;
    width: 72px; padding-top: .05rem;
  }
  /* Tfoot: show as simple summary line */
  .items-tbl tfoot tr {
    display: flex; border-top: 2px solid #bbb;
    padding: .4rem .75rem; gap: .5rem; flex-wrap: wrap;
    justify-content: flex-end; background: #f7f7f7;
  }
  .items-tbl tfoot td { display: none; border: none; padding: 0; font-size: .76rem; width: auto }
  .items-tbl tfoot td.tfoot-gst   { display: block; color: #555 }
  .items-tbl tfoot td.tfoot-total { display: block; font-weight: 800; color: #111 }

  /* ── Bottom: tax + totals stacked ── */
  .doc-bottom { grid-template-columns: 1fr }
  .tax-col { border-right: none; border-bottom: 1px solid #bbb; padding: .7rem .9rem }
  /* Tax table: swap to 4-column on mobile */
  .th-hide { display: none }
  .th-mob-gst { display: table-cell }
  .tax-tbl { width: 100%; font-size: .72rem }
  .tax-tbl th { padding: .28rem .35rem; font-size: .62rem }
  .tax-tbl td { padding: .26rem .35rem }
  .totals-col { min-width: unset; padding: .7rem .9rem }
  .tot-row { font-size: .8rem }
  .tot-grand { padding: .45rem .65rem }
  .tot-grand .val { font-size: .95rem }
  .tot-words { font-size: .69rem }

  /* ── Footer: single column ── */
  .doc-footer { grid-template-columns: 1fr }
  .footer-cell + .footer-cell { border-left: none; border-top: 1px solid #bbb }
  .footer-cell { padding: .65rem .9rem }
  .bank-row { gap: .6rem }
  .bank-qr { width: 60px; height: 60px }
  .bank-dl dt { width: 50px }
  .bank-dl dd { margin-left: 50px; font-size: .71rem }
  .sig-img { height: 46px }
}
@media screen and (max-width: 400px) {
  .pbar a, .pbar button { font-size: .62rem; padding: .25rem .4rem }
  .biz-logo { width: 42px; height: 42px }
  .biz-name { font-size: .85rem }
}
</style>
</head>
<body>

@php
use Illuminate\Support\Str;

$taxByHsn = $invoice->taxSummaryByHsn();
$hasTax   = collect($taxByHsn)->sum('tax') > 0;

$waText = rawurlencode(
    "Hello {$invoice->client_name}! ".
    ($invoice->business_name ? "Here is your invoice from {$invoice->business_name}." : "Here is your invoice.").
    "\n\nInvoice No: {$invoice->invoice_number}".
    "\nDate: ".optional($invoice->invoice_date)->format('d M Y').
    "\nTotal: ₹".number_format($invoice->total, 2).
    "\n\nView: ".url()->current()
);

function inWords(float $n): string {
    $n = (int) round($n);
    if ($n === 0) return 'Zero';
    $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
             'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
             'Seventeen','Eighteen','Nineteen'];
    $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
    if ($n < 20)       return $ones[$n];
    if ($n < 100)      return $tens[intdiv($n,10)].($n%10 ? ' '.$ones[$n%10] : '');
    if ($n < 1000)     return $ones[intdiv($n,100)].' Hundred'.($n%100 ? ' '.inWords($n%100) : '');
    if ($n < 100000)   return inWords(intdiv($n,1000)).' Thousand'.($n%1000 ? ' '.inWords($n%1000) : '');
    if ($n < 10000000) return inWords(intdiv($n,100000)).' Lakh'.($n%100000 ? ' '.inWords($n%100000) : '');
    return inWords(intdiv($n,10000000)).' Crore'.($n%10000000 ? ' '.inWords($n%10000000) : '');
}
$amountWords = inWords((float) $invoice->total).' Rupees Only';

$bizName    = $profile->business_name    ?: $invoice->business_name;
$bizPhone   = $profile->business_phone   ?: $invoice->business_phone;
$bizEmail   = $profile->business_email;
$bizAddr    = $profile->business_address ?: $invoice->business_address;
$bizGstin   = $profile->gstin            ?: $invoice->business_gstin;
$bizLogo    = $profile->business_logo;
$docType    = $invoice->status === 'draft' ? 'Estimate' : 'Invoice';
@endphp

{{-- Screen toolbar --}}
<div class="pbar">
    <h1>{{ $docType }} {{ $invoice->invoice_number }}</h1>
    <a href="{{ route('invoices.show', $invoice) }}" class="pbtn-back">← Back</a>
    <a href="https://wa.me/?text={{ $waText }}" target="_blank" rel="noopener" class="pbtn-wa" id="wa-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        Share WhatsApp
    </a>
    <button onclick="window.print()" class="pbtn-print">🖨 Print / Save PDF</button>
</div>

<div class="page">
<div class="doc">

    @if($invoice->status === 'paid')
        <div class="paid-stamp no-print">PAID</div>
    @endif

    {{-- ── Header ── --}}
    <div class="doc-head">
        <div class="biz-left">
            @if($bizLogo)
                <img src="{{ asset('storage/'.$bizLogo) }}" alt="Logo" class="biz-logo">
            @endif
            <div>
                <div class="biz-name">{{ $bizName ?: 'Your Business Name' }}</div>
                <div class="biz-detail">
                    @if($bizAddr) {{ str_replace("\n", ', ', trim($bizAddr)) }}<br> @endif
                    @if($bizPhone) Phone: <strong>{{ $bizPhone }}</strong>
                        @if($bizEmail) &nbsp;&nbsp; Email: <strong>{{ $bizEmail }}</strong> @endif
                        <br>
                    @elseif($bizEmail)
                        Email: <strong>{{ $bizEmail }}</strong><br>
                    @endif
                    @if($bizGstin) GSTIN: <strong>{{ $bizGstin }}</strong> &nbsp;&nbsp; @endif
                    State: <strong>27-Maharashtra</strong>
                </div>
            </div>
        </div>
        <div class="inv-box">
            <div class="inv-type">{{ $docType }}</div>
            <div class="inv-no-label">No.</div>
            <div class="inv-no-val">{{ $invoice->invoice_number }}</div>
            <div class="inv-dates">
                Date: <strong>{{ optional($invoice->invoice_date)->format('d-m-Y') }}</strong><br>
                @if($invoice->due_date) Due: <strong>{{ $invoice->due_date->format('d-m-Y') }}</strong><br> @endif
                Status: <strong>{{ strtoupper($invoice->status) }}</strong>
            </div>
        </div>
    </div>

    {{-- ── Bill To + Details ── --}}
    <div class="doc-subhead">
        <div class="subhead-cell">
            <div class="sub-label">{{ $docType }} For</div>
            <div class="sub-name">{{ $invoice->client_name }}</div>
            <div class="sub-detail">
                @if($invoice->client_phone) Phone: {{ $invoice->client_phone }}<br> @endif
                @if($invoice->client_email) Email: {{ $invoice->client_email }}<br> @endif
                @if($invoice->client_address) {{ $invoice->client_address }} @endif
            </div>
        </div>
        <div class="subhead-cell">
            @if($invoice->payment_received_at)
                <div class="sub-label">{{ $docType }} Details</div>
                <div class="sub-detail">
                    Payment received {{ $invoice->payment_received_at->format('d M Y') }}
                    via {{ ucfirst($invoice->payment_method ?? 'cash') }}<br>
                    @if($invoice->payment_note) Note: {{ $invoice->payment_note }} @endif
                </div>
            @else
                <div class="sub-label">{{ $docType }} Details</div>
                <div class="sub-detail">
                    No. <strong>{{ $invoice->invoice_number }}</strong><br>
                    Date: <strong>{{ optional($invoice->invoice_date)->format('d-m-Y') }}</strong>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Items Table ── --}}
    <div class="items-wrap"><table class="items-tbl">
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:34%">Item Name</th>
                <th style="width:11%">HSN/SAC</th>
                <th class="r" style="width:7%">QTY</th>
                <th class="r" style="width:12%">Price/Unit (₹)</th>
                <th class="r" style="width:13%">GST (₹)</th>
                <th class="r" style="width:13%">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td data-label="#">{{ $i+1 }}</td>
                <td data-label="Item">
                    <span class="item-main">{{ $item->description }}</span>
                    @if($item->unit)<span class="item-sub">{{ $item->unit }}</span>@endif
                </td>
                <td data-label="HSN/SAC" style="font-family:monospace;font-size:.71rem;color:#555">{{ $item->hsn_sac ?: '—' }}</td>
                <td data-label="Qty" class="r">{{ rtrim(rtrim(number_format((float)$item->quantity, 3, '.', ''), '0'), '.') }}</td>
                <td data-label="Price" class="r">₹ {{ number_format($item->unit_price, 2) }}</td>
                <td data-label="GST">
                    @if($item->tax_rate > 0)
                    <div class="gst-right">
                        ₹ {{ number_format($item->tax_amount, 2) }}
                        <span class="gst-pct">({{ number_format($item->tax_rate, 1) }}%)</span>
                    </div>
                    @else
                    <span style="color:#ccc;display:block;text-align:right">—</span>
                    @endif
                </td>
                <td data-label="Amount" class="r" style="font-weight:700">₹ {{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="color:#777;font-weight:400;font-size:.7rem">Total</td>
                <td class="r">{{ $invoice->items->sum(fn($i) => (float)$i->quantity) }}</td>
                <td></td>
                <td class="r tfoot-gst">GST: ₹ {{ number_format($invoice->tax_amount, 2) }}</td>
                <td class="r tfoot-total">₹ {{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tfoot>
    </table></div>{{-- end items-wrap --}}

    {{-- ── Tax Summary + Totals ── --}}
    <div class="doc-bottom">
        <div class="tax-col">
            @if($hasTax)
            <div class="tax-col-title">Tax Summary</div>
            <table class="tax-tbl">
                <thead>
                    <tr>
                        <th>HSN/SAC</th>
                        <th>Taxable Amt (₹)</th>
                        <th class="th-hide">CGST Rate(%)</th>
                        <th class="th-hide">CGST Amt (₹)</th>
                        <th class="th-hide">SGST Rate(%)</th>
                        <th class="th-hide">SGST Amt (₹)</th>
                        <th class="th-mob-gst">CGST+SGST</th>
                        <th>Total Tax (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($taxByHsn as $row)
                    @if($row['tax'] > 0)
                    <tr>
                        <td style="font-family:monospace">{{ $row['hsn'] }}</td>
                        <td>{{ number_format($row['taxable'], 2) }}</td>
                        <td class="th-hide">{{ number_format($row['rate']/2, 1) }}</td>
                        <td class="th-hide">{{ number_format($row['tax']/2, 2) }}</td>
                        <td class="th-hide">{{ number_format($row['rate']/2, 1) }}</td>
                        <td class="th-hide">{{ number_format($row['tax']/2, 2) }}</td>
                        <td class="th-mob-gst" style="font-size:.7rem;color:#555">
                            {{ number_format($row['rate']/2,1) }}% + {{ number_format($row['rate']/2,1) }}%<br>
                            <span style="font-size:.72rem;font-weight:600;color:#222">₹{{ number_format($row['tax']/2,2) }}+₹{{ number_format($row['tax']/2,2) }}</span>
                        </td>
                        <td style="font-weight:700">{{ number_format($row['tax'], 2) }}</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr class="total-row">
                        <td><strong>TOTAL</strong></td>
                        <td>{{ number_format($invoice->subtotal, 2) }}</td>
                        <td class="th-hide"></td>
                        <td class="th-hide">{{ number_format($invoice->tax_amount/2, 2) }}</td>
                        <td class="th-hide"></td>
                        <td class="th-hide">{{ number_format($invoice->tax_amount/2, 2) }}</td>
                        <td class="th-mob-gst"></td>
                        <td>{{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>

        <div class="totals-col">
            <div class="tot-row"><span class="lbl">Sub Total</span><span class="val">₹ {{ number_format($invoice->subtotal, 2) }}</span></div>
            @if($invoice->tax_amount > 0)
            <div class="tot-row"><span class="lbl">GST / Tax</span><span class="val">₹ {{ number_format($invoice->tax_amount, 2) }}</span></div>
            @endif
            @if($invoice->discount_amount > 0)
            <div class="tot-row"><span class="lbl">Discount</span><span class="val" style="color:#dc2626">- ₹ {{ number_format($invoice->discount_amount, 2) }}</span></div>
            @endif
            <div class="tot-grand">
                <span class="lbl">Total</span>
                <span class="val">₹ {{ number_format($invoice->total, 2) }}</span>
            </div>
            <div class="tot-words">
                {{ $docType }} Amount In Words :<br>{{ $amountWords }}
            </div>
        </div>
    </div>

    {{-- ── Footer: Terms | Bank+QR | Signature ── --}}
    <div class="doc-footer">

        {{-- Terms & Notes --}}
        <div class="footer-cell">
            @if($invoice->terms)
                <div class="footer-title">Terms And Conditions</div>
                <div class="footer-text">{{ $invoice->terms }}</div>
            @endif
            @if($invoice->notes)
                <div class="footer-title" @if($invoice->terms) style="margin-top:.6rem" @endif>Notes</div>
                <div class="footer-text">{{ $invoice->notes }}</div>
            @endif
            @if(!$invoice->terms && !$invoice->notes)
                <div class="footer-title">Terms And Conditions</div>
                <div class="footer-text" style="color:#bbb;font-style:italic">—</div>
            @endif
        </div>

        {{-- Bank Details + QR --}}
        <div class="footer-cell">
            <div class="footer-title">Bank Details</div>
            @if($invoice->bank_name || $invoice->bank_account || $profile->payment_qr)
                <div class="bank-row">
                    @if($profile->payment_qr)
                        <img src="{{ asset('storage/'.$profile->payment_qr) }}" alt="QR" class="bank-qr">
                    @endif
                    <dl class="bank-dl" style="margin:0">
                        @if($invoice->bank_name)<dt>Name</dt><dd>{{ $invoice->bank_name }}</dd>@endif
                        @if($invoice->bank_account)<dt>A/C No.</dt><dd>{{ $invoice->bank_account }}</dd>@endif
                        @if($invoice->bank_ifsc)<dt>IFSC</dt><dd>{{ $invoice->bank_ifsc }}</dd>@endif
                        @if($invoice->bank_holder)<dt>Holder</dt><dd>{{ $invoice->bank_holder }}</dd>@endif
                    </dl>
                </div>
            @else
                <div style="font-size:.74rem;color:#bbb;font-style:italic">No bank details added</div>
            @endif
        </div>

        {{-- Authorized Signatory --}}
        <div class="footer-cell">
            <div class="footer-title">For {{ $bizName ?: 'Your Business' }}</div>
            <div class="sig-wrap">
                @if($profile->signature)
                    <img src="{{ asset('storage/'.$profile->signature) }}" alt="Signature" class="sig-img">
                @else
                    <div style="height:55px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:flex-end;justify-content:center;padding-bottom:.3rem">
                        <span style="font-size:.68rem;color:#bbb">Authorized Signatory</span>
                    </div>
                @endif
                <div class="sig-line">Authorized Signatory</div>
            </div>
        </div>

    </div>

</div>
</div>

<script>
document.getElementById('wa-btn')?.addEventListener('click', () => {
    fetch('{{ route('invoices.share', $invoice) }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
        body: JSON.stringify({via: 'whatsapp'})
    });
});
</script>
</body>
</html>
