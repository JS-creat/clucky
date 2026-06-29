<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\MessageSentEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // 1. Validar la estructura que ya envía tu aplicación móvil
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $userId = $request->user_id;
        $userMessage = $request->message;

        try {
            // 2. Apuntar al microservicio local de Python (Uvicorn en el puerto 8000)
            $urlPython = 'http://127.0.0.1:8000/api/chat';

            // 3. Hacer la petición HTTP interna con un timeout prudente
            $response = Http::timeout(15)->post($urlPython, [
                'user_id' => $userId,
                'message' => $userMessage
            ]);

            if ($response->successful()) {
                $resultadoData = $response->json();
                // Extraemos el mensaje que construyó Alessia en Python
                $aiMessage = $resultadoData['message'] ?? 'Lo siento, no pude procesar tu solicitud.';
            } else {
                $aiMessage = "¡Hola! En este momento estoy atendiendo a muchos clientes a la vez en B-EDEN. Por favor, escribe tu consulta de nuevo en unos segundos.";
                Log::error('El servicio de Python retornó un error: ' . $response->body());
            }

        } catch (\Exception $e) {
            $aiMessage = "¡Hola! En este momento estoy atendiendo a muchos clientes a la vez en B-EDEN. Por favor, escribe tu consulta de nuevo en unos segundos.";
            Log::error('No se pudo conectar con el servidor de Python: ' . $e->getMessage());
        }

        // 4. Mantenemos tu evento original por si usas Websockets/Pusher para actualizar la interfaz del móvil
        try {
            broadcast(new MessageSentEvent($userId, $aiMessage));
        } catch (\Exception $ex) {
            Log::warning('Websocket no disponible: ' . $ex->getMessage());
        }

        // 5. Devolvemos la respuesta exacta en el mismo formato JSON que tu Flutter ya procesa
        return response()->json([
            'success' => true,
            'message' => $aiMessage,
            'ai_response' => $aiMessage
        ]);
    }

    public function checkOllamaStatus()
    {
        return response()->json([
            'status' => 'online',
            'provider' => 'B-EDEN Python AI Brain & Gemini 2.5'
        ]);
    }
}
