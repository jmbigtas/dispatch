@extends('layout')
@section('title', 'Dashboard')
@section('content')
<h1>Dashboard</h1>

<div class="row" style="grid-template-columns:2fr 3fr">
  <div class="card clock">
    <small id="clockDate">&nbsp;</small>
    <b id="clockTime">--:--:--</b>
    <small>Live time ({{ config('app.timezone') }})</small>
  </div>
  <div class="card" id="availBox">@include('partials.available')</div>
</div>

<div class="row c2">
  <div class="card">
    <div class="head">
      <h2 style="margin:0">{{ $month->format('F Y') }}</h2>
      <div>
        <a class="btn out" href="{{ route('dashboard', ['date' => $month->copy()->subMonth()->toDateString()]) }}">Prev</a>
        <a class="btn out" href="{{ route('dashboard') }}">Today</a>
        <a class="btn out" href="{{ route('dashboard', ['date' => $month->copy()->addMonth()->toDateString()]) }}">Next</a>
      </div>
    </div>
    <div class="cal">
      @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<b>{{ $d }}</b>@endforeach
      @for($i = 0; $i < $month->dayOfWeek; $i++)<div></div>@endfor
      @foreach($cells as $c)
        <a href="{{ route('dashboard', ['date' => $c['date']->toDateString()]) }}"
           class="day {{ $c['level'] }} {{ $c['date']->isSameDay($sel) ? 'sel' : '' }}"
           title="{{ $c['trips'] }} trip(s) · {{ $c['drivers'] }}/{{ $drivers->count() }} drivers · {{ $c['vehicles'] }}/{{ $vehicles->count() }} vehicles booked">
          {{ $c['date']->day }}<small>{{ $c['trips'] ? $c['trips'].' trip'.($c['trips'] > 1 ? 's' : '') : 'Free' }}</small>
        </a>
      @endforeach
    </div>
    <div class="key">
      <span><i style="background:#fff;border-color:#b6c5e3"></i>No trips</span>
      <span><i style="background:var(--sky)"></i>Light traffic</span>
      <span><i style="background:var(--navy);border-color:var(--navy)"></i>Busy: half or more of drivers out</span>
    </div>
  </div>

  <div class="card">
    <h2>Availability · {{ $sel->format('D, M j') }}</h2>
    <table>
      <tr><th>Driver</th><th>Status</th></tr>
      @forelse($drivers as $d)
        @php($n = $selTrips->where('AssignedDriver', $d->UserID)->count())
        <tr><td>{{ $d->name }}</td><td><span class="badge {{ $n ? 'dk' : '' }}">{{ $n ? $n.' trip'.($n > 1 ? 's' : '') : 'Free' }}</span></td></tr>
      @empty
        <tr><td colspan="2">No drivers yet.</td></tr>
      @endforelse
    </table>
    <table style="margin-top:14px">
      <tr><th>Vehicle</th><th>Status</th></tr>
      @foreach($vehicles as $v)
        @php($n = $selTrips->where('AssignedVehicle', $v->VehicleID)->count())
        <tr><td>{{ $v->PlateNo }} <small>{{ $v->vehicleModel->ModelName }}</small></td><td><span class="badge {{ $n ? 'dk' : '' }}">{{ $n ? $n.' trip'.($n > 1 ? 's' : '') : 'Free' }}</span></td></tr>
      @endforeach
    </table>
  </div>
</div>

<script>
  // Live clock, shown in the same timezone the server uses to decide who is available.
  const tz = @json(config('app.timezone'));
  const clockTime = document.getElementById('clockTime'), clockDate = document.getElementById('clockDate');
  const tick = () => {
    const n = new Date();
    clockTime.textContent = n.toLocaleTimeString([], {timeZone: tz, hour: '2-digit', minute: '2-digit', second: '2-digit'});
    clockDate.textContent = n.toLocaleDateString([], {timeZone: tz, weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
  };
  tick(); setInterval(tick, 1000);
  // Refresh the available-drivers list every 30 seconds.
  setInterval(() => fetch(@json(route('dashboard.available')), {headers: {'X-Requested-With': 'XMLHttpRequest'}})
    .then(r => r.ok ? r.text() : null).then(h => { if (h) document.getElementById('availBox').innerHTML = h; }).catch(() => {}), 30000);
</script>
@endsection
