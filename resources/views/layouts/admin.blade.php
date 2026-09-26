<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($titlePage ?? 'Dashboard') }} &middot; {{ config('app.name') }}</title>
    <style>
        /*
         * Light theme: light blue (primary/brand), light orange (accent/hover),
         * white/near-white surfaces. Functional status colors (success green,
         * danger red) are kept distinct from the brand palette so pass/fail
         * states stay readable at a glance.
         */
        :root {
            --bg: #f2f8fd; --panel: #ffffff; --ink: #1f2937; --muted: #64748b;
            --line: #dce9f5; --brand: #4da6e0; --brand-dark: #2f8fcc; --brand-ink: #ffffff;
            --brand-tint: #e8f4fc;
            --accent: #f5a35c; --accent-dark: #d97e2e; --accent-ink: #7a3d09; --accent-tint: #fdecd8;
            --ok: #15803d; --ok-tint: #dcfce7;
            --warn: #b45309; --warn-tint: #fef3c7;
            --bad: #b91c1c; --bad-tint: #fee2e2;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink);
               font: 15px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        a { color: var(--brand-dark); }
        .shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; flex: 0 0 240px; background: var(--brand-tint); color: #2a4a63;
                   padding: 20px 0; border-right: 1px solid var(--line); }
        .sidebar .brand { padding: 0 20px 20px; font-weight: 600; font-size: 17px; color: var(--brand-dark); }
        .sidebar .nav { list-style: none; margin: 0; padding: 0; }
        .sidebar .nav-link { display: block; padding: 11px 20px; color: #2a4a63; text-decoration: none; }
        .sidebar .nav-link:hover { background: var(--accent-tint); color: var(--accent-ink); }
        .sidebar .nav-item.active > .nav-link { background: var(--brand); color: var(--brand-ink); }
        .sidebar form { margin: 0; }
        .main { flex: 1; min-width: 0; }
        .topbar { background: var(--panel); border-bottom: 1px solid var(--line);
                  padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        .topbar .who { color: var(--muted); font-size: 14px; }
        .content-wrapper { padding: 24px; }
        .row { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 16px; }
        .row > [class^="col-"] { flex: 1 1 220px; min-width: 0; }
        .col-md-8 { flex: 2 1 420px; } .col-md-10 { flex: 1 1 100%; } .col-md-12 { flex: 1 1 100%; }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: 10px; }
        .card-body { padding: 18px 20px; }
        .card-title { margin: 0 0 12px; font-size: 16px; font-weight: 600; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--line); vertical-align: top; }
        .table th { font-size: 13px; color: var(--muted); font-weight: 600; }
        .table-responsive { overflow-x: auto; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px;
                 background: var(--brand-tint); color: var(--brand-dark); }
        .badge-success { background: var(--ok-tint); color: var(--ok); }
        .badge-warning { background: var(--accent-tint); color: var(--accent-dark); }
        .btn { display: inline-block; padding: 8px 14px; border-radius: 8px; border: 1px solid var(--line);
               background: var(--panel); color: var(--ink); cursor: pointer; font-size: 14px; text-decoration: none; }
        .btn:hover { background: var(--brand-tint); }
        .btn-primary { background: var(--brand); border-color: var(--brand-dark); color: #fff; }
        .btn-primary:hover { background: var(--brand-dark); }
        .btn-sm { padding: 5px 10px; font-size: 13px; }
        .btn-outline-danger { color: var(--bad); border-color: #fecaca; }
        .btn-outline-danger:hover { background: var(--bad-tint); }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid transparent; }
        .alert-success { background: var(--ok-tint); border-color: #bbf7d0; color: var(--ok); }
        .alert-warning { background: var(--accent-tint); border-color: #fbd8ac; color: var(--accent-dark); }
        .alert-danger { background: var(--bad-tint); border-color: #fecaca; color: var(--bad); }
        .alert-info { background: var(--brand-tint); border-color: #bfe1f7; color: var(--brand-dark); }
        .text-muted { color: var(--muted); }
        .text-danger { color: var(--bad); }
        .small, .form-text { font-size: 13px; }
        .form-group { margin-bottom: 16px; }
        .form-control { width: 100%; padding: 9px 12px; border: 1px solid var(--line);
                        border-radius: 8px; font: inherit; background: #fff; }
        .form-control:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-tint); }
        label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 14px; }
        code { background: var(--brand-tint); color: var(--brand-dark); padding: 1px 5px; border-radius: 4px; font-size: 13px; }
        .d-inline { display: inline-block; }
        .text-right { text-align: right; }
        .mb-0 { margin-bottom: 0; } .mb-1 { margin-bottom: 4px; } .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; } .mb-4 { margin-bottom: 16px; } .mt-3 { margin-top: 12px; }
        .d-flex { display: flex; } .justify-content-between { justify-content: space-between; }
        .align-items-center { align-items: center; }
        ol { padding-left: 20px; } ol li { margin-bottom: 8px; }
        @media (max-width: 800px) {
            .shell { flex-direction: column; }
            .sidebar { width: 100%; flex: none; }
        }
    </style>
</head>
<body>
<div class="shell">
    @include('layouts.sidebar')

    <div class="main">
        <div class="topbar">
            <strong>{{ $titlePage ?? 'Dashboard' }}</strong>
            <span class="who">
                {{ Auth::user()->name }}
                @if (Auth::user()->tenant)
                    &middot; {{ Auth::user()->tenant->name }}
                @else
                    &middot; Platform
                @endif
            </span>
        </div>

        @yield('content')
    </div>
</div>
</body>
</html>
