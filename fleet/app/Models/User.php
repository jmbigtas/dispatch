<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'Users';
    protected $primaryKey = 'UserID';
    protected $fillable = ['Division', 'FirstName', 'LastName', 'Email', 'passwordhash', 'Role'];
    protected $hidden = ['passwordhash'];

    protected function casts(): array { return ['passwordhash' => 'hashed']; }

    // The schema stores the hash in `passwordhash` and has no remember_token column.
    public function getAuthPasswordName() { return 'passwordhash'; }
    public function getAuthPassword() { return $this->passwordhash; }
    public function getRememberTokenName() { return ''; }

    public function roleRel() { return $this->belongsTo(Role::class, 'Role', 'RoleID'); }
    public function divisionRel() { return $this->belongsTo(Division::class, 'Division', 'DivisionID'); }
    public function hasRole(string ...$roles): bool { return in_array($this->roleRel?->RoleName, $roles, true); }
    public function getNameAttribute(): string { return "{$this->FirstName} {$this->LastName}"; }
    public function getRoleLabelAttribute(): string { return ucwords(str_replace('_', ' ', $this->roleRel?->RoleName ?? '')); }
}
