<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    public function all(): JsonResponse
    {
        $myId = Auth::id();

        $contacts = User::query()
            ->join("contacts as c", "users.id", "=", DB::raw("IF(user_1_id = '$myId}', user_2_id, user_1_id)"))
            ->select([
                "users.id",
                "users.name",
                "users.email",
                "c.dialog_id"
            ])
            ->get();

        return response()->json($contacts);
    }
}
