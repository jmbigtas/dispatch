<?php
namespace App\Http\Controllers;
use App\Models\{Division, TripTicket, User};

class AdminDivisionController extends AdminCrudController
{
    protected function cfg($row = null): array
    {
        return [
            'model' => Division::class, 'route' => 'admin.divisions', 'title' => 'Divisions', 'singular' => 'Division',
            'columns' => ['Division' => fn ($m) => $m->DivisionName],
            'fields' => ['DivisionName' => ['label' => 'Division name', 'type' => 'text']],
            'rules' => ['DivisionName' => 'required|string|max:100'],
        ];
    }

    protected function inUse($row): ?string
    {
        return User::where('Division', $row->DivisionID)->exists() || TripTicket::where('RequestingDivision', $row->DivisionID)->exists()
            ? 'it still has users or trip tickets.' : null;
    }
}
