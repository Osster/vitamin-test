<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\Models\Dialog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DialogController extends Controller
{
    public function index(Request $request, Dialog $dialog): JsonResponse
    {
        $messages = $dialog->messages()->get();

        return response()->json($messages);
    }

    public function create(Request $request, User $user): JsonResponse {

        $dlg = Dialog::withUser($user)
            ->first();

        if (!$dlg) {

            try {

                DB::beginTransaction();

                $dlg = new Dialog();

                $dlg->state = 'pending';

                $dlg->save();

                $dlg->users()->attach(Auth::id());

                $dlg->users()->attach($user->id);

                Contacts::query()
                    ->where(function ($query) use ($user) {
                        $query
                            ->where("user_1_id", $user->id)
                            ->where("user_2_id", Auth::id());
                    })
                    ->orWhere(function ($query) use ($user) {
                        $query
                            ->where("user_1_id", Auth::id())
                            ->where("user_2_id", $user->id);
                    })
                    ->update(["dialog_id" => $dlg->id]);

                DB::commit();

            } catch (\Exception $e) {

                DB::rollBack();

                throw $e;

            }

        }

        return response()->json($dlg);
    }

    public function send(Request $request, Dialog $dialog): JsonResponse
    {
        $this->validate($request, [
            "body" => "required|min:1"
        ]);

        $msg = new Message();

        $msg->dialog_id = $dialog->id;

        $msg->user_id = Auth::id();

        $msg->body = $request->body;

        $msg->save();

        broadcast(new NewMessage($msg))->toOthers();

        return response()->json($msg);
    }
}
