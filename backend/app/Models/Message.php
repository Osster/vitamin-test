<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "body",
    ];

    protected $appends = [
        'at',
    ];

    public function dialog()
    {
        return $this->belongsTo(Dialog::class, "dialog_id", "id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, "message_id", "id");
    }

    public function getAtAttribute()
    {
        return $this->updated_at->timestamp;
    }

    public function getAttachmentsAttribute()
    {
        $attachments = [];

        foreach ($this->attachments as $attachment) {
            $attachments[] = $attachment->public_path;
        }

        return $attachments;
    }
}
