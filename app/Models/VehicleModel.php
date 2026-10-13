<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    protected $table = 'Vehicles_Models';
    protected $primaryKey = 'ModelID';
    protected $fillable = ['ModelName','Type','Capacity'];

}
