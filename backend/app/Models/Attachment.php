<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "message_id",
        "file_name",
        "hash",
        "is_uploaded"
    ];

    protected $casts = [
        "is_uploaded" => "boolean"
    ];

    protected $appends = [
        "public_path"
    ];

    public function getPublicPathAttribute()
    {
        return Storage::url($this->file_name);
    }
}
