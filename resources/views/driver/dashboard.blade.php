@extends('layout')
@section('title', 'Driver schedule')
@section('content')
@php($go = fn ($extra = []) => route($rn, $rp + $extra + ['range' => $range]))
<div class="head">
  <h1 style="margin:0">{{ $rn === 'driver.dashboard' ? 'My schedule' : $driver->name."'s schedule" }}</h1>
  <span class="badge {{ $current ? 'dk' : '' }}">{{ $current ? 'On a trip until '.$current->ReturnTime?->format('g:i A') : 'Available now' }}</span>
</div>

<div class="row c3">
  @foreach(['today' => 'Trips today', 'week' => 'Trips this week', 'month' => 'Trips this month'] as $k => $t)
    <div class="card stat"><b>{{ $counts[$k] }}</b><small>{{ $t }}</small></div>
  @endforeach
</div>

<div class="row c2">
  <div class="card">
    <div class="head">
      <h2 style="margin:0">{{ $month->format('F Y') }}</h2>
      <div>
        <a class="btn out" href="{{ $go(['date' => $month->copy()->subMonth()->toDateString()]) }}">Prev</a>
        <a class="btn out" href="{{ route($rn, $rp + ['range' => $range]) }}">Today</a>
        <a class="btn out" href="{{ $go(['date' => $month->copy()->addMonth()->toDateString()]) }}">Next</a>
      </div>
    </div>
    <div class="cal">
      @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<b>{{ $d }}</b>@endforeach
      @for($i = 0; $i < $month->dayOfWeek; $i++)<div></div>@endfor
      @foreach($cells as $c)
        <a href="{{ $go(['date' => $c['date']->toDateString()]) }}" class="day {{ $c['level'] }} {{ $c['date']->isSameDay($sel) ? 'sel' : '' }}"
           title="{{ $c['trips'] }} trip(s)">{{ $c['date']->day }}<small>{{ $c['trips'] ? $c['trips'].' trip'.($c['trips'] > 1 ? 's' : '') : 'Free' }}</small></a>
      @endforeach
    </div>
    <div class="key">
      <span><i style="background:#fff;border-color:#b6c5e3"></i>No trips</span>
      <span><i style="background:var(--sky)"></i>1 to 2 trips</span>
      <span><i style="background:var(--navy);border-color:var(--navy)"></i>3 or more trips</span>
    </div>
  </div>

  <div class="card">
    <h2>Upcoming trips</h2>
    @forelse($upcoming as $t)
      <div class="drv"><span class="dot"></span><div><b>{{ $t->DepartureTime->format('D, M j · g:i A') }}</b>
        <small>{{ $t->TripPurpose }}{{ $t->vehicle ? ' · '.$t->vehicle->PlateNo : '' }}</small></div></div>
    @empty
      <p style="color:var(--mute)">No upcoming trips.</p>
    @endforelse
  </div>
</div>

<div class="card">
  <div class="head">
    <h2 style="margin:0">Trip log · {{ $label }}</h2>
    <div class="tabs">
      @foreach(['day' => 'Day', 'week' => 'Week', 'month' => 'Month'] as $k => $t)
        <a class="btn {{ $range === $k ? '' : 'out' }}" href="{{ $go(['range' => $k, 'date' => $sel->toDateString()]) }}">{{ $t }}</a>
      @endforeach
    </div>
  </div>
  <table>
    <tr><th>Departure</th><th>Return</th><th>Requested by</th><th>Division</th><th>Purpose</th><th>Vehicle</th><th>Pax</th></tr>
    @forelse($log as $t)
      <tr><td>{{ $t->DepartureTime->format('M j, g:i A') }}</td><td>{{ $t->ReturnTime?->format('M j, g:i A') }}</td><td>{{ $t->requester->name }}</td>
          <td>{{ $t->divisionRel->DivisionName }}</td><td>{{ $t->TripPurpose }}</td><td>{{ $t->vehicle?->PlateNo }}</td><td>{{ $t->numOfPassenger }}</td></tr>
    @empty
      <tr><td colspan="7">No trips in this period.</td></tr>
    @endforelse
  </table>
</div>
@endsection
