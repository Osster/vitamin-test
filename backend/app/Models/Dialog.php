<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dialog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "state"
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
