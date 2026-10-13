@extends('layout')
@section('title', 'Dashboard')
@section('content')
<h1>Dashboard</h1>
<div class="row c3">
  @foreach(['today' => 'Trips today', 'week' => 'Trips this week', 'month' => 'Trips this month'] as $k => $label)
    <a class="card stat" style="text-decoration:none;color:inherit" href="{{ route('dashboard', ['range' => $k, 'date' => $sel->toDateString()]) }}">
      <b>{{ $counts[$k] }}</b><small>{{ $label }}</small></a>
  @endforeach
</div>

<div class="row c2">
  <div class="card">
    <div class="head">
      <h2 style="margin:0">{{ $month->format('F Y') }}</h2>
      <div>
        <a class="btn out" href="{{ route('dashboard', ['date' => $month->copy()->subMonth()->toDateString(), 'range' => $range]) }}">Prev</a>
        <a class="btn out" href="{{ route('dashboard', ['range' => $range]) }}">Today</a>
        <a class="btn out" href="{{ route('dashboard', ['date' => $month->copy()->addMonth()->toDateString(), 'range' => $range]) }}">Next</a>
      </div>
    </div>
    <div class="cal">
      @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<b>{{ $d }}</b>@endforeach
      @for($i = 0; $i < $month->dayOfWeek; $i++)<div></div>@endfor
      @foreach($cells as $c)
        <a href="{{ route('dashboard', ['date' => $c['date']->toDateString(), 'range' => $range]) }}"
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
      @foreach($drivers as $d)
        @php($n = $selTrips->where('AssignedDriver', $d->DriversID)->count())
        <tr><td>{{ $d->full_name }}</td><td><span class="badge {{ $n ? 'dk' : '' }}">{{ $n ? $n.' trip'.($n > 1 ? 's' : '') : 'Free' }}</span></td></tr>
      @endforeach
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

<div class="card">
  <div class="head">
    <h2 style="margin:0">Trip log</h2>
    <div class="tabs">
      @foreach(['today' => 'Today', 'week' => 'This week', 'month' => 'This month'] as $k => $label)
        <a class="btn {{ $range === $k ? '' : 'out' }}" href="{{ route('dashboard', ['range' => $k, 'date' => $sel->toDateString()]) }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>
  <table>
    <tr><th>Departure</th><th>Requested by</th><th>Division</th><th>Purpose</th><th>Vehicle</th><th>Driver</th></tr>
    @forelse($log as $t)
      <tr><td>{{ $t->DepartureTime->format('M j, g:i A') }}</td><td>{{ $t->requester->name }}</td><td>{{ $t->divisionRel->DivisionName }}</td>
          <td>{{ $t->TripPurpose }}</td><td>{{ $t->vehicle?->PlateNo }}</td><td>{{ $t->driver?->full_name }}</td></tr>
    @empty
      <tr><td colspan="6">No trips in this period.</td></tr>
    @endforelse
  </table>
</div>
@endsection
