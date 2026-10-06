<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Pergunta;

class EventoController extends Controller
{
    // Mostra um evento com a lista de perguntas dele
    public function show(Evento $evento)
    {
        // Ticket #004 - with('user') carrega os usuarios de uma vez so
        // (Eager Loading), evitando o problema N+1.
        // Mantive: filtro do evento, ordem decrescente e paginacao de 10 em 10.
        $perguntas = Pergunta::with('user')
            ->where('evento_id', $evento->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('eventos.show', compact('evento', 'perguntas'));
    }
}
