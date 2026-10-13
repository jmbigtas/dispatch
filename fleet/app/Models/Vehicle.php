<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'Vehicles';
    protected $primaryKey = 'VehicleID';
    protected $fillable = ['ModelID','PlateNo','StatusID'];

    public function vehicleModel() { return $this->belongsTo(VehicleModel::class, 'ModelID', 'ModelID'); }
    public function statusRel() { return $this->belongsTo(Status::class, 'StatusID', 'StatusID'); }
    public function trips() { return $this->hasMany(TripTicket::class, 'AssignedVehicle', 'VehicleID'); }
}
