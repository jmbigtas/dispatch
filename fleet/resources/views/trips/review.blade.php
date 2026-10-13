@extends('layout')
@section('title', 'Review trip')
@section('content')
<h1>Review trip #{{ $trip->tripTicketId }}</h1>
<div class="row c2">
  <div class="card">
    <h2>Request</h2>
    <p><b>{{ $trip->requester->name }}</b> · {{ $trip->divisionRel->DivisionName }}<br>
    {{ $trip->TripPurpose }}<br>{{ $trip->numOfPassenger }} passenger(s)<br>
    {{ $trip->DepartureTime->format('M j, Y g:i A') }} to {{ $trip->ReturnTime->format('M j, Y g:i A') }}</p>
    <form method="POST" action="{{ route('trips.reject', $trip) }}" onsubmit="return confirm('Reject this request?')">@csrf
      <button class="btn dark">Reject request</button></form>
  </div>
  <form class="card" id="approveForm" method="POST" action="{{ route('trips.approve', $trip) }}">@csrf
    <h2>Assign and approve</h2>
    <p style="font-size:13px;margin-top:0">Only vehicles and drivers free for this time window are listed.</p>
    <label>Vehicle</label>
    <select name="AssignedVehicle" required><option value="">Choose a vehicle</option>
      @foreach($vehicles as $v)<option value="{{ $v->VehicleID }}">{{ $v->PlateNo }} · {{ $v->vehicleModel->ModelName }} ({{ $v->vehicleModel->Capacity }} seats)</option>@endforeach</select>
    <label>Driver</label>
    <select name="AssignedDriver" required><option value="">Choose a driver</option>
      @foreach($drivers as $d)<option value="{{ $d->DriversID }}">{{ $d->full_name }}</option>@endforeach</select>
    @foreach($errors->all() as $e)<div class="err">{{ $e }}</div>@endforeach
    <button type="button" class="btn" id="askApprove">Approve trip</button>
  </form>
</div>
<dialog id="confirmDlg" aria-labelledby="confirmTitle">
  <h2 id="confirmTitle">Approve this trip?</h2>
  <p id="confirmText"></p>
  <button type="button" class="btn out" id="cancelApprove">Cancel</button>
  <button type="button" class="btn" id="doApprove">Yes, approve</button>
</dialog>
<script>
  const f = document.getElementById('approveForm'), dlg = document.getElementById('confirmDlg');
  document.getElementById('askApprove').onclick = () => {
    if (!f.reportValidity()) return;
    document.getElementById('confirmText').textContent =
      'Assign ' + f.AssignedVehicle.selectedOptions[0].text + ' with driver ' + f.AssignedDriver.selectedOptions[0].text +
      ' to trip #{{ $trip->tripTicketId }}? It will count on the calendar and block those bookings for that time.';
    dlg.showModal();
  };
  document.getElementById('cancelApprove').onclick = () => dlg.close();
  document.getElementById('doApprove').onclick = () => f.submit();
</script>
@endsection
