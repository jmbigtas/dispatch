@extends('layout')
@section('title', 'New trip request')
@section('content')
<h1>New trip request</h1>
<form class="card" method="POST" action="{{ route('trips.store') }}" style="max-width:640px">@csrf
  <label>Requesting division</label>
  <select name="RequestingDivision">@foreach($divisions as $d)<option value="{{ $d->DivisionID }}" @selected(old('RequestingDivision', auth()->user()->Division) == $d->DivisionID)>{{ $d->DivisionName }}</option>@endforeach</select>
  <label>Trip purpose</label><input name="TripPurpose" value="{{ old('TripPurpose') }}" maxlength="150" required>
  <label>Number of passengers</label><input type="number" name="numOfPassenger" value="{{ old('numOfPassenger', 1) }}" min="1" required>
  <label>Departure</label><input type="datetime-local" name="DepartureTime" value="{{ old('DepartureTime') }}" required>
  <label>Expected arrival (optional)</label><input type="datetime-local" name="ArrivalTime" value="{{ old('ArrivalTime') }}">
  <label>Return</label><input type="datetime-local" name="ReturnTime" value="{{ old('ReturnTime') }}" required>
  @foreach($errors->all() as $e)<div class="err">{{ $e }}</div>@endforeach
  <button class="btn">Submit request</button> <a class="btn out" href="{{ route('trips.index') }}">Cancel</a>
</form>
@endsection
