<?php
namespace App\Http\Controllers;
use App\Models\{Driver, Status, TripTicket};

class AdminDriverController extends AdminCrudController
{
    protected function cfg($row = null): array
    {
        return [
            'model' => Driver::class, 'route' => 'admin.drivers', 'title' => 'Drivers', 'singular' => 'Driver',
            'with' => ['statusRel'],
            'columns' => ['Name' => fn ($m) => $m->full_name, 'Contact' => fn ($m) => $m->ContactNo, 'Status' => fn ($m) => $m->statusRel->StatusName],
            'fields' => [
                'FirstName' => ['label' => 'First name', 'type' => 'text'],
                'LastName' => ['label' => 'Last name', 'type' => 'text'],
                'ContactNo' => ['label' => 'Contact number', 'type' => 'text'],
                'Status' => ['label' => 'Status', 'type' => 'select', 'options' => Status::where('StatusCategory', 'Driver')->pluck('StatusName', 'StatusID')],
            ],
            'rules' => [
                'FirstName' => 'required|string|max:100', 'LastName' => 'required|string|max:100',
                'ContactNo' => 'required|string|max:13', 'Status' => 'required|exists:Status,StatusID',
            ],
        ];
    }

    protected function inUse($row): ?string
    {
        return TripTicket::where('AssignedDriver', $row->DriversID)->exists()
            ? 'the driver is on trip tickets. Set the status to On Leave instead.' : null;
    }
}
