<?php

namespace Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Security\Database\Factories\PersonFactory;

class Person extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'people';

    protected $fillable = [
        'dni',
        'names',
        'surnames',
        'email',
        'address',
        'phone',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'person_id');
    }

    // protected static function newFactory(): PersonFactory
    // {
    //     // return PersonFactory::new();
    // }
}
