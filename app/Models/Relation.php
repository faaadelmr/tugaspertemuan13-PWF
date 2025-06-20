<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    use HasFactory;

    protected $table = 't_relation';
    protected $primaryKey = 'RELATION_CODE';
    public $timestamps = false;

    protected $fillable = [
        'RELATION_CODE',
        'RELATION_DESC',
        'INSERT_TIME',
    ];

    protected $casts = [
        'INSERT_TIME' => 'datetime',
    ];
}