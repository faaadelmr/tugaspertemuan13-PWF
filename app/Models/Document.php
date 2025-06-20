<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 't_document';
    protected $primaryKey = 'TYPE_ID';
    public $timestamps = false;

    protected $fillable = [
        'TYPE_ID',
        'TYPE_NAME',
        'INSERT_TIME',
        'UPDATED_BY',
    ];

    protected $casts = [
        'INSERT_TIME' => 'datetime',
    ];
}