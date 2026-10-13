@extends('layout')
@section('title', $c['title'])
@section('content')
<div class="head"><h1 style="margin:0">{{ $c['title'] }}</h1><a class="btn" href="{{ route($c['route'].'.create') }}">Add {{ strtolower($c['singular']) }}</a></div>
@foreach($errors->all() as $e)<div class="ok" style="background:#fee2e2;color:#991b1b">{{ $e }}</div>@endforeach
<div class="card"><table>
  <tr>@foreach(array_keys($c['columns']) as $label)<th>{{ $label }}</th>@endforeach<th></th></tr>
  @forelse($rows as $row)
    <tr>@foreach($c['columns'] as $fn)<td>{{ $fn($row) }}</td>@endforeach
      <td style="white-space:nowrap;text-align:right">
        <a class="btn out" href="{{ route($c['route'].'.edit', $row->getKey()) }}">Edit</a>
        <form method="POST" action="{{ route($c['route'].'.destroy', $row->getKey()) }}" style="display:inline"
              onsubmit="return confirm('Delete this {{ strtolower($c['singular']) }}?')">@csrf @method('DELETE')<button class="btn dark">Delete</button></form>
      </td></tr>
  @empty
    <tr><td colspan="{{ count($c['columns']) + 1 }}">Nothing here yet.</td></tr>
  @endforelse
</table></div>
@endsection
