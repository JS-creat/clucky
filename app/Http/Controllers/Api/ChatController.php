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
            // El "System Prompt" que ya tenías configurado con tus reglas de B-EDEN
            $systemInstruction = "Eres Alessia, asistente virtual de la tienda B-EDEN. Atiendes a clientes de forma breve, clara y amigable. Si el cliente saluda respondes el saludo. Vendemos ropa de varon y mujer, responde con un saludo cordial y pregunta en qué puedes ayudar. Ayudas a encontrar ropa como poleras, abrigos, polos, pantalones y otros que puedes ver en el catálogo. Solo si realmente no sabes la respuesta o es un caso especial, sugiere amablemente contactar al número 992387342. Evita responder siempre con el número. No des respuestas largas ni técnicas.";

            $apiKey = env('GEMINI_API_KEY');
            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

            // Llamar a Gemini (API en la nube, ultra rápida)
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
                // Fallback si la API falla
                $aiMessage = "Lo siento, el servicio de asistente no está disponible en este momento. Por favor, contacta al 992387342 para atención personalizada.";
                Log::error('Error al llamar a Gemini: ' . $response->body());
            }

        } catch (\Exception $e) {
            // Manejar errores de conexión de red
            $aiMessage = "Lo siento, no puedo conectarme al asistente. Por favor, contacta al 992387342 para atención personalizada.";
            Log::error('Excepción al llamar a Gemini: ' . $e->getMessage());
        }

        // Transmitir la respuesta por Websockets exactamente como lo tenías
        broadcast(new MessageSentEvent(
            $userId,
            $aiMessage
        ));

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada',
            'ai_response' => $aiMessage
        ]);
    }


    public function checkOllamaStatus()
    {
        $apiKey = env('GEMINI_API_KEY');
        return response()->json([
            'status' => !empty($apiKey) ? 'online' : 'offline',
            'provider' => 'Google Gemini Cloud'
        ]);
    }
}
