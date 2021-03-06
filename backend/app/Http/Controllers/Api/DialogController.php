<?php

namespace App\Http\Controllers\Api;

use App\Events\DeleteMessage;
use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Contacts;
use App\Models\Dialog;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DialogController extends Controller
{
    public function index(Request $request, Dialog $dialog): JsonResponse
    {
        $messages = $dialog
            ->messages()
            ->leftJoin("message_user as mu", function($q) {
                $q
                    ->on("mu.message_id", "=", "messages.id")
                    ->whereRaw("mu.user_id = " . Auth::id())
                    ->whereNull("mu.viewed_at");
            })
            ->with(["attachments"])
            ->groupBy([
                "messages.id"
            ])
            ->select([
                "messages.*",
                DB::raw("COUNT(mu.id) as unread")
            ]);

        // dd($messages->toSql());

        return response()->json($messages->get());
    }

    public function create(Request $request, User $user): JsonResponse
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
            "id" => "nullable|integer",
            "body" => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->files->count() == 0 && is_null($value)) {
                        $fail('The ' . $attribute . ' can not be empty.');
                    }
                },
            ]
        ]);

        try {

            DB::beginTransaction();

            $msg = ($request->id)
                ? Message::findOrFail($request->id)
                : new Message();

            $msg->dialog_id = $dialog->id;

            $msg->user_id = Auth::id();

            $msg->body = $request->body;

            $msg->save();

            $recipients = $dialog->users()
                ->where("users.id", "<>", Auth::id())
                ->pluck("users.id");

            foreach ($recipients as $recipient) {

                $msg->recipients()->attach($recipient);

            }

            if ($request->files) {
                foreach ($request->files as $file) {
                    try {
                        $path = Storage::putFileAs(
                            "public/attachments/{$dialog->id}",
                            $file,
                            "{$msg->id}." . $file->getClientOriginalExtension()
                        );
                        Attachment::create([
                            "message_id" => $msg->id,
                            "file_name" => $path,
                            "hash" => md5_file(Storage::path($path)),
                            "is_uploaded" => true
                        ]);
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $msg = $msg->fresh(["attachments"]);

            broadcast(new NewMessage($msg))->toOthers();

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;

        }

        return response()->json($msg);
    }

    public function delete(Request $request, Dialog $dialog, Message $message): JsonResponse
    {
        if ($message->dialog_id == $dialog->id && $message->user_id == Auth::id()) {

            $message->delete();

            broadcast(new DeleteMessage($message))->toOthers();

            return response()->json(["result" => "OK"]);

        }

        throw new \Exception("Record can not be deleted");
    }

    public function updateViewed(Request $request, Dialog $dialog): JsonResponse
    {
        $request->validate([
            "messages" => "array"
        ]);

        $allowedUserId = $dialog->users()
            ->where("users.id", "<>", Auth::id())
            ->pluck("users.id");

        DB::table("message_user as mu")
            ->whereIn("mu.message_id", $request->messages)
            ->where("mu.user_id", Auth::id())
            ->update([
                "viewed_at" => Carbon::now()
            ]);

        $messages = $dialog
            ->messages()
            ->leftJoin("message_user as mu", function($q) {
                $q
                    ->on("mu.message_id", "=", "messages.id")
                    ->whereRaw("mu.user_id = " . Auth::id())
                    ->whereNull("mu.viewed_at");
            })
            ->with(["attachments"])
            ->whereIn("messages.id", $request->messages)
            ->select([
                "messages.*",
                DB::raw("COUNT(mu.id) as unread")
            ])
            ->groupBy([
                "messages.id"
            ]);

        return response()->json($messages->get());
    }
}
