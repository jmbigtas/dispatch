<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Fleet') · Fleet Trips</title>
<style>
:root{--ink:#05070d;--ink2:#0d1322;--navy:#0a2a6b;--blue:#1d4ed8;--bright:#3b82f6;--sky:#bfdbfe;--mist:#eef3fd;--line:#e1e8f5;--bg:#f5f7fc;--mute:#5b6b8c;--side:256px}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;background:var(--bg);color:var(--ink);line-height:1.45}
a{color:var(--blue)}

/* ---- sidebar ---- */
.side{position:fixed;inset:0 auto 0 0;width:var(--side);background:var(--ink);color:#fff;display:flex;flex-direction:column;padding:20px 14px;z-index:30;overflow-y:auto;transition:transform .2s}
.brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:18px;color:#fff;text-decoration:none;padding:2px 8px 18px}
.brand i{width:32px;height:32px;border-radius:8px;background:var(--blue);display:grid;place-items:center}
.brand svg{width:18px;height:18px}
.newbtn{display:flex;align-items:center;justify-content:center;gap:6px;background:var(--blue);color:#fff;text-decoration:none;padding:10px;border-radius:8px;font-size:14px;font-weight:600;margin:0 4px 18px}
.newbtn:hover{background:var(--bright)}
.grp{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#6b7da6;margin:14px 10px 6px}
.nav{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:8px;color:#b7c4e2;text-decoration:none;font-size:14px;border-left:3px solid transparent}
.nav svg{width:18px;height:18px;flex:none}
.nav:hover{background:var(--ink2);color:#fff}
.nav.on{background:rgba(59,130,246,.16);color:#fff;border-left-color:var(--bright)}
.me{margin-top:auto;padding-top:16px;border-top:1px solid #1b2440}
.me b{display:block;font-size:14px}.me small{color:#8394bb}
.me .who{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.av{width:36px;height:36px;border-radius:50%;background:var(--blue);display:grid;place-items:center;font-weight:700;font-size:13px;flex:none}
.out-btn{width:100%;display:flex;align-items:center;justify-content:center;gap:8px;background:transparent;color:#b7c4e2;border:1px solid #26314f;border-radius:8px;padding:8px;cursor:pointer;font:inherit;font-size:14px}
.out-btn:hover{background:var(--ink2);color:#fff}.out-btn svg{width:16px;height:16px}
.top{display:none}

/* ---- content ---- */
.page{margin-left:var(--side);min-height:100vh}
main{max-width:1180px;margin:0 auto;padding:30px 32px}
.guest main{max-width:none;padding:0}
.guest{background:radial-gradient(1200px 500px at 80% -10%,#dbe7ff,transparent),var(--bg)}
h1{margin:0 0 20px;font-size:26px;letter-spacing:-.01em;color:var(--ink)}
h2{margin:0 0 14px;font-size:16px;color:var(--ink)}
.card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(10,42,107,.04),0 6px 18px rgba(10,42,107,.05)}
.head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap}
.btn{display:inline-block;background:var(--blue);color:#fff;border:1px solid var(--blue);padding:8px 14px;border-radius:8px;cursor:pointer;text-decoration:none;font:inherit;font-size:14px;font-weight:500}
.btn:hover{background:var(--navy);border-color:var(--navy)}
.btn.dark{background:var(--ink);border-color:var(--ink)}.btn.dark:hover{background:#26314f}
.btn.out{background:#fff;color:var(--blue);border-color:#b9c9ee}.btn.out:hover{background:var(--mist);border-color:var(--blue)}

table{width:100%;border-collapse:collapse}
th,td{padding:11px 10px;border-bottom:1px solid var(--line);text-align:left;font-size:14px}
th{background:var(--mist);color:var(--navy);font-size:12px;text-transform:uppercase;letter-spacing:.05em;font-weight:600}
th:first-child{border-radius:8px 0 0 8px}th:last-child{border-radius:0 8px 8px 0}
tr:last-child td{border-bottom:0}tbody tr:hover td,table tr:hover td{background:#fafcff}
label{font-size:13px;font-weight:600;color:#26314f}
input,select{width:100%;padding:10px 12px;border:1px solid #c3d0ea;border-radius:8px;margin:5px 0 16px;font:inherit;background:#fff}
input:focus,select:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(29,78,216,.15);outline:0}
a:focus-visible,button:focus-visible{outline:2px solid var(--blue);outline-offset:2px}
.row{display:grid;gap:20px}.c3{grid-template-columns:repeat(3,1fr)}.c2{grid-template-columns:3fr 2fr}.row>.card{margin-bottom:0}.row{margin-bottom:20px}
.stat{border:0;background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;position:relative;overflow:hidden}
.stat b{font-size:38px;display:block;line-height:1.1;color:#fff}.stat small{color:#c7d8ff;font-size:14px}
.stat:nth-child(2){background:linear-gradient(135deg,#1e40af,#3b82f6)}
.stat:nth-child(3){background:linear-gradient(135deg,var(--ink),#26314f)}
.badge{padding:3px 10px;border-radius:99px;background:var(--sky);color:var(--navy);font-size:12px;font-weight:600;white-space:nowrap}.badge.dk{background:var(--navy);color:#fff}
.err{color:#b91c1c;font-size:13px;margin:-10px 0 12px}
.ok{background:var(--mist);border:1px solid var(--sky);padding:11px 14px;border-radius:8px;margin-bottom:18px;color:var(--navy);font-size:14px}
.tabs{display:flex;gap:6px;flex-wrap:wrap}

.cal{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}.cal>b{font-size:12px;color:var(--mute);text-align:center;padding-bottom:2px}
.day{display:block;min-height:68px;border:1px solid var(--line);border-radius:8px;padding:7px 8px;font-size:13px;font-weight:600;background:#fff;color:var(--ink);text-decoration:none;transition:transform .1s}
.day:hover{transform:translateY(-1px);border-color:var(--bright)}
.day small{display:block;margin-top:8px;font-size:11px;font-weight:400;opacity:.85}
.day.low{background:var(--sky);border-color:#93c5fd}
.day.high{background:var(--navy);color:#fff;border-color:var(--navy)}
.day.sel{outline:3px solid var(--bright);outline-offset:1px}
.key{display:flex;gap:16px;font-size:12px;margin-top:14px;color:var(--mute);flex-wrap:wrap}
.key i{display:inline-block;width:12px;height:12px;border:1px solid #93c5fd;border-radius:3px;margin-right:6px;vertical-align:-1px}
dialog{border:0;border-radius:14px;padding:24px;max-width:430px;box-shadow:0 20px 60px rgba(5,7,13,.35)}
dialog::backdrop{background:rgba(5,7,13,.55)}dialog p{line-height:1.5;color:#26314f}

/* ---- small screens: sidebar becomes a slide-in menu ---- */
@media(max-width:900px){
  .top{display:flex;align-items:center;gap:12px;background:var(--ink);color:#fff;padding:10px 16px;position:sticky;top:0;z-index:20}
  .top button{background:transparent;border:0;color:#fff;cursor:pointer;padding:4px}.top svg{width:24px;height:24px;display:block}
  .side{transform:translateX(-100%)}.side.open{transform:none;box-shadow:0 0 0 100vmax rgba(5,7,13,.5)}
  .page{margin-left:0}main{padding:20px 16px}.c3,.c2{grid-template-columns:1fr}
  .cal{gap:3px}.day{min-height:50px;padding:4px 5px}.day small{display:none}
  .card{overflow-x:auto}
}
</style></head>
<body class="{{ auth()->check() ? '' : 'guest' }}">
@auth
@php
  $icons = [
    'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
    'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>',
    'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
    'building' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
    'car' => '<path d="M5 11l1.5-4.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11"/><rect x="3" y="11" width="18" height="6" rx="2"/><circle cx="7.5" cy="17" r="1.5"/><circle cx="16.5" cy="17" r="1.5"/>',
    'layers' => '<path d="M12 2 2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>',
    'wheel' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2"/><path d="M12 14v7M10 11.5 4 10M14 11.5l6-1.5"/>',
  ];
  $groups = [
    'Menu' => [
      ['Dashboard', 'dashboard', 'dashboard', 'grid', []],
      ['Trip tickets', 'trips.index', 'trips.*', 'file', []],
    ],
    'Administration' => [
      ['Users & roles', 'admin.users', 'admin.users*', 'users', ['admin']],
      ['Divisions', 'admin.divisions.index', 'admin.divisions.*', 'building', ['admin']],
      ['Vehicles', 'admin.vehicles.index', 'admin.vehicles.*', 'car', ['admin']],
      ['Vehicle models', 'admin.vehicle-models.index', 'admin.vehicle-models.*', 'layers', ['admin']],
      ['Drivers', 'admin.drivers.index', 'admin.drivers.*', 'wheel', ['admin']],
    ],
  ];
  $me = auth()->user();
@endphp
<div class="top">
  <button type="button" id="menuBtn" aria-label="Open menu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
  <b>Fleet Trips</b>
</div>
<aside class="side" id="side">
  <a class="brand" href="{{ route('dashboard') }}"><i><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icons['car'] !!}</svg></i>Fleet Trips</a>
  <a class="newbtn" href="{{ route('trips.create') }}">+ New trip request</a>
  @foreach($groups as $title => $items)
    @php($items = array_filter($items, fn ($i) => empty($i[4]) || $me->hasRole(...$i[4])))
    @if($items)
      <div class="grp">{{ $title }}</div>
      @foreach($items as $i)
        <a class="nav {{ request()->routeIs($i[2]) ? 'on' : '' }}" href="{{ route($i[1]) }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$i[3]] !!}</svg>{{ $i[0] }}</a>
      @endforeach
    @endif
  @endforeach
  <div class="me">
    <div class="who"><div class="av">{{ strtoupper(substr($me->FirstName, 0, 1).substr($me->LastName, 0, 1)) }}</div>
      <div><b>{{ $me->name }}</b><small>{{ $me->role_label }}</small></div></div>
    <form method="POST" action="{{ route('logout') }}">@csrf
      <button class="out-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>Log out</button></form>
  </div>
</aside>
<script>
  const side = document.getElementById('side');
  document.getElementById('menuBtn').onclick = e => { side.classList.toggle('open'); e.stopPropagation(); };
  document.addEventListener('click', e => { if (!side.contains(e.target)) side.classList.remove('open'); });
</script>
@endauth
<div class="page">
<main>
  @if(session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
  @yield('content')
</main>
</div>
</body></html>
