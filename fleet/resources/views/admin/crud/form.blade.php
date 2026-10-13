@extends('layout')
@section('title', $c['singular'])
@section('content')
<h1>{{ $row ? 'Edit' : 'Add' }} {{ strtolower($c['singular']) }}</h1>
<form class="card" method="POST" style="max-width:520px"
      action="{{ $row ? route($c['route'].'.update', $row->getKey()) : route($c['route'].'.store') }}">@csrf
  @if($row) @method('PUT') @endif
  @foreach($c['fields'] as $name => $f)
    <label for="{{ $name }}">{{ $f['label'] }}</label>
    @if($f['type'] === 'select')
      <select id="{{ $name }}" name="{{ $name }}" required>
        <option value="">Choose one</option>
        @foreach($f['options'] as $id => $text)<option value="{{ $id }}" @selected(old($name, $row?->$name) == $id)>{{ $text }}</option>@endforeach
      </select>
    @else
      <input id="{{ $name }}" name="{{ $name }}" type="{{ $f['type'] }}" value="{{ old($name, $row?->$name) }}" required>
    @endif
    @error($name)<div class="err">{{ $message }}</div>@enderror
  @endforeach
  <button class="btn">Save</button> <a class="btn out" href="{{ route($c['route'].'.index') }}">Cancel</a>
</form>
@endsection
