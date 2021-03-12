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
            ->join("contacts as c", "users.id", "=", DB::raw("IF(user_1_id = {$myId}, user_2_id, user_1_id)"))
            ->leftJoin("dialogs as dg", "dg.id", "=", "c.dialog_id")
            ->leftJoin("messages as m", "m.dialog_id", "=", "dg.id")
            ->leftJoin('message_user as mu', function ($query) use ($myId) {
                $query
                    ->whereRaw("mu.message_id = m.id")
                    ->whereRaw("mu.user_id = {$myId}")
                    ->whereNull("mu.viewed_at");
            })
            ->select([
                "users.id",
                "users.name",
                "users.email",
                "c.dialog_id",
                DB::raw("COUNT(m.id) as m_total"),
                DB::raw("COUNT(mu.id) as m_unread")
            ])
            ->where(function ($query) use ($myId) {
                $query
                    ->where("user_1_id", $myId)
                    ->orWhere("user_2_id", $myId);
            })
            ->whereNull("dg.deleted_at")
            ->whereNull("m.deleted_at")
            ->groupBy([
                "users.id",
                "c.dialog_id"
            ]);

        return response()->json($contacts->get());
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
