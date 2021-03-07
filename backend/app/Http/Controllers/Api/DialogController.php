<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
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
    public function index(Request $request, User $user): JsonResponse
    {
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

                DB::commit();

            } catch (\Exception $e) {

                DB::rollBack();

                throw $e;

            }

        }

        $messages = $dlg
            ? $dlg->messages()->get()
            : [];

        return response()->json($messages);
    }

    public function send(Request $request, User $user): JsonResponse
    {
        $this->validate($request, [
            "body" => "required|min:1"
        ]);

        $dlg = Dialog::withUser($user)
            ->first();

        if (!$dlg) {
            throw new \Exception("Dialog not found");
        }

        $msg = new Message();

        $msg->dialog_id = $dlg->id;

        $msg->user_id = Auth::id();

        $msg->body = $request->body;

        $msg->save();

        NewMessage::dispatch($msg);

        return response()->json($msg);
    }
}
