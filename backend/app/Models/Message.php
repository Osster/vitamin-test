<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "body",
    ];

    public function dialog()
    {
        return $this->belongsTo(Dialog::class, "dialog_id", "id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, "message_id", "id");
    }
}
