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
            ->where(function ($query) use ($myId) {
                $query
                    ->where("user_1_id", $myId)
                    ->orWhere("user_2_id", $myId);
            })
            ->get();

        return response()->json($contacts);
    }

    public function create(Request $request, User $user): JsonResponse
    {
        $myId = Auth::id();

        $contactUser = User::query()
            ->join("contacts as c", "users.id", "=", DB::raw("IF(user_1_id = '$myId}', user_2_id, user_1_id)"))
            ->select([
                "users.id",
                "users.name",
                "users.email",
                "c.dialog_id"
            ])
            ->where(function ($query) use ($myId) {
                $query
                    ->where("user_1_id", $myId)
                    ->orWhere("user_2_id", $myId);
            })
            ->where("users.id", $user->id)
            ->first();

        if (!$contactUser) {

            $contact = Contacts::create([
                "user_1_id" => $myId,
                "user_2_id" => $user->id
            ]);

        } else {

            $contact = Contacts::where([
                "user_1_id" => $myId,
                "user_2_id" => $user->id
            ])->orWhere([
                "user_2_id" => $myId,
                "user_1_id" => $user->id
            ])->first();

        }

        return response()->json($contact);
    }

    public function search(): JsonResponse
    {
        return User::where("id", "<>", Auth::id());
    }
}
