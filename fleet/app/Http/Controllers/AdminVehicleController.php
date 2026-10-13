<?php
namespace App\Http\Controllers;
use App\Models\{Status, TripTicket, Vehicle, VehicleModel};
use Illuminate\Validation\Rule;

class AdminVehicleController extends AdminCrudController
{
    protected function cfg($row = null): array
    {
        return [
            'model' => Vehicle::class, 'route' => 'admin.vehicles', 'title' => 'Vehicles', 'singular' => 'Vehicle',
            'with' => ['vehicleModel', 'statusRel'],
            'columns' => [
                'Plate no.' => fn ($m) => $m->PlateNo,
                'Model' => fn ($m) => $m->vehicleModel->ModelName.' ('.$m->vehicleModel->Capacity.' seats)',
                'Status' => fn ($m) => $m->statusRel->StatusName,
            ],
            'fields' => [
                'ModelID' => ['label' => 'Model', 'type' => 'select',
                    'options' => VehicleModel::all()->mapWithKeys(fn ($m) => [$m->ModelID => "{$m->ModelName} ({$m->Capacity} seats)"])],
                'PlateNo' => ['label' => 'Plate number', 'type' => 'text'],
                'StatusID' => ['label' => 'Status', 'type' => 'select', 'options' => Status::where('StatusCategory', 'Vehicle')->pluck('StatusName', 'StatusID')],
            ],
            'rules' => [
                'ModelID' => 'required|exists:Vehicles_Models,ModelID',
                'PlateNo' => ['required', 'string', 'max:100', Rule::unique('Vehicles', 'PlateNo')->ignore($row?->VehicleID, 'VehicleID')],
                'StatusID' => 'required|exists:Status,StatusID',
            ],
        ];
    }

    protected function inUse($row): ?string
    {
        return TripTicket::where('AssignedVehicle', $row->VehicleID)->exists()
            ? 'it is on trip tickets. Set its status to Maintenance instead.' : null;
    }
}
