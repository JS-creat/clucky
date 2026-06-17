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
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $userId = $request->user_id;
        $userMessage = $request->message;

        $user = User::find($userId);
        $userName = $user ? $user->nombres : 'usuario';

        try {
            $systemInstruction = "Eres Alessia, asistente virtual de la tienda B-EDEN. Atiendes a clientes de forma breve, clara y amigable. Si el cliente saluda respondes el saludo. Vendemos ropa de varon y mujer, responde con un saludo cordial y pregunta en qué puedes ayudar. Ayudas a encontrar ropa como poleras, abrigos, polos, pantalones y otros que puedes ver en el catálogo. Solo si realmente no sabes la respuesta o es un caso especial, sugiere amablemente contactar al número 992387342. Evita responder siempre con el número. No des respuestas largas ni técnicas.";

            $apiKey = config('services.gemini.key');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . trim($apiKey);

            $response = Http::timeout(15)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $userMessage]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $geminiResponse = $response->json();
                $aiMessage = $geminiResponse['candidates'][0]['content']['parts'][0]['text'] ?? 'Lo siento, no pude procesar tu solicitud.';
            } else {
                //Si Google responde con error (403 o 503)
                $aiMessage = "¡Hola! En este momento estoy atendiendo a muchos clientes a la vez en B-EDEN. Por favor, escribe tu consulta de nuevo en unos segundos para poder ayudarte.";
                Log::error('Error al llamar a Gemini: ' . $response->body());
            }
        } catch (\Exception $e) {
            // Si hay un problema de red o timeout total
            $aiMessage = "¡Hola! En este momento estoy atendiendo a muchos clientes a la vez en B-EDEN. Por favor, escribe tu consulta de nuevo en unos segundos para poder ayudarte.";
            Log::error('Excepción al llamar a Gemini: ' . $e->getMessage());
        }

        // Transmitir por Websockets (si usas el evento)
        broadcast(new MessageSentEvent(
            $userId,
            $aiMessage
        ));

        return response()->json([
            'success' => true,
            'message' => $aiMessage,
            'ai_response' => $aiMessage
        ]);
    }

    public function checkOllamaStatus()
    {
        $apiKey = config('services.gemini.key');
        return response()->json([
            'status' => !empty($apiKey) ? 'online' : 'offline',
            'provider' => 'Google Gemini Cloud'
        ]);
    }
}
