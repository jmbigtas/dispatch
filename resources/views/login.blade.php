@extends('layout')
@section('title', 'Log in')
@section('content')
<div class="card" style="max-width:380px;margin:80px auto;border-top:5px solid var(--ink)">
  <h1>Log in</h1>
  <form method="POST" action="{{ route('login') }}">@csrf
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
    @error('email')<div class="err">{{ $message }}</div>@enderror
    <label for="password">Password</label>
    <input id="password" type="password" name="password" required>
    <button class="btn dark" style="width:100%">Log in</button>
  </form>
  <p style="font-size:14px;margin:16px 0 0">No account yet? <a href="{{ route('register') }}">Create one</a></p>
</div>
@endsection
