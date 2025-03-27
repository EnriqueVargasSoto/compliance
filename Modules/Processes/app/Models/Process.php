<?php

namespace Modules\Processes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Processes\Database\Factories\ProcessFactory;

class Process extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ProcessFactory
    // {
    //     // return ProcessFactory::new();
    // }
}
