<?php

// app/Http/Controllers/PerguntaController.php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class PerguntaController extends Controller
{
    public function store(Request $request, Evento $evento)
    {
        $request->validate([
            'conteudo' => 'required|string|min:3|max:500',
        ], [
            'conteudo.required' => 'A pergunta não pode estar em branco.',
            'conteudo.min'      => 'A pergunta deve ter pelo menos 3 caracteres.',
            'conteudo.max'      => 'A pergunta deve ter no máximo 500 caracteres.',
        ]);

        $evento->perguntas()->create([
            'conteudo' => $request->conteudo,
        ]);

        return redirect()->route('eventos.show', $evento);
    }
}
