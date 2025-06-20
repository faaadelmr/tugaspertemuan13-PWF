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
        'TYPE_NAME'
    ];
}