<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Negocio;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $mensaje = $request->input('mensaje');
        $historial = $request->input('historial', []);

        // Contexto de la BD
        $contexto = $this->buildContexto($mensaje);

        // System prompt
        $systemPrompt = "Sos un asistente de La Malaguía, una guía para argentinos en Málaga y Andalucía, España. 
        Respondés en español rioplatense, de forma cálida y directa. 
        Ayudás con trámites, negocios, profesionales y eventos de la comunidad argentina.
        Solo respondés sobre temas relacionados con vivir en Málaga como argentino.
        {$contexto}";

        // Armar mensajes para Groq
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($historial as $msg) {
            $messages[] = [
                'role' => $msg['rol'] === 'usuario' ? 'user' : 'assistant',
                'content' => $msg['contenido'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $mensaje];

        // Llamada a Groq
        $response = Http::withToken(config('services.groq.key'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

        $respuesta = $response->json('choices.0.message.content') ?? 'No pude procesar tu consulta, intentá de nuevo.';

        return response()->json(['respuesta' => $respuesta]);
    }

    private function buildContexto(string $mensaje): string
    {
        $contexto = '';
        $mensajeLower = strtolower($mensaje);

        // Eventos próximos
        if (str_contains($mensajeLower, 'evento') || str_contains($mensajeLower, 'actividad')) {
            $eventos = Evento::where('activo', true)
                ->where('fecha', '>=', now())
                ->orderBy('fecha')
                ->take(5)
                ->get(['nombre', 'fecha', 'lugar', 'descripcion']);

            if ($eventos->isNotEmpty()) {
                $contexto .= "\nPróximos eventos:\n";
                foreach ($eventos as $evento) {
                    $contexto .= "- {$evento->nombre} | {$evento->fecha} | {$evento->lugar}\n";
                }
            }
        }

        // Negocios
        if (str_contains($mensajeLower, 'negocio') || str_contains($mensajeLower, 'profesional') || str_contains($mensajeLower, 'busco') || str_contains($mensajeLower, 'buscando')) {
            $negocios = Negocio::where('activo', true)
                ->with('categoria')
                ->take(10)
                ->get(['nombre', 'descripcion', 'telefono', 'whatsapp', 'categoria_negocio_id']);

            if ($negocios->isNotEmpty()) {
                $contexto .= "\nNegocios y profesionales disponibles:\n";
                foreach ($negocios as $negocio) {
                    $contexto .= "- {$negocio->nombre} ({$negocio->categoria?->nombre})\n";
                }
            }
        }

        // Trámites
        if (str_contains($mensajeLower, 'trámite') || str_contains($mensajeLower, 'tramite') || str_contains($mensajeLower, 'nie') || str_contains($mensajeLower, 'empadron')) {
            $tramites = Tramite::where('activo', true)
                ->orderBy('orden')
                ->get(['titulo', 'categoria', 'slug']);

            if ($tramites->isNotEmpty()) {
                $contexto .= "\nTrámites disponibles en la guía:\n";
                foreach ($tramites as $tramite) {
                    $contexto .= "- {$tramite->titulo} ({$tramite->categoria})\n";
                }
            }
        }

        return $contexto;
    }
}