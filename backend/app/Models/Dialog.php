<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dialog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "state"
    ];

    protected $appends = [
        //"dialog_users",
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function scopeWithUser($query, User $user)
    {
        $userId = $user->id;

        $selfId = Auth::id();

        $regExp = "\"{$userId}\"?[,\d\"]*\"{$selfId}\"|\"{$selfId}\"?[,\d\"]*\"{$userId}\"";

        return $query
            ->leftJoin("dialog_user AS du", "dialogs.id", "=", "du.dialog_id")
            ->select([
                "dialogs.*",
                DB::raw("GROUP_CONCAT(CONCAT('\"', du.user_id, '\"') SEPARATOR ',') as dialog_users")
            ])
            ->groupBy('dialogs.id')
            ->havingRaw("dialog_users REGEXP '{$regExp}'");
    }

//    public function getDialogUsersAttribute()
//    {
//        return ""; //$this->dialog_users | "";
//    }
}
