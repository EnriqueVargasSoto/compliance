<?php

namespace Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

// use Modules\Security\Database\Factories\OfficeFactory;

class Office extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'offices';

    protected $fillable = [
        'name',
        'company_id',
        'email',
        'phone',
        'address',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'office_users')
                    ->withTimestamps()
                    ->withTrashed();
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'office_user_roles')
                    ->withTimestamps()
                    ->withTrashed();
    }

    // protected static function newFactory(): OfficeFactory
    // {
    //     // return OfficeFactory::new();
    // }
}
