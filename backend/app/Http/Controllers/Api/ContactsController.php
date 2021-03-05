<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactsController extends Controller
{
    public function all(): JsonResponse
    {
        $contacts = User::where("id", "<>", Auth::id())
            ->get();

        return response()->json($contacts);
    }
}
