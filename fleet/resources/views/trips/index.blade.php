@extends('layout')
@section('title', 'Trip tickets')
@section('content')
<div class="head"><h1 style="margin:0">Trip tickets</h1><a class="btn" href="{{ route('trips.create') }}">New trip request</a></div>
@foreach($errors->all() as $e)<div class="ok" style="background:#fee2e2;color:#991b1b">{{ $e }}</div>@endforeach
<div class="card">
  <table>
    <tr><th>#</th><th>Requested by</th><th>Purpose</th><th>Departure</th><th>Return</th><th>Vehicle</th><th>Driver</th><th>Status</th><th></th></tr>
    @forelse($trips as $t)
      <tr><td>{{ $t->tripTicketId }}</td><td>{{ $t->requester->name }}</td><td>{{ $t->TripPurpose }}</td>
        <td>{{ $t->DepartureTime->format('M j, g:i A') }}</td><td>{{ $t->ReturnTime?->format('M j, g:i A') }}</td>
        <td>{{ $t->vehicle?->PlateNo }}</td><td>{{ $t->driver?->full_name }}</td>
        <td><span class="badge {{ $t->statusRel->StatusName === 'Approved' ? 'dk' : '' }}">{{ $t->statusRel->StatusName }}</span></td>
        <td style="white-space:nowrap">@if(auth()->user()->hasRole('approving_authority', 'admin'))
          @if($t->statusRel->StatusName === 'Pending')<a class="btn" href="{{ route('trips.review', $t) }}">Review</a>
          @else<form method="POST" action="{{ route('trips.status', $t) }}" style="display:flex;gap:6px" onsubmit="return confirm('Change the status of trip #{{ $t->tripTicketId }}?')">@csrf @method('PATCH')
            <select name="Status" aria-label="Status" style="margin:0;width:auto">@foreach($statuses as $s)<option value="{{ $s->StatusID }}" @selected($t->Status == $s->StatusID)>{{ $s->StatusName }}</option>@endforeach</select>
            <button class="btn out">Update</button></form>@endif
        @endif</td></tr>
    @empty
      <tr><td colspan="9">No trip tickets yet. Create a request to get started.</td></tr>
    @endforelse
  </table>
  <div style="margin-top:12px">{{ $trips->links() }}</div>
</div>
@endsection
