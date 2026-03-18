<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\MessageSentEvent;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $userId = $request->user_id;
        $userMessage = $request->message;

        $user = User::find($userId);
        $userName = $user ? $user->nombres : 'usuario';

        $response = "¡Hola $userName! Por ahora no tengo información.";

        broadcast(new MessageSentEvent(
            $userId,
            $response
        ));

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada'
        ]);
    }
}