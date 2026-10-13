@extends('layout')
@section('title', 'Users & roles')
@section('content')
<h1>Users &amp; roles</h1>
<div class="card"><table>
  <tr><th>Name</th><th>Email</th><th>Division</th><th>Role</th></tr>
  @foreach($users as $u)
    <tr><td>{{ $u->name }}</td><td>{{ $u->Email }}</td><td>{{ $u->divisionRel->DivisionName }}</td>
      <td><form method="POST" action="{{ route('admin.users.update', $u) }}" style="display:flex;gap:8px">@csrf @method('PATCH')
        <select name="Role" style="margin:0">@foreach($roles as $r)<option value="{{ $r->RoleID }}" @selected($u->Role == $r->RoleID)>{{ ucwords(str_replace('_', ' ', $r->RoleName)) }}</option>@endforeach</select>
        <button class="btn">Save</button></form></td></tr>
  @endforeach
</table></div>
@endsection
