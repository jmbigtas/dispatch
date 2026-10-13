<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'Status';
    protected $primaryKey = 'StatusID';
    protected $fillable = ['StatusCategory','StatusName','StatusDesc'];
    public $timestamps = false;

    public static function idOf(string $category, string $name): int
    {
        return (int) static::where(['StatusCategory' => $category, 'StatusName' => $name])->value('StatusID');
    }
}
