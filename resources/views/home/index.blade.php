@extends('layouts.app')

@section('content')
@php
    $initialCity = $city ?? $cityRecords->first();
    $cityPayload = ($cityRecords ?? collect())->map(function ($c) {
        return [
            'name' => $c->name,
            'lat' => $c->latitude ? (float) $c->latitude : null,
            'lon' => $c->longitude ? (float) $c->longitude : null,
            'district' => $c->agmarknet_district ?: $c->name,
        ];
    })->values();

    $farmingPayload = ($farmings ?? collect())->map(function ($f) {
        return [
            'title' => $f->title,
            'content' => $f->content,
            'url' => route('farming.show', $f->id),
        ];
    })->values();

    $yesterdayMarketMap = ($ratesYesterday ?? collect())
        ->groupBy(function ($r) {
            return strtolower(trim(($r->city ?? ''))) . '|' . strtolower(trim(($r->commodity ?? '')));
        })
        ->map(function ($rows) {
            $first = $rows->first();
            return (float) ($first->modal_price ?? $first->rate ?? 0);
        });

    $dbMarketPayload = ($ratesToday ?? collect())->take(12)->map(function ($r) use ($yesterdayMarketMap) {
        $key = strtolower(trim(($r->city ?? ''))) . '|' . strtolower(trim(($r->commodity ?? '')));
        $current = (float) ($r->modal_price ?? $r->rate ?? 0);
        $previous = $yesterdayMarketMap->get($key);
        $previous = is_numeric($previous) ? (float) $previous : $current;

        return [
            'n' => $r->commodity ?: 'General Rate',
            'u' => ($r->city ?: 'Local mandi') . ' market',
            'min' => (float) ($r->min_price ?? 0),
            'p' => $current,
            'pv' => $previous,
            'max' => (float) ($r->max_price ?? 0),
            'date' => optional($r->price_date)->format('Y-m-d')
                ?: optional($r->updated_at)->format('Y-m-d'),
        ];
    })->values();

    $groupedMarketPayload = collect($marketRates ?? [])->map(function ($commodities, $market) {
        return [
            'market' => $market,
            'commodities' => collect($commodities)->map(function ($rows, $commodity) {
                return [
                    'commodity' => $commodity,
                    'rows' => collect($rows)->map(function ($row) {
                        return [
                            'variety' => $row['variety'] ?? '-',
                            'grade' => $row['grade'] ?? null,
                            'min_price' => $row['min_price'] ?? null,
                            'max_price' => $row['max_price'] ?? null,
                            'modal_price' => $row['modal_price'] ?? null,
                            'date' => $row['date'] ?? null,
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    })->values();

    if ($groupedMarketPayload->isEmpty()) {
        $groupedMarketPayload = ($ratesToday ?? collect())
            ->groupBy(function ($row) {
                return $row->city ?: 'Local Market';
            })
            ->map(function ($rows, $market) {
                return [
                    'market' => $market,
                    'commodities' => $rows
                        ->groupBy(function ($row) {
                            return $row->commodity ?: 'General Rate';
                        })
                        ->map(function ($commodityRows, $commodity) {
                            return [
                                'commodity' => $commodity,
                                'rows' => collect($commodityRows)->map(function ($row) {
                                    $basePrice = (float) ($row->modal_price ?? $row->rate ?? 0);
                                    return [
                                        'variety' => 'Standard',
                                        'grade' => null,
                                        'min_price' => $row->min_price ?? $basePrice,
                                        'max_price' => $row->max_price ?? $basePrice,
                                        'modal_price' => $row->modal_price ?? $row->rate ?? $basePrice,
                                        'date' => optional($row->price_date)->format('Y-m-d')
                                            ?: optional($row->updated_at)->format('Y-m-d')
                                            ?: now()->toDateString(),
                                    ];
                                })->values(),
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();
    }

    $dbNewsPayload = collect($cityNews ?? [])->map(function ($n) {
        return [
            'title' => $n['title'] ?? '',
            'link' => $n['link'] ?? '#',
            'source' => $n['source'] ?? 'News',
            'pubDate' => $n['pubDate'] ?? null,
            'category' => 'City',
        ];
    })->values();
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap');

    .ao-home{--sf:#FF6B00;--gd:#D4A017;--em:#1A936F;--cr:#FFF8F0;--ink:#1A1A2E;--mu:#6B7280;--cd:#FFFFFF;--bd:#F0E8DC;--rd:#E53E3E;--bl:#2B6CB0;font-family:'DM Sans','Noto Sans Devanagari',sans-serif;color:var(--ink);max-width:100%;overflow-x:hidden;margin:0 auto;padding:1rem;width:100%}
    .ao-home *{box-sizing:border-box}
    .ao-inner{max-width:none;margin:0;width:100%}
    @media(max-width:768px){.ao-home{padding:0.75rem}}
    @media(max-width:640px){.ao-home{padding:0.5rem}}
    @media(max-width:480px){.ao-home{padding:0.5rem;font-size:0.95rem}}
    @media(max-width:360px){.ao-home{padding:0.375rem}}
    .ao-city-pill{border:1.4px solid var(--sf);background:#fff;color:var(--sf);border-radius:18px;padding:.26rem .62rem;font-weight:700;font-size:.79rem;cursor:pointer}

    /* Hard guardrail: keep Today's Pulses truly single-column on mobile */
    @media(max-width:900px){
        .main{min-width:0 !important}
        .content{padding:8px !important;overflow-x:hidden}
        .iconbar{display:none !important}
        body:not(.mobile-sidebar-open) .sidebar-panel{display:none !important;transform:translateX(-104%) !important}
    }

    .ao-rates{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:0.58rem;margin:0 0 1rem;width:100%}
    @media(max-width:1024px){.ao-rates{grid-template-columns:repeat(4,minmax(0,1fr));gap:0.5rem}}
    @media(max-width:768px){.ao-rates{grid-template-columns:repeat(3,minmax(0,1fr));gap:0.45rem}}
    @media(max-width:640px){.ao-rates{grid-template-columns:repeat(2,minmax(0,1fr));gap:0.4rem}.ao-rate{padding:0.6rem}}
    @media(max-width:480px){.ao-rates{grid-template-columns:repeat(2,minmax(0,1fr));gap:0.3rem}.ao-rate{padding:0.5rem}}
    @media(max-width:360px){.ao-rates{grid-template-columns:repeat(2,minmax(0,1fr));gap:0.25rem}.ao-rate{padding:0.4rem}}
    .ao-rate{background:#fff;border:1px solid var(--bd);border-radius:12px;padding:0.72rem 0.82rem;position:relative;min-width:0;flex-shrink:0;display:flex;flex-direction:column;justify-content:space-between}
    .ao-rate:before{content:'';position:absolute;left:0;right:0;top:0;height:3px;border-radius:12px 12px 0 0;background:linear-gradient(90deg,var(--sf),var(--gd))}
    .ao-rate-l{font-size:clamp(0.55rem,1.5vw,0.64rem);text-transform:uppercase;letter-spacing:0.08em;color:var(--mu);font-weight:700;line-height:1.2}
    .ao-rate-v{font-family:'Playfair Display',serif;font-size:clamp(0.9rem,2vw,1.16rem);font-weight:700;margin:0.2rem 0;line-height:1.1}
    .ao-rate-s{font-size:clamp(0.5rem,1.2vw,0.64rem);color:var(--mu);font-family:'DM Mono',monospace}
    @media(max-width:480px){.ao-rate{padding:0.5rem 0.6rem}.ao-rate-l{font-size:0.5rem}.ao-rate-v{font-size:0.85rem}.ao-rate-s{font-size:0.45rem}}
    @media(max-width:360px){.ao-rate{padding:0.4rem 0.5rem}.ao-rate-l{font-size:0.45rem}.ao-rate-v{font-size:0.75rem}.ao-rate-s{font-size:0.4rem}}

    .ao-ticker{background:var(--ink);color:#fff;border-radius:10px;overflow:hidden;padding:.34rem 0;margin-bottom:1rem;width:100%;max-width:100%}
    .ao-ticker-track{display:flex;gap:1.6rem;white-space:nowrap;animation:ao-ticker 38s linear infinite;padding:0 .5rem;min-width:100%}
    @media(max-width:480px){.ao-ticker-track{gap:1rem;padding:0 .35rem}}
    @media(max-width:360px){.ao-ticker-track{gap:0.8rem;padding:0 .25rem}}
    @keyframes ao-ticker{from{transform:translateX(0)}to{transform:translateX(-50%)}}
    .ao-ti{font-size:.75rem}
    .ao-ti b{font-family:'DM Mono',monospace}

    .ao-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,320px);gap:clamp(0.75rem,2vw,1rem);align-items:start;width:100%;max-width:100%;margin:0}
    @media(max-width:1024px){.ao-grid{grid-template-columns:1fr 260px;gap:0.85rem}}
    @media(max-width:900px){.ao-grid{grid-template-columns:1fr 240px;gap:0.75rem}}
    @media(max-width:768px){.ao-grid{grid-template-columns:1fr;gap:0.75rem}}
    @media(max-width:640px){.ao-grid{gap:0.6rem}}
    @media(max-width:480px){.ao-grid{gap:0.5rem}}
    @media(max-width:360px){.ao-grid{gap:0.4rem}}
    .ao-card{background:#fff;border:1px solid var(--bd);border-radius:clamp(10px,2vw,14px);overflow:hidden;margin-bottom:clamp(0.75rem,2vw,1rem);min-width:0;width:100%;max-width:100%}
    @media(max-width:640px){.ao-card{border-radius:12px;margin-bottom:0.8rem}}
    @media(max-width:480px){.ao-card{border-radius:10px;margin-bottom:0.65rem}}
    @media(max-width:360px){.ao-card{border-radius:8px;margin-bottom:0.5rem}}
    .ao-card-h{display:flex;align-items:center;justify-content:space-between;padding:clamp(0.65rem,2vw,0.92rem) clamp(0.75rem,2vw,1rem);border-bottom:1px solid var(--bd);gap:0.5rem;flex-wrap:wrap}
    .ao-card-t{font-family:'Playfair Display',serif;font-weight:700;font-size:clamp(0.85rem,2vw,0.95rem);line-height:1.25}
    .ao-badge{font-size:clamp(0.5rem,1.2vw,0.62rem);padding:clamp(0.1rem,0.5vw,0.16rem) clamp(0.35rem,1vw,0.5rem);border-radius:8px;background:rgba(255,107,0,.1);color:var(--sf);font-weight:700}
    @media(max-width:640px){.ao-card-h{padding:0.7rem 0.8rem}.ao-card-t{font-size:0.8rem}.ao-badge{font-size:0.5rem;padding:0.12rem 0.35rem}}
    @media(max-width:480px){.ao-card-h{padding:0.6rem 0.65rem}.ao-card-t{font-size:0.75rem}.ao-badge{font-size:0.45rem;padding:0.1rem 0.3rem}}
    @media(max-width:360px){.ao-card-h{padding:0.5rem 0.55rem}.ao-card-t{font-size:0.7rem}.ao-badge{font-size:0.4rem;padding:0.08rem 0.25rem}}
    .ao-list-item{display:flex;gap:clamp(0.5rem,1.5vw,0.65rem);padding:clamp(0.55rem,1.5vw,0.75rem) clamp(0.75rem,2vw,1rem);border-bottom:1px solid var(--bd);text-decoration:none;color:inherit;min-width:0}
    .ao-list-item:last-child{border-bottom:0}
    .ao-list-item:hover{background:rgba(255,107,0,.04)}
    .ao-nno{font-family:'Playfair Display',serif;color:#dfd6cc;font-size:clamp(0.95rem,2vw,1.2rem);font-weight:900;line-height:1}
    .ao-nt{font-size:clamp(0.7rem,1.8vw,0.82rem);font-weight:600;line-height:1.4;word-break:break-word;overflow-wrap:anywhere}
    .ao-nm{font-size:clamp(0.55rem,1.3vw,0.66rem);color:var(--mu);margin-top:0.2rem}
    @media(max-width:640px){.ao-list-item{padding:0.6rem 0.8rem}.ao-nt{font-size:0.75rem}.ao-nm{font-size:0.55rem}}
    @media(max-width:480px){.ao-list-item{padding:0.5rem 0.65rem;gap:0.4rem}.ao-nt{font-size:0.7rem}.ao-nno{font-size:0.95rem}.ao-nm{font-size:0.5rem}}
    @media(max-width:360px){.ao-list-item{padding:0.45rem 0.55rem;gap:0.3rem}.ao-nt{font-size:0.65rem}.ao-nno{font-size:0.85rem}.ao-nm{font-size:0.45rem}}

    .ao-table-h,.ao-row{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:.5rem;padding:.62rem 1rem;align-items:center}
    .ao-table-h{font-size:.64rem;color:var(--mu);font-weight:700;text-transform:uppercase;letter-spacing:.08em;border-bottom:1px solid var(--bd)}
    .ao-row{font-size:.81rem;border-bottom:1px solid var(--bd)}
    @media(max-width:768px){.ao-table-h,.ao-row{grid-template-columns:1.8fr 1fr 1fr 1fr;gap:.4rem;padding:.5rem .8rem}}
    @media(max-width:640px){.ao-table-h,.ao-row{gap:.35rem;font-size:.75rem}}
    @media(max-width:480px){.ao-table-h,.ao-row{grid-template-columns:1.5fr 1fr 0.8fr 0.8fr;gap:.3rem;padding:.45rem .6rem;font-size:.65rem}}
    @media(max-width:360px){.ao-table-h,.ao-row{grid-template-columns:1.4fr 1fr 0.7fr 0.7fr;gap:.25rem;padding:.4rem .5rem;font-size:.6rem}}
    .ao-row:last-child{border-bottom:0}
    .ao-p{text-align:right;font-family:'DM Mono',monospace}
    .ao-c{text-align:right;font-weight:700}
    .ao-up{color:var(--em)} .ao-dn{color:var(--rd)} .ao-fl{color:var(--mu)}
    .ao-market-wrap{padding:.85rem 1rem;overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%;max-width:100%}
    @media(max-width:480px){.ao-market-wrap{padding:.65rem .7rem}}
    @media(max-width:360px){.ao-market-wrap{padding:.55rem .6rem}}
    .ao-market-wrap + .ao-market-wrap{border-top:1px solid var(--bd)}
    .ao-market-name{margin:0 0 .6rem;color:#2e7d32;font-size:.98rem;font-weight:700}
    .ao-market-table{width:100%;min-width:620px;border-collapse:collapse;font-size:.82rem;background:#fff;border:1px solid var(--bd);border-radius:10px;overflow:hidden}
    @media(max-width:768px){.ao-market-table{min-width:500px;font-size:.75rem}}
    @media(max-width:480px){.ao-market-table{min-width:420px;font-size:.65rem}}
    @media(max-width:360px){.ao-market-table{min-width:360px;font-size:.6rem}}
    .ao-market-table th{background:#fff7ef;color:#6B7280;text-align:left;padding:.52rem;border-bottom:1px solid var(--bd);font-size:.68rem;letter-spacing:.06em;text-transform:uppercase}
    .ao-market-table td{padding:.52rem;border-bottom:1px solid #f3ece3;color:#243852;vertical-align:top}
    .ao-market-table tr:last-child td{border-bottom:0}
    .ao-market-modal{font-weight:700;color:#007bff}
    .ao-market-table td.ao-up{color:var(--em) !important;font-weight:700}
    .ao-market-table td.ao-dn{color:var(--rd) !important;font-weight:700}
    .ao-market-table td.ao-fl{color:var(--mu) !important;font-weight:700}
    @media(max-width:520px){
        .ao-market-wrap{padding:.55rem .55rem;overflow-x:hidden}
        .ao-market-name{font-size:.84rem;margin:0 0 .45rem}
        .ao-market-table{min-width:100% !important;width:100%;table-layout:fixed;font-size:.68rem}
        .ao-market-table th,.ao-market-table td{padding:.42rem .28rem;white-space:normal;word-break:break-word;overflow-wrap:anywhere;line-height:1.25}
        .ao-market-table th:nth-child(2),
        .ao-market-table th:nth-child(3),
        .ao-market-table th:nth-child(4),
        .ao-market-table th:nth-child(6),
        .ao-market-table td:nth-child(2),
        .ao-market-table td:nth-child(3),
        .ao-market-table td:nth-child(4),
        .ao-market-table td:nth-child(6){display:none}
        .ao-market-table th:nth-child(1), .ao-market-table td:nth-child(1){width:44%}
        .ao-market-table th:nth-child(5), .ao-market-table td:nth-child(5){width:18%;text-align:right}
        .ao-market-table th:nth-child(7), .ao-market-table td:nth-child(7){width:19%;text-align:right}
        .ao-market-table th:nth-child(8), .ao-market-table td:nth-child(8){width:19%;text-align:right}
    }
    @media(max-width:360px){
        .ao-market-table{font-size:.64rem}
        .ao-market-table th,.ao-market-table td{padding:.38rem .28rem}
    }

    .ao-weather{background:linear-gradient(135deg,#1A1A2E,#16213E,#0F3460);color:#fff;border-radius:14px;overflow:hidden;margin-bottom:1rem;width:100%;max-width:100%}
    @media(max-width:768px){.ao-weather{border-radius:12px}}
    @media(max-width:480px){.ao-weather{border-radius:10px}}
    .ao-w-top{padding:1rem;border-bottom:1px solid rgba(255,255,255,.12)}
    @media(max-width:480px){.ao-w-top{padding:.8rem .75rem}}
    @media(max-width:360px){.ao-w-top{padding:.7rem .6rem}}
    .ao-w-city{font-size:.73rem;opacity:.62}
    @media(max-width:480px){.ao-w-city{font-size:.65rem}}
    @media(max-width:360px){.ao-w-city{font-size:.6rem}}
    .ao-w-temp{font-family:'Playfair Display',serif;font-size:2.3rem;line-height:1.1}
    @media(max-width:768px){.ao-w-temp{font-size:2rem}}
    @media(max-width:480px){.ao-w-temp{font-size:1.7rem}}
    @media(max-width:360px){.ao-w-temp{font-size:1.4rem}}
    .ao-w-desc{font-size:.82rem;opacity:.78}
    @media(max-width:480px){.ao-w-desc{font-size:.7rem}}
    @media(max-width:360px){.ao-w-desc{font-size:.6rem}}
    .ao-w-grid{display:grid;grid-template-columns:1fr 1fr;gap:.45rem;padding:.8rem 1rem;width:100%;max-width:100%}
    @media(max-width:768px){.ao-w-grid{grid-template-columns:1fr 1fr;gap:.4rem;padding:.7rem .8rem}}
    @media(max-width:420px){.ao-w-grid{grid-template-columns:1fr 1fr;gap:.35rem;padding:.6rem .7rem}}
    @media(max-width:360px){.ao-w-grid{grid-template-columns:1fr}}
    .ao-w-box{background:rgba(255,255,255,.08);border-radius:8px;padding:.48rem .6rem}
    @media(max-width:480px){.ao-w-box{padding:.4rem .5rem;border-radius:6px}}
    @media(max-width:360px){.ao-w-box{padding:.35rem .45rem}}
    .ao-w-box small{display:block;opacity:.58;font-size:.6rem;letter-spacing:.06em}
    @media(max-width:480px){.ao-w-box small{font-size:.55rem}}
    @media(max-width:360px){.ao-w-box small{font-size:.5rem}}
    .ao-w-box strong{font-size:.83rem}
    @media(max-width:480px){.ao-w-box strong{font-size:.7rem}}
    @media(max-width:360px){.ao-w-box strong{font-size:.6rem}}
    .ao-fc{padding:.75rem 1rem;border-top:1px solid rgba(255,255,255,.12)}
    @media(max-width:480px){.ao-fc{padding:.6rem .8rem}}
    @media(max-width:360px){.ao-fc{padding:.5rem .6rem}}
    .ao-f{display:flex;justify-content:space-between;font-size:.74rem;padding:.25rem 0;border-bottom:1px solid rgba(255,255,255,.08)}
    @media(max-width:480px){.ao-f{font-size:.65rem;padding:.2rem 0}}
    @media(max-width:360px){.ao-f{font-size:.6rem}}
    .ao-f:last-child{border-bottom:0}

    .ao-mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:clamp(0.3rem,1.5vw,0.45rem);padding:clamp(0.6rem,2vw,0.8rem)}
    .ao-mini{background:var(--cr);border:1px solid var(--bd);border-radius:9px;padding:clamp(0.4rem,1.5vw,0.55rem) clamp(0.5rem,1.5vw,0.68rem)}
    .ao-mini div:first-child{font-size:clamp(0.55rem,1.3vw,0.64rem);color:var(--mu);font-weight:700}
    .ao-mini div:nth-child(2){font-family:'DM Mono',monospace;font-size:clamp(0.7rem,1.8vw,0.84rem);font-weight:700;margin:clamp(0.05rem,0.5vw,0.1rem) 0}
    .ao-mini div:last-child{font-size:clamp(0.55rem,1.3vw,0.68rem);font-weight:700}
    @media(max-width:640px){.ao-mini-grid{grid-template-columns:1fr 1fr;gap:0.3rem;padding:0.6rem}.ao-mini{padding:0.4rem 0.5rem}}
    @media(max-width:480px){.ao-mini-grid{grid-template-columns:1fr 1fr;gap:0.25rem;padding:0.5rem}.ao-mini{padding:0.35rem 0.45rem}.ao-mini div:first-child{font-size:0.5rem}.ao-mini div:nth-child(2){font-size:0.7rem}.ao-mini div:last-child{font-size:0.5rem}}
    @media(max-width:360px){.ao-mini-grid{grid-template-columns:1fr;padding:0.4rem}.ao-mini{padding:0.3rem 0.4rem}.ao-mini div:first-child{font-size:0.45rem}.ao-mini div:nth-child(2){font-size:0.6rem}.ao-mini div:last-child{font-size:0.45rem}}

    .ao-fx-row{display:flex;justify-content:space-between;align-items:center;padding:.6rem 1rem;border-bottom:1px solid #F0E8DC;font-size:.82rem;gap:.5rem}
    .ao-fx-code{font-weight:700;white-space:nowrap}
    .ao-fx-val{font-family:'DM Mono',monospace;white-space:nowrap}
    .ao-ind-row{display:flex;justify-content:space-between;align-items:center;padding:.72rem 1rem;border-bottom:1px solid #F0E8DC;gap:.5rem}
    .ao-ind-name{font-weight:700;font-size:.84rem}
    .ao-ind-right{text-align:right}
    .ao-ind-price{font-family:'DM Mono',monospace;font-weight:700}
    .ao-ind-change{font-size:.69rem;font-weight:700}
    .ao-farm-ico{width:32px;height:32px;border-radius:8px;background:rgba(26,147,111,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ao-farm-title{font-size:.82rem;font-weight:700}
    .ao-farm-copy{font-size:.74rem;color:#6B7280;line-height:1.4}
    .ao-soft-empty{padding:.8rem 1rem;color:#6B7280}
    .ao-city-empty{grid-column:1/-1;color:#6B7280;padding:.8rem}
    .ao-row-sub{font-size:.65rem;color:#6B7280}
    .ao-mini-wide{grid-column:1/-1}
    @media(max-width:480px){
        .ao-fx-row{padding:.5rem .7rem;font-size:.72rem}
        .ao-ind-row{padding:.55rem .7rem}
        .ao-ind-name{font-size:.76rem}
        .ao-ind-price{font-size:.74rem}
        .ao-ind-change{font-size:.62rem}
        .ao-farm-ico{width:28px;height:28px}
        .ao-farm-title{font-size:.74rem}
        .ao-farm-copy{font-size:.66rem}
        .ao-soft-empty{padding:.65rem .75rem;font-size:.72rem}
        .ao-row-sub{font-size:.58rem}
    }
    @media(max-width:360px){
        .ao-fx-row{padding:.45rem .6rem;font-size:.68rem}
        .ao-ind-row{padding:.5rem .6rem}
        .ao-ind-name{font-size:.7rem}
        .ao-ind-price{font-size:.68rem}
        .ao-ind-change{font-size:.58rem}
        .ao-soft-empty{padding:.55rem .65rem;font-size:.66rem}
        .ao-row-sub{font-size:.52rem}
    }

    .ao-city-overlay{position:fixed;inset:0;background:rgba(26,26,46,.55);display:none;align-items:flex-start;justify-content:center;padding-top:80px;z-index:999;overflow-y:auto}
    @media(max-width:768px){.ao-city-overlay{padding-top:60px}}
    @media(max-width:480px){.ao-city-overlay{padding-top:40px}}
    .ao-city-overlay.open{display:flex}
    .ao-city-modal{background:#fff;border-radius:14px;width:560px;max-width:95vw;max-height:72vh;display:flex;flex-direction:column;overflow:hidden}
    @media(max-width:640px){.ao-city-modal{max-width:92vw;max-height:70vh;width:100%}}
    @media(max-width:480px){.ao-city-modal{max-width:90vw;max-height:65vh;width:100%;border-radius:10px}}
    @media(max-width:360px){.ao-city-modal{max-width:88vw;max-height:60vh}}
    .ao-city-h{padding:.9rem 1rem;border-bottom:1px solid var(--bd);display:flex;gap:.5rem;align-items:center}
    .ao-city-h input{flex:1;border:1px solid var(--bd);border-radius:9px;padding:.45rem .6rem}
    .ao-city-b{padding:.75rem;overflow:auto;display:grid;grid-template-columns:repeat(3,1fr);gap:.35rem}
    .ao-city-btn{border:1px solid var(--bd);background:#fff;border-radius:8px;padding:.44rem .5rem;text-align:left;cursor:pointer;font-size:.78rem}
    .ao-city-btn.on{border-color:var(--sf);background:rgba(255,107,0,.08);color:var(--sf);font-weight:700}

    @media(max-width:640px){.ao-city-b{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:480px){.ao-city-b{grid-template-columns:1fr}}
</style>

<div class="ao-home">
    <div class="ao-inner">
    <div class="ao-rates">
        <div class="ao-rate"><div class="ao-rate-l" data-k="g24">24K Gold /g</div><div class="ao-rate-v" id="aoG24">—</div><div class="ao-rate-s" id="aoG24s">—</div></div>
        <div class="ao-rate"><div class="ao-rate-l" data-k="g22">22K Gold /g</div><div class="ao-rate-v" id="aoG22">—</div><div class="ao-rate-s" id="aoG22s">—</div></div>
        <div class="ao-rate"><div class="ao-rate-l" data-k="g18">18K Gold /g</div><div class="ao-rate-v" id="aoG18">—</div><div class="ao-rate-s" id="aoG18s">—</div></div>
        <div class="ao-rate"><div class="ao-rate-l" data-k="sg">Silver /g</div><div class="ao-rate-v" id="aoSG">—</div><div class="ao-rate-s" id="aoSGs">—</div></div>
        <div class="ao-rate"><div class="ao-rate-l" data-k="s10">Silver /10g</div><div class="ao-rate-v" id="aoS10">—</div><div class="ao-rate-s" id="aoS10s">—</div></div>
        <div class="ao-rate"><div class="ao-rate-l" data-k="skg">Silver /kg</div><div class="ao-rate-v" id="aoSKG">—</div><div class="ao-rate-s" id="aoSKGs">—</div></div>
    </div>

    <div class="ao-ticker"><div class="ao-ticker-track" id="aoTicker"><span class="ao-ti">Loading...</span></div></div>

    <div class="ao-grid">
        <div>
            <div class="ao-card">
                <div class="ao-card-h"><div class="ao-card-t" id="aoNewsTitle">City News</div><span class="ao-badge" id="aoLocalLbl">LOCAL</span></div>
                <div id="aoNews"></div>
            </div>

            <div class="ao-card">
                <div class="ao-card-h"><div class="ao-card-t" id="aoMandiTitle">Market Commodity Prices</div><span class="ao-badge ao-badge-live" id="aoLiveMandi">LIVE</span></div>
                <div class="ao-table-h" id="aoMandiHeader"><div id="aoHc">Commodity</div><div class="ao-p" id="aoHp">Price</div><div class="ao-c" id="aoHch">Change</div><div class="ao-c" id="aoHt">Trend</div></div>
                <div id="aoMandi"></div>
            </div>

            <div class="ao-card">
                <div class="ao-card-h"><div class="ao-card-t" id="aoCryptoTitle">Cryptocurrency</div><span class="ao-badge ao-badge-live" id="aoLiveCrypto">LIVE</span></div>
                <div class="ao-mini-grid" id="aoCrypto"></div>
            </div>

            <div class="ao-card">
                <div class="ao-card-h"><div class="ao-card-t" id="aoFxTitle">Currency Exchange</div><span class="ao-badge ao-badge-live" id="aoLiveFx">LIVE</span></div>
                <div id="aoFx"></div>
            </div>

            <div class="ao-card">
                <div class="ao-card-h"><div class="ao-card-t" id="aoFarmTitle">Farming Notes</div><span class="ao-badge" id="aoSeasonLbl">SEASONAL</span></div>
                <div id="aoFarm"></div>
            </div>
        </div>

        <div>
            <div class="ao-weather">
                <div class="ao-w-top">
                    <div class="ao-w-city" id="aoWCity">{{ city_display_name($initialCity?->name ?? 'Washim') }}, MH</div>
                    <div class="ao-w-temp" id="aoWTemp">—°C</div>
                    <div class="ao-w-desc" id="aoWDesc">Fetching weather...</div>
                </div>
                <div class="ao-w-grid">
                    <div class="ao-w-box"><small id="aoLH">HUMIDITY</small><strong id="aoWH">—%</strong></div>
                    <div class="ao-w-box"><small id="aoLW">WIND</small><strong id="aoWW">— km/h</strong></div>
                    <div class="ao-w-box"><small id="aoLF">FEELS LIKE</small><strong id="aoWF">—°C</strong></div>
                    <div class="ao-w-box"><small id="aoLU">UV INDEX</small><strong id="aoWU">—</strong></div>
                </div>
                <div class="ao-fc" id="aoFC"></div>
            </div>

            <div class="ao-card">
                <div class="ao-card-h"><div class="ao-card-t" id="aoIndiaTitle">Indian Markets</div><span class="ao-badge">NSE/BSE</span></div>
                <div id="aoIndia"></div>
            </div>
        </div>
    </div>
</div>
    </div>

<div class="ao-city-overlay" id="aoCityOverlay" onclick="if(event.target===this) closeAoCity()">
    <div class="ao-city-modal">
        <div class="ao-city-h">
            <strong id="aoCityModalTitle">Choose Your City</strong>
            <input type="text" id="aoCitySearch" placeholder="Search city...">
            <button type="button" class="ao-city-pill" onclick="closeAoCity()">✕</button>
        </div>
        <div class="ao-city-b" id="aoCityGrid"></div>
    </div>
</div>

<script>
(() => {
    const TR = {
        en:{tag:'Aajcha bhav, aajcha offer',news:'City News',local:'LOCAL',mandi:'Market Commodity Prices',crypto:'Cryptocurrency',fx:'Currency Exchange',farm:'Farming Notes',season:'SEASONAL',india:'Indian Markets',hc:'Commodity',hp:'Price (/q)',hch:'Change',ht:'Trend',h:'HUMIDITY',w:'WIND',f:'FEELS LIKE',u:'UV INDEX',choose:'Choose Your City',g24:'24K Gold /g',g22:'22K Gold /g',g18:'18K Gold /g',sg:'Silver /g',s10:'Silver /10g',skg:'Silver /kg',live:'LIVE',loading:'Loading live rates...',noCity:'No city found.',weatherLive:'Live weather data',weatherUnavailable:'Weather unavailable',forecastUnavailable:'Forecast unavailable',noNews:'No local news available.',noMarketWise:'No market-wise data available.',noMarket:'No market data.',cryptoUnavailable:'Crypto feed unavailable.',forexUnavailable:'Forex feed unavailable.',indiaUnavailable:'Indian market feed unavailable right now.',commodity:'Commodity',variety:'Variety',min:'Min',max:'Max',modal:'Modal',date:'Date',change:'Change',trend:'Trend',liveRatesUnavailable:'Live rates temporarily unavailable.'},
        mr:{tag:'आजचा भाव, आजचा ऑफर',news:'शहर बातम्या',local:'स्थानिक',mandi:'बाजार भाव',crypto:'क्रिप्टोकरन्सी',fx:'परकीय चलन',farm:'शेती सल्ला',season:'हंगाम',india:'भारतीय बाजार',hc:'माल',hp:'भाव (/क्विं)',hch:'बदल',ht:'कल',h:'आर्द्रता',w:'वारा',f:'जाणवते',u:'UV निर्देशांक',choose:'तुमचे शहर निवडा',g24:'२४K सोने /ग्रॅम',g22:'२२K सोने /ग्रॅम',g18:'१८K सोने /ग्रॅम',sg:'चांदी /ग्रॅम',s10:'चांदी /१०ग्रॅम',skg:'चांदी /किलो',live:'लाईव्ह',loading:'लाईव्ह दर लोड होत आहेत...',noCity:'शहर सापडले नाही.',weatherLive:'लाईव्ह हवामान माहिती',weatherUnavailable:'हवामान उपलब्ध नाही',forecastUnavailable:'अंदाज उपलब्ध नाही',noNews:'स्थानिक बातम्या उपलब्ध नाहीत.',noMarketWise:'बाजारनिहाय डेटा उपलब्ध नाही.',noMarket:'बाजार डेटा उपलब्ध नाही.',cryptoUnavailable:'क्रिप्टो फीड उपलब्ध नाही.',forexUnavailable:'फॉरेक्स फीड उपलब्ध नाही.',indiaUnavailable:'भारतीय बाजार फीड सध्या उपलब्ध नाही.',commodity:'माल',variety:'प्रकार',min:'किमान',max:'कमाल',modal:'मोडल',date:'दिनांक',change:'बदल',trend:'कल',liveRatesUnavailable:'लाईव्ह दर तात्पुरते उपलब्ध नाहीत.'},
        hi:{tag:'आज का भाव, आज का ऑफर',news:'शहर समाचार',local:'स्थानीय',mandi:'बाज़ार भाव',crypto:'क्रिप्टोकरेंसी',fx:'विदेशी मुद्रा',farm:'खेती सलाह',season:'मौसमी',india:'भारतीय बाज़ार',hc:'वस्तु',hp:'भाव (/क्विं)',hch:'बदलाव',ht:'रुझान',h:'नमी',w:'हवा',f:'महसूस होता है',u:'UV सूचकांक',choose:'अपना शहर चुनें',g24:'२४K सोना /ग्राम',g22:'२२K सोना /ग्राम',g18:'१८K सोना /ग्राम',sg:'चांदी /ग्राम',s10:'चांदी /१०ग्राम',skg:'चांदी /किलो',live:'लाइव',loading:'लाइव रेट लोड हो रहे हैं...',noCity:'कोई शहर नहीं मिला।',weatherLive:'लाइव मौसम डेटा',weatherUnavailable:'मौसम उपलब्ध नहीं',forecastUnavailable:'पूर्वानुमान उपलब्ध नहीं',noNews:'स्थानीय समाचार उपलब्ध नहीं।',noMarketWise:'बाज़ार-वार डेटा उपलब्ध नहीं।',noMarket:'बाज़ार डेटा उपलब्ध नहीं।',cryptoUnavailable:'क्रिप्टो फीड उपलब्ध नहीं।',forexUnavailable:'फॉरेक्स फीड उपलब्ध नहीं।',indiaUnavailable:'भारतीय बाज़ार फीड अभी उपलब्ध नहीं है।',commodity:'वस्तु',variety:'किस्म',min:'न्यूनतम',max:'अधिकतम',modal:'मोडल',date:'तारीख',change:'बदलाव',trend:'रुझान',liveRatesUnavailable:'लाइव रेट अस्थायी रूप से उपलब्ध नहीं हैं।'}
    };

    const CITY_DATA = @json($cityPayload);
    const DB_FARM = @json($farmingPayload);
    const DB_MARKET = @json($dbMarketPayload);
    const SERVER_GROUPED_MARKET = @json($groupedMarketPayload);
    const MARKET_SOURCE = @json($marketSource ?? 'database');
    const DB_NEWS = @json($dbNewsPayload);
    const SERVER_METALS = @json($metals ?? []);
    const SERVER_METALS_META = @json($metalsMeta ?? []);
    const SERVER_INDIAN_MARKETS = @json($indianMarkets ?? []);
    const AGMARK_RESOURCE_ID = '35985678-0d79-46b4-9ed6-6f13308a1d24';
    const AGMARK_API_KEY = '579b464db66ec23bdd000001c20c0593c63b4ae97757e11d2e3f369e';
    const FALLBACK_WEATHER = { days:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'], mr:['रवि','सोम','मंगळ','बुध','गुरु','शुक्र','शनि'], hi:['रवि','सोम','मंगल','बुध','गुरु','शुक्र','शनि'] };

    let lang = @json(app()->getLocale() ?: 'en');
    let city = {
        name: @json($initialCity?->name ?? 'Washim'),
        lat: Number(@json($initialCity?->latitude ?? 20.1035)) || 20.1035,
        lon: Number(@json($initialCity?->longitude ?? 77.1478)) || 77.1478,
        district: @json($initialCity?->agmarknet_district ?: $initialCity?->name ?: 'Washim'),
    };

    const inr = (n) => '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 2 });
    const text = (k) => (TR[lang] && TR[lang][k]) ? TR[lang][k] : TR.en[k];
    const esc = (s) => String(s || '').replace(/[&<>'"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[m]));

    function applyLang(){
        document.getElementById('aoNewsTitle').textContent = `${text('news')} — ${city.name}`;
        document.getElementById('aoLocalLbl').textContent = text('local');
        document.getElementById('aoMandiTitle').textContent = text('mandi');
        document.getElementById('aoCryptoTitle').textContent = text('crypto');
        document.getElementById('aoFxTitle').textContent = text('fx');
        document.getElementById('aoFarmTitle').textContent = text('farm');
        document.getElementById('aoSeasonLbl').textContent = text('season');
        document.getElementById('aoIndiaTitle').textContent = text('india');
        document.getElementById('aoCityModalTitle').textContent = text('choose');
        const liveMandi = document.getElementById('aoLiveMandi');
        const liveCrypto = document.getElementById('aoLiveCrypto');
        const liveFx = document.getElementById('aoLiveFx');
        if (liveMandi) liveMandi.textContent = text('live');
        if (liveCrypto) liveCrypto.textContent = text('live');
        if (liveFx) liveFx.textContent = text('live');
        document.getElementById('aoHc').textContent = text('hc');
        document.getElementById('aoHp').textContent = text('hp');
        document.getElementById('aoHch').textContent = text('hch');
        document.getElementById('aoHt').textContent = text('ht');
        document.getElementById('aoLH').textContent = text('h');
        document.getElementById('aoLW').textContent = text('w');
        document.getElementById('aoLF').textContent = text('f');
        document.getElementById('aoLU').textContent = text('u');
        ['g24','g22','g18','sg','s10','skg'].forEach(k => {
            const el = document.querySelector(`[data-k="${k}"]`);
            if (el) el.textContent = text(k);
        });
    }

    function renderCityModal(filter=''){
        const q = filter.toLowerCase().trim();
        const grid = document.getElementById('aoCityGrid');
        const list = CITY_DATA.filter(c => !q || c.name.toLowerCase().includes(q));
        grid.innerHTML = list.map(c => `
            <button type="button" class="ao-city-btn ${c.name===city.name?'on':''}" data-city="${esc(c.name)}">${esc(c.name)}</button>
        `).join('') || `<div class="ao-city-empty">${esc(text('noCity'))}</div>`;

        grid.querySelectorAll('.ao-city-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const found = CITY_DATA.find(c => c.name === btn.dataset.city);
                if (!found) return;
                city = {
                    name: found.name,
                    lat: Number(found.lat) || city.lat,
                    lon: Number(found.lon) || city.lon,
                    district: found.district || found.name || city.name,
                };
                document.getElementById('aoWCity').textContent = `${city.name}, MH`;
                document.getElementById('aoNewsTitle').textContent = `${text('news')} — ${city.name}`;
                closeAoCity();
                await Promise.allSettled([fetchWeather(), fetchNews(), fetchCommodities()]);
            });
        });
    }

    window.openAoCity = function(){
        document.getElementById('aoCityOverlay').classList.add('open');
        const inp = document.getElementById('aoCitySearch');
        inp.value = '';
        renderCityModal('');
        setTimeout(() => inp.focus(), 80);
    }

    window.closeAoCity = function(){
        document.getElementById('aoCityOverlay').classList.remove('open');
    }

    document.getElementById('aoCitySearch').addEventListener('input', (e) => renderCityModal(e.target.value || ''));

    function renderMetalsFromPerGram(g24, sg, usdInr = null, goldOz = null, silverOz = null){
        const g22 = g24 * (22 / 24);
        const g18 = g24 * (18 / 24);

        document.getElementById('aoG24').textContent = inr(g24.toFixed(0));
        document.getElementById('aoG22').textContent = inr(g22.toFixed(0));
        document.getElementById('aoG18').textContent = inr(g18.toFixed(0));
        document.getElementById('aoSG').textContent = inr(sg.toFixed(2));
        document.getElementById('aoS10').textContent = inr((sg * 10).toFixed(0));
        document.getElementById('aoSKG').textContent = inr((sg * 1000).toFixed(0));

        document.getElementById('aoG24s').textContent = `Per 10g: ${inr((g24 * 10 * 1.03).toFixed(0))} +GST`;
        document.getElementById('aoG22s').textContent = `Per 10g: ${inr((g22 * 10 * 1.03).toFixed(0))} +GST`;
        document.getElementById('aoG18s').textContent = `Per 10g: ${inr((g18 * 10 * 1.03).toFixed(0))} +GST`;
        document.getElementById('aoSGs').textContent = `Retail: ${inr(sg.toFixed(2))}`;
        document.getElementById('aoS10s').textContent = `Retail: ${inr((sg * 10).toFixed(0))}`;
        document.getElementById('aoSKGs').textContent = `Retail: ${inr((sg * 1000).toFixed(0))}`;

        const ticker = [
            `🥇 24K/g <b>${inr(g24.toFixed(0))}</b>`,
            `🥈 Silver/g <b>${inr(sg.toFixed(2))}</b>`,
            usdInr ? `💵 USD/INR <b>${inr(Number(usdInr).toFixed(2))}</b>` : null,
            goldOz ? `🪙 Gold/oz <b>$${Number(goldOz).toFixed(2)}</b>` : null,
            silverOz ? `🥈 Silver/oz <b>$${Number(silverOz).toFixed(2)}</b>` : null
        ].filter(Boolean);

        document.getElementById('aoTicker').innerHTML = [...ticker, ...ticker].map(t => `<span class="ao-ti">${t}</span>`).join('');
    }

    async function fetchMetals(){
        const serverGold = Number(SERVER_METALS?.gold || 0);
        const serverSilver = Number(SERVER_METALS?.silver || 0);
        const serverUsdInr = Number((SERVER_METALS_META || {}).usd_to_inr || 0) || null;

        if (serverGold > 0 && serverSilver > 0) {
            renderMetalsFromPerGram(serverGold, serverSilver, serverUsdInr, null, null);
            return;
        }

        try{
            const [fxRes, mRes] = await Promise.all([
                fetch('https://api.exchangerate-api.com/v4/latest/USD'),
                fetch('https://api.metals.live/v1/spot')
            ]);
            const fx = await fxRes.json();
            const metals = await mRes.json();
            const usdInr = fx?.rates?.INR || 83.5;
            let goldOz = 0, silverOz = 0;
            if (Array.isArray(metals)) {
                metals.forEach(item => { if (item.gold) goldOz = Number(item.gold); if (item.silver) silverOz = Number(item.silver); });
            }
            if (!goldOz || !silverOz) throw new Error('metals missing');

            const g24 = (goldOz / 31.1035) * usdInr;
            const sg = (silverOz / 31.1035) * usdInr;
            renderMetalsFromPerGram(g24, sg, usdInr, goldOz, silverOz);
        } catch {
            document.getElementById('aoTicker').innerHTML = `<span class="ao-ti">${esc(text('liveRatesUnavailable'))}</span>`;
        }
    }

    async function fetchWeather(){
        try{
            const r = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${city.lat}&longitude=${city.lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,uv_index&daily=temperature_2m_max,temperature_2m_min&forecast_days=4&timezone=Asia%2FKolkata`);
            const d = await r.json();
            const c = d.current || {};
            document.getElementById('aoWTemp').textContent = `${Math.round(c.temperature_2m ?? 0)}°C`;
            document.getElementById('aoWDesc').textContent = text('weatherLive');
            document.getElementById('aoWH').textContent = `${Math.round(c.relative_humidity_2m ?? 0)}%`;
            document.getElementById('aoWW').textContent = `${Math.round(c.wind_speed_10m ?? 0)} km/h`;
            document.getElementById('aoWF').textContent = `${Math.round(c.apparent_temperature ?? 0)}°C`;
            document.getElementById('aoWU').textContent = `${c.uv_index ?? '—'}`;

            const days = d.daily?.time || [];
            const tmax = d.daily?.temperature_2m_max || [];
            const tmin = d.daily?.temperature_2m_min || [];
            const labels = lang === 'mr' ? FALLBACK_WEATHER.mr : lang === 'hi' ? FALLBACK_WEATHER.hi : FALLBACK_WEATHER.days;

            let fc = '';
            for (let i = 1; i <= 3; i++) {
                if (!days[i]) continue;
                const dt = new Date(days[i]);
                fc += `<div class="ao-f"><span>${labels[dt.getDay()]} ${dt.getDate()}</span><span><strong>${Math.round(tmax[i])}°</strong> / ${Math.round(tmin[i])}°</span></div>`;
            }
            document.getElementById('aoFC').innerHTML = fc || `<div class="ao-f">${esc(text('forecastUnavailable'))}</div>`;
        } catch {
            document.getElementById('aoWDesc').textContent = text('weatherUnavailable');
        }
    }

    async function fetchNews(){
        const cname = city.name;
        document.getElementById('aoNewsTitle').textContent = `${text('news')} — ${cname}`;
        
        // Prefer server-side news if available
        if (DB_NEWS && Array.isArray(DB_NEWS) && DB_NEWS.length > 0) {
            renderNews(DB_NEWS);
            return;
        }
        
        // Fallback to browser API if server news is unavailable
        const sources = [
            `https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(`https://news.google.com/rss/search?q=${encodeURIComponent(cname + ' Maharashtra')}&hl=en-IN&gl=IN&ceid=IN:en`)}&count=8`,
            `https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(`https://news.google.com/rss/search?q=${encodeURIComponent(cname + ' site:timesofindia.indiatimes.com')}&hl=en-IN&gl=IN&ceid=IN:en`)}&count=8`
        ];
        for (const src of sources) {
            try {
                const res = await fetch(src);
                const data = await res.json();
                if (data.status === 'ok' && data.items?.length) {
                    renderNews(data.items.map((it) => ({
                        title: (it.title || '').replace(/ - [^-]{1,30}$/,'').trim(),
                        link: it.link || '#',
                        source: it.author || 'News',
                        pubDate: it.pubDate || null,
                        category: 'City'
                    })));
                    return;
                }
            } catch {}
        }
        renderNews([]);
    }

    function renderNews(items){
        const list = (items || []).slice(0, 6);
        if (!list.length) {
            document.getElementById('aoNews').innerHTML = `<div class="ao-list-item">${esc(text('noNews'))}</div>`;
            return;
        }
        document.getElementById('aoNews').innerHTML = list.map((n, i) => `
            <a class="ao-list-item" href="${esc(n.link || '#')}" target="_blank" rel="noopener">
                <div class="ao-nno">${String(i + 1).padStart(2, '0')}</div>
                <div>
                    <div class="ao-nt">${esc(n.title || '')}</div>
                    <div class="ao-nm">${esc(n.source || 'News')} ${n.pubDate ? '· ' + new Date(n.pubDate).toLocaleDateString('en-IN') : ''}</div>
                </div>
            </a>
        `).join('');
    }

    async function fetchCommodities(){
        if (MARKET_SOURCE === 'agmarknet' && SERVER_GROUPED_MARKET.length) {
            renderGroupedCommodities(SERVER_GROUPED_MARKET);
            return;
        }

        const buildAgmarkUrl = (district = '') => {
            const fromDate = new Date(Date.now() - 4 * 24 * 60 * 60 * 1000).toISOString().slice(0, 10);
            const toDate = new Date().toISOString().slice(0, 10);
            const params = new URLSearchParams({
                'api-key': AGMARK_API_KEY,
                'format': 'json',
                'filters[State]': 'Maharashtra',
                'sort[Market]': 'desc',
                'range[Arrival_Date][gte]': fromDate,
                'range[Arrival_Date][lte]': toDate,
            });
            if (district) params.set('filters[District]', district);
            return `https://api.data.gov.in/resource/${AGMARK_RESOURCE_ID}?${params.toString()}`;
        };

        try {
            const cityUrl = buildAgmarkUrl(city.district || city.name);
            const r1 = await fetch(cityUrl);
            const d1 = await r1.json();
            if (d1.records?.length) {
                const rows = normalizeMandiRows(d1.records);
                if (rows.length) return renderCommodities(rows);
            }

            const stateUrl = buildAgmarkUrl('');
            const r2 = await fetch(stateUrl);
            const d2 = await r2.json();
            const rows2 = normalizeMandiRows(d2.records || []);
            if (rows2.length) return renderCommodities(rows2);
        } catch {}
        if (SERVER_GROUPED_MARKET.length) {
            renderGroupedCommodities(SERVER_GROUPED_MARKET);
            return;
        }
        renderCommodities(DB_MARKET);
    }

    function formatMarketDate(value){
        if (!value) return '-';
        if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(value)) {
            const [day, month, year] = value.split('/');
            const dt = new Date(`${year}-${month}-${day}`);
            if (!Number.isNaN(dt.getTime())) {
                return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short' });
            }
        }
        const dt = new Date(value);
        if (!Number.isNaN(dt.getTime())) {
            return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short' });
        }
        return value;
    }

    function marketNumber(value){
        if (value === null || value === undefined || value === '') return '-';
        const num = Number(String(value).replace(/,/g, ''));
        return Number.isFinite(num) ? num.toLocaleString('en-IN', { maximumFractionDigits: 0 }) : String(value);
    }

    function marketNumeric(value){
        if (value === null || value === undefined || value === '') return null;
        const num = Number(String(value).replace(/,/g, ''));
        return Number.isFinite(num) ? num : null;
    }

    function marketDateTs(value){
        if (!value) return 0;
        if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(value)) {
            const [day, month, year] = value.split('/');
            const dt = new Date(`${year}-${month}-${day}`);
            return Number.isNaN(dt.getTime()) ? 0 : dt.getTime();
        }
        const dt = new Date(value);
        return Number.isNaN(dt.getTime()) ? 0 : dt.getTime();
    }

    function renderGroupedCommodities(groups){
        const header = document.getElementById('aoMandiHeader');
        if (header) header.style.display = 'none';

        if (!groups || !groups.length) {
            document.getElementById('aoMandi').innerHTML = `<div class="ao-row"><div>${esc(text('noMarketWise'))}</div></div>`;
            return;
        }

        document.getElementById('aoMandi').innerHTML = groups.map(group => `
            <div class="ao-market-wrap">
                <h4 class="ao-market-name">${esc(group.market || '')}</h4>
                <table class="ao-market-table">
                    <thead>
                        <tr>
                            <th>${esc(text('commodity'))}</th>
                            <th>${esc(text('variety'))}</th>
                            <th>${esc(text('min'))}</th>
                            <th>${esc(text('max'))}</th>
                            <th>${esc(text('modal'))}</th>
                            <th>${esc(text('date'))}</th>
                            <th>${esc(text('change'))}</th>
                            <th>${esc(text('trend'))}</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${group.commodities.flatMap(item => {
                            const sortedRows = [...(item.rows || [])]
                                .map((row) => ({
                                    ...row,
                                    _ts: marketDateTs(row.date),
                                    _modal: marketNumeric(row.modal_price),
                                }))
                                .sort((a, b) => b._ts - a._ts);

                            return sortedRows.map((row, idx) => {
                                const prev = sortedRows[idx + 1] || null;
                                const prevModal = prev ? prev._modal : null;
                                const currentModal = row._modal;
                                const hasDelta = currentModal !== null && prevModal !== null && prevModal !== 0;
                                const diff = hasDelta ? currentModal - prevModal : 0;
                                const pctSigned = hasDelta ? ((diff / prevModal) * 100).toFixed(1) : '0.0';
                                const cls = diff > 0 ? 'ao-up' : diff < 0 ? 'ao-dn' : 'ao-fl';
                                const trend = diff > 0 ? '▲' : diff < 0 ? '▼' : '—';
                                const changeText = hasDelta
                                    ? `${diff > 0 ? '+' : ''}${inr(diff)}`
                                    : '—';
                                const trendText = hasDelta
                                    ? `${trend} ${diff > 0 ? '+' : ''}${pctSigned}%`
                                    : '— 0.0%';

                                return `
                                    <tr>
                                        <td>${esc(item.commodity || '-')}</td>
                                        <td>${esc(row.variety || '-')}</td>
                                        <td>${marketNumber(row.min_price)}</td>
                                        <td>${marketNumber(row.max_price)}</td>
                                        <td class="ao-market-modal">${marketNumber(row.modal_price)}</td>
                                        <td>${esc(formatMarketDate(row.date))}</td>
                                        <td class="${cls}">${changeText}</td>
                                        <td class="${cls}">${trendText}</td>
                                    </tr>
                                `;
                            });
                        }).join('')}
                    </tbody>
                </table>
            </div>
        `).join('');
    }

    function normalizeMandiRows(records){
        const parseDate = (value) => {
            if (!value) return 0;
            if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(value)) {
                const [day, month, year] = value.split('/');
                return new Date(`${year}-${month}-${day}`).getTime() || 0;
            }
            return new Date(value).getTime() || 0;
        };

        const grouped = new Map();
        (records || []).forEach((record) => {
            const market = record.Market || record.District || city.name;
            const commodity = record.Commodity || 'General Rate';
            const modal = Number(record.Modal_Price || 0);
            if (!modal) return;

            const key = `${market}__${commodity}`;
            if (!grouped.has(key)) grouped.set(key, []);
            grouped.get(key).push({
                market,
                commodity,
                modal,
                min: Number(record.Min_Price || modal),
                max: Number(record.Max_Price || modal),
                dateRaw: record.Arrival_Date || '',
                dateTs: parseDate(record.Arrival_Date || ''),
            });
        });

        const rows = [];
        grouped.forEach((items) => {
            items.sort((a, b) => b.dateTs - a.dateTs);
            const current = items[0];
            const previous = items.find((item) => item.dateRaw !== current.dateRaw) || items[1] || current;

            rows.push({
                n: current.commodity,
                u: `${current.market} mandi`,
                min: current.min,
                p: current.modal,
                pv: previous.modal,
                max: current.max,
                date: current.dateRaw,
                _sort: current.dateTs,
            });
        });

        return rows
            .sort((a, b) => b._sort - a._sort)
            .slice(0, 10)
            .map(({ _sort, ...row }) => row);
    }

    function renderCommodities(rows){
        const header = document.getElementById('aoMandiHeader');
        if (header) header.style.display = 'grid';
        if (!rows || !rows.length) {
            document.getElementById('aoMandi').innerHTML = `<div class="ao-row"><div>${esc(text('noMarket'))}</div></div>`;
            return;
        }
        document.getElementById('aoMandi').innerHTML = rows.map(r => {
            const diff = Number(r.p) - Number(r.pv || r.p);
            const pct = r.pv ? Math.abs((diff / r.pv) * 100).toFixed(1) : '0.0';
            const cls = diff > 0 ? 'ao-up' : diff < 0 ? 'ao-dn' : 'ao-fl';
            const trend = diff > 0 ? '▲' : diff < 0 ? '▼' : '—';
            const minValue = Number(r.min || 0);
            const maxValue = Number(r.max || 0);
            const rangeText = (minValue > 0 || maxValue > 0)
                ? ` · Min ${inr(minValue || r.p)} · Max ${inr(maxValue || r.p)}`
                : '';
            return `<div class="ao-row"><div><strong>${esc(r.n)}</strong><div class="ao-row-sub">${esc(r.u || '')}${r.date ? ' · ' + esc(r.date) : ''}${rangeText}</div></div><div class="ao-p">${inr(r.p)}</div><div class="ao-c ${cls}">${diff === 0 ? '—' : (diff > 0 ? '+' : '') + inr(diff)}</div><div class="ao-c ${cls}">${trend} ${diff === 0 ? '0.0' : pct}%</div></div>`;
        }).join('');
    }

    async function fetchCrypto(){
        try {
            const res = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,ripple,solana,binancecoin,dogecoin,matic-network,tether&vs_currencies=inr&include_24hr_change=true');
            const data = await res.json();
            const coins = [
                ['bitcoin', '₿ BTC'], ['ethereum', '⟠ ETH'], ['solana', '◎ SOL'], ['ripple', '✕ XRP'],
                ['binancecoin', '⬡ BNB'], ['dogecoin', 'Ð DOGE'], ['matic-network', '⬡ MATIC'], ['tether', '₮ USDT']
            ];
            document.getElementById('aoCrypto').innerHTML = coins.filter(([id]) => data[id]).map(([id, symbol]) => {
                const p = Number(data[id].inr || 0);
                const ch = Number(data[id].inr_24h_change || 0);
                return `<div class="ao-mini"><div>${symbol}</div><div>${p > 100 ? inr(p.toFixed(0)) : inr(p.toFixed(4))}</div><div class="${ch>=0?'ao-up':'ao-dn'}">${ch>=0?'▲':'▼'} ${Math.abs(ch).toFixed(2)}%</div></div>`;
            }).join('');
        } catch {
            document.getElementById('aoCrypto').innerHTML = `<div class="ao-mini ao-mini-wide">${esc(text('cryptoUnavailable'))}</div>`;
        }
    }

    async function fetchForex(){
        try {
            const res = await fetch('https://api.exchangerate-api.com/v4/latest/INR');
            const d = await res.json();
            const pairs = [['USD','🇺🇸'],['EUR','🇪🇺'],['GBP','🇬🇧'],['AED','🇦🇪'],['SAR','🇸🇦'],['JPY','🇯🇵'],['SGD','🇸🇬'],['CNY','🇨🇳']];
            document.getElementById('aoFx').innerHTML = pairs.map(([c,f]) => `
                <div class="ao-fx-row">
                    <div class="ao-fx-code">${f} 1 ${c}</div><div class="ao-fx-val">${inr((1 / (d.rates[c] || 1)).toFixed(2))}</div>
                </div>`).join('');
        } catch {
            document.getElementById('aoFx').innerHTML = `<div class="ao-soft-empty">${esc(text('forexUnavailable'))}</div>`;
        }
    }

    async function fetchIndianMarkets(){
        // Prefer server-side data if available
        if (SERVER_INDIAN_MARKETS && Array.isArray(SERVER_INDIAN_MARKETS) && SERVER_INDIAN_MARKETS.length > 0) {
            renderIndianMarkets(SERVER_INDIAN_MARKETS);
            return;
        }
        
        // Fallback to browser API if server data unavailable
        const idx = [
            {symbol:'^BSESN', name:'SENSEX'},
            {symbol:'^NSEI', name:'NIFTY 50'},
            {symbol:'^NSEBANK', name:'BANK NIFTY'},
            {symbol:'^CNXIT', name:'NIFTY IT'}
        ];
        const out = [];
        for (const item of idx) {
            try {
                const res = await fetch(`https://query1.finance.yahoo.com/v8/finance/chart/${encodeURIComponent(item.symbol)}?interval=1d&range=5d`);
                const d = await res.json();
                const m = d.chart?.result?.[0]?.meta;
                const price = Number(m?.regularMarketPrice || 0);
                const prev = Number(m?.chartPreviousClose || m?.previousClose || 0);
                if (price && prev) {
                    const change = ((price - prev) / prev) * 100;
                    out.push({name:item.name, price, change});
                }
            } catch {}
        }
        renderIndianMarkets(out);
    }

    function renderIndianMarkets(indices){
        if (!indices || !indices.length) {
            document.getElementById('aoIndia').innerHTML = `<div class="ao-soft-empty">${esc(text('indiaUnavailable'))}</div>`;
            return;
        }
        document.getElementById('aoIndia').innerHTML = indices.map(r => `
            <div class="ao-ind-row">
                <div class="ao-ind-name">${r.name}</div>
                <div class="ao-ind-right"><div class="ao-ind-price">${Number(r.price).toLocaleString('en-IN',{maximumFractionDigits:2})}</div><div class="ao-ind-change ${r.change>=0?'ao-up':'ao-dn'}">${r.change>=0?'▲':'▼'} ${Math.abs(r.change).toFixed(2)}%</div></div>
            </div>
        `).join('');
    }

    function renderFarm(){
        const items = DB_FARM.length ? DB_FARM : [
            { title: 'Soya bean harvest tips', content: 'Ensure timely sowing and use certified seeds. Monitor moisture.', url: '#' },
            { title: 'Bee keeping basics', content: 'Bees increase pollination; keep boxes shaded and water available.', url: '#' }
        ];
        document.getElementById('aoFarm').innerHTML = items.slice(0, 6).map((f) => `
            <a class="ao-list-item" href="${esc(f.url || '#')}">
                <div class="ao-farm-ico">🌱</div>
                <div><div class="ao-farm-title">${esc(f.title || '')}</div><div class="ao-farm-copy">${esc((f.content || '').slice(0, 140))}</div></div>
            </a>
        `).join('');
    }

    async function init(){
        applyLang();
        document.getElementById('aoTicker').innerHTML = `<span class="ao-ti">${esc(text('loading'))}</span>`;
        document.getElementById('aoWCity').textContent = `${city.name}, MH`;
        renderFarm();
        await Promise.allSettled([fetchMetals(), fetchWeather(), fetchNews(), fetchCommodities(), fetchCrypto(), fetchForex(), fetchIndianMarkets()]);
    }

    init();
    setInterval(fetchMetals, 5 * 60 * 1000);
    setInterval(fetchCrypto, 2 * 60 * 1000);
    setInterval(fetchNews, 10 * 60 * 1000);
    setInterval(fetchWeather, 15 * 60 * 1000);
})();
</script>
@endsection
