@php($canView = auth()->user()->hasRole('approving_authority', 'admin'))
<div class="head" style="margin-bottom:6px">
  <h2 style="margin:0">Available drivers right now</h2>
  <span class="badge {{ $free->isEmpty() ? '' : 'dk' }}">{{ $free->count() }} of {{ $free->count() + $busy->count() }} free</span>
</div>
@forelse($free as $d)
  <div class="drv"><span class="dot"></span><div>
    @if($canView)<a href="{{ route('driver.schedule', $d->UserID) }}">{{ $d->name }}</a>@else<b>{{ $d->name }}</b>@endif
    <small>{{ $next->has($d->UserID) ? 'Next trip '.$next[$d->UserID]->DepartureTime->format('M j, g:i A') : 'No upcoming trips' }}</small>
  </div></div>
@empty
  <p style="color:var(--mute);margin:10px 0">{{ $busy->isEmpty() ? 'No drivers yet. An admin can give a user the Driver role.' : 'Every driver is on a trip right now.' }}</p>
@endforelse
@if($busy->isNotEmpty())
  <div class="sub">On a trip</div>
  @foreach($busy as $d)
    <div class="drv"><span class="dot off"></span><div>
      @if($canView)<a href="{{ route('driver.schedule', $d->UserID) }}">{{ $d->name }}</a>@else<b>{{ $d->name }}</b>@endif
      <small>Back around {{ ($active[$d->UserID]->ReturnTime ?? $active[$d->UserID]->DepartureTime)->format('g:i A') }}{{ $active[$d->UserID]->vehicle ? ' · '.$active[$d->UserID]->vehicle->PlateNo : '' }}</small>
    </div></div>
  @endforeach
@endif
