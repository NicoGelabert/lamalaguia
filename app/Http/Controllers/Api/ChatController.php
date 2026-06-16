<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatContextBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct(private ChatContextBuilder $contextBuilder) {}

    public function __invoke(Request $request)
    {
        $mensaje = $request->input('mensaje');
        $historial = $request->input('historial', []);

        if (! is_string($mensaje) || trim($mensaje) === '') {
            return response()->json(['respuesta' => 'Escribí tu consulta y te ayudo.'], 422);
        }

        $ubicacion = $request->input('ubicacion');
        $ubicacion = is_array($ubicacion) ? $ubicacion : null;

        $contexto = $this->contextBuilder->build($mensaje, $historial, $ubicacion);

        $systemPrompt = $this->buildSystemPrompt($contexto);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($historial as $msg) {
            $messages[] = [
                'role' => $msg['rol'] === 'usuario' ? 'user' : 'assistant',
                'content' => $msg['contenido'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $mensaje];

        $apiKey = config('services.groq.key');
        if (! $apiKey) {
            Log::error('GROQ_API_KEY no configurada');

            return response()->json(['respuesta' => 'El asistente no está configurado. Contactá al administrador.'], 503);
        }

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.5,
            ]);

        if (! $response->successful()) {
            Log::error('Error en Groq API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json(['respuesta' => 'No pude procesar tu consulta, intentá de nuevo.'], 502);
        }

        $respuesta = $response->json('choices.0.message.content')
            ?? 'No pude procesar tu consulta, intentá de nuevo.';

        return response()->json(['respuesta' => $respuesta]);
    }

    private function buildSystemPrompt(string $contexto): string
    {
        $prompt = <<<'PROMPT'
Sos el asistente de La Malaguía, una guía para argentinos en Málaga y Andalucía, España.

Reglas:
- Respondé en español rioplatense, cálido y directo.
- Para negocios, eventos, trámites y sitios de interés, usá EXCLUSIVAMENTE la información del contexto.
- Si hay trámites y sitios de interés para una ciudad, combiná ambos: explicá el trámite y mencioná la ubicación del ayuntamiento u oficina correspondiente.
- NUNCA recomiendes un local, negocio o profesional que no figure en el contexto.
- Si el usuario pide algo en una ciudad y el contexto dice "SIN RESULTADOS EN [CIUDAD]", decilo explícitamente.
- Si hay "ALTERNATIVAS EN LA GUÍA" u "OTRAS ALTERNATIVAS", SIEMPRE mencionarlas en la respuesta.
- Si un resultado incluye distancia en km, mencionala. Si dice "CERCA DE VOS", priorizá esos lugares.
- Ejemplo de tono: "En Benalmádena no tengo referencias, pero en Fuengirola está Pipazze!"
- Si no hay datos en el contexto, decilo con honestidad. No inventes nombres, teléfonos, fechas ni direcciones.
- Podés conversar sobre vivir en España como argentino solo cuando no haya datos específicos en el contexto.
- Respuestas concisas y útiles.
- Cuando menciones contacto, usá el formato exacto: Tel: +34..., WhatsApp: +34..., Web: https://...
- Los números de WhatsApp y teléfono deben incluir prefijo internacional cuando lo tengas.
- Cuando recomiendes un negocio, evento o trámite de la guía, incluí su link interno del contexto (Guía: /negocios/...) o usá markdown [nombre](/negocios/slug).
PROMPT;

        if ($contexto !== '') {
            $prompt .= "\n\n{$contexto}";
        }

        return $prompt;
    }
}
