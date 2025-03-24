<?php

namespace Modules\Security\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

// use Modules\Security\Database\Factories\ModuleFactory;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'modules';

    protected $fillable = [
        'name',
        'parent_id',
        'route',
        'icon',
    ];

    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id')->with('children');
    }

    // Relación de módulos con permisos (Muchos a Muchos)
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'module_has_permissions', 'module_id', 'permission_id');
    }

    // protected static function newFactory(): ModuleFactory
    // {
    //     // return ModuleFactory::new();
    // }
}
