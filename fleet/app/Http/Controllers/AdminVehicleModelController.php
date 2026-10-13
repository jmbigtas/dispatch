<?php
namespace App\Http\Controllers;
use App\Models\{Vehicle, VehicleModel};

class AdminVehicleModelController extends AdminCrudController
{
    protected function cfg($row = null): array
    {
        return [
            'model' => VehicleModel::class, 'route' => 'admin.vehicle-models', 'title' => 'Vehicle models', 'singular' => 'Vehicle model',
            'columns' => ['Model' => fn ($m) => $m->ModelName, 'Type' => fn ($m) => $m->Type, 'Capacity' => fn ($m) => $m->Capacity.' seats'],
            'fields' => [
                'ModelName' => ['label' => 'Model name', 'type' => 'text'],
                'Type' => ['label' => 'Type (Sedan, Van, ...)', 'type' => 'text'],
                'Capacity' => ['label' => 'Seat capacity', 'type' => 'number'],
            ],
            'rules' => ['ModelName' => 'required|string|max:100', 'Type' => 'required|string|max:100', 'Capacity' => 'required|integer|min:1|max:100'],
        ];
    }

    protected function inUse($row): ?string
    {
        return Vehicle::where('ModelID', $row->ModelID)->exists() ? 'vehicles still use this model.' : null;
    }
}
