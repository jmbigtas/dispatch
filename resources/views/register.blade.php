@extends('layout')
@section('title', 'Create account')
@section('content')
<div class="card" style="max-width:440px;margin:48px auto;border-top:5px solid var(--blue)">
  <h1>Create account</h1>
  <form method="POST" action="{{ route('register') }}">@csrf
    <label for="FirstName">First name</label>
    <input id="FirstName" name="FirstName" value="{{ old('FirstName') }}" required autofocus>
    @error('FirstName')<div class="err">{{ $message }}</div>@enderror
    <label for="LastName">Last name</label>
    <input id="LastName" name="LastName" value="{{ old('LastName') }}" required>
    @error('LastName')<div class="err">{{ $message }}</div>@enderror
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
    @error('email')<div class="err">{{ $message }}</div>@enderror
    <label for="Division">Division</label>
    <select id="Division" name="Division" required>
      <option value="">Choose your division</option>
      @foreach($divisions as $d)<option value="{{ $d->DivisionID }}" @selected(old('Division') == $d->DivisionID)>{{ $d->DivisionName }}</option>@endforeach
    </select>
    @error('Division')<div class="err">{{ $message }}</div>@enderror
    <label for="password">Password (8+ characters)</label>
    <input id="password" type="password" name="password" required>
    @error('password')<div class="err">{{ $message }}</div>@enderror
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" required>
    <button class="btn" style="width:100%">Create account</button>
  </form>
  <p style="font-size:14px;margin:16px 0 0">Already registered? <a href="{{ route('login') }}">Log in</a></p>
</div>
@endsection
