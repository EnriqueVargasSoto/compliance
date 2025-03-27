<?php

namespace Modules\Batches\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Batches\Database\Factories\BatchFactory;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'batches';

    protected $fillable = [
        'id',
        'batch',
        'date_init',
        'date_end',
        'status',
        'signed'
    ];

    // protected static function newFactory(): BatchFactory
    // {
    //     // return BatchFactory::new();
    // }
}
