<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'Division';
    protected $primaryKey = 'DivisionID';
    protected $fillable = ['DivisionName'];

}
