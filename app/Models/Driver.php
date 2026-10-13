<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'Drivers';
    protected $primaryKey = 'DriversID';
    protected $fillable = ['FirstName','LastName','ContactNo','Status'];

    public function statusRel() { return $this->belongsTo(Status::class, 'Status', 'StatusID'); }
    public function trips() { return $this->hasMany(TripTicket::class, 'AssignedDriver', 'DriversID'); }
    public function getFullNameAttribute(): string { return "{$this->FirstName} {$this->LastName}"; }
}
