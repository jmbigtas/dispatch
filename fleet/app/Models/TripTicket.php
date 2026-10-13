<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TripTicket extends Model
{
    protected $table = 'Trip_Ticket';
    protected $primaryKey = 'tripTicketId';
    protected $fillable = ['DateRequested','DatePrepared','RequestedBy','RequestingDivision','numOfPassenger','TripPurpose','DepartureTime','ArrivalTime','ReturnTime','AssignedVehicle','AssignedDriver','Status'];

    protected $casts = [
        'DateRequested' => 'datetime', 'DatePrepared' => 'datetime', 'DepartureTime' => 'datetime',
        'ArrivalTime' => 'datetime', 'ReturnTime' => 'datetime',
    ];
    public function requester() { return $this->belongsTo(User::class, 'RequestedBy', 'UserID'); }
    public function divisionRel() { return $this->belongsTo(Division::class, 'RequestingDivision', 'DivisionID'); }
    public function vehicle() { return $this->belongsTo(Vehicle::class, 'AssignedVehicle', 'VehicleID'); }
    public function driver() { return $this->belongsTo(Driver::class, 'AssignedDriver', 'DriversID'); }
    public function statusRel() { return $this->belongsTo(Status::class, 'Status', 'StatusID'); }
}
