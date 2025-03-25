<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Security\Models\Module;
use Modules\Security\Models\Office;
use Modules\Security\Models\Person;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'person_id',
        /* 'name', */
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Devuelve un array con cualquier información adicional que se desee agregar al token JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relación 1 a 1 con Persona
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function offices() {
        return $this->belongsToMany(Office::class, 'office_users')
                    ->withTimestamps()
                    ->withTrashed();
    }

    public function rolesInOffice($officeId) {
        return $this->belongsToMany(Role::class, 'office_user_roles')
                    ->wherePivot('office_id', $officeId)
                    ->withTimestamps()
                    ->withTrashed();
    }

    public function additionalPermissionsInOffice($officeId) {
        return $this->belongsToMany(Permission::class, 'office_user_permissions')
                    ->wherePivot('office_id', $officeId)
                    ->withTimestamps()
                    ->withTrashed();
    }

   // Obtener roles del usuario en una oficina específica
   public function getRolesByOffice($officeId)
   {
       return Role::whereIn('id', function ($query) use ($officeId) {
           $query->select('role_id')
                 ->from('office_user_roles')
                 ->where('user_id', $this->id)
                 ->where('office_id', $officeId);
       })->pluck('name');
   }

   // Obtener permisos del usuario en una oficina específica
   public function getPermissionsByOffice($officeId)
   {
       $rolePermissions = Permission::whereIn('id', function ($query) use ($officeId) {
           $query->select('permission_id')
                 ->from('role_has_permissions')
                 ->whereIn('role_id', function ($subQuery) use ($officeId) {
                     $subQuery->select('role_id')
                              ->from('office_user_roles')
                              ->where('user_id', $this->id)
                              ->where('office_id', $officeId);
                 });
       })->pluck('name');

       $directPermissions = Permission::whereIn('id', function ($query) use ($officeId) {
           $query->select('permission_id')
                 ->from('office_user_permissions')
                 ->where('user_id', $this->id)
                 ->where('office_id', $officeId);
       })->pluck('name');

       return $rolePermissions->merge($directPermissions)->unique()->values();
   }

   // Obtener módulos a los que el usuario tiene acceso según los permisos en una oficina específica
   public function getModulesByOffice($officeId)
   {
       return Module::whereIn('id', function ($query) use ($officeId) {
           $query->select('module_id')
                 ->from('module_has_permissions')
                 ->whereIn('permission_id', function ($subQuery) use ($officeId) {
                     $subQuery->select('id')->from('permissions')
                              ->whereIn('name', $this->getPermissionsByOffice($officeId));
                 });
       })->get();
   }



}
