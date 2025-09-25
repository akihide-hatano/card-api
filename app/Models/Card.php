<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminata\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory as FactoriesHasFactory;

class Card extends Model
{
    use FactoriesHasFactory;

    protected $fillable = ['title','description','status','due_date','archived_at'];

    protected $casts = [
        'due_date'    => 'date',
        'archived_at' => 'datetime',
    ];
}
