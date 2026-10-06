<?php


Route::post('/eventos/{evento}/perguntas', [PerguntaController::class, 'store'])
    ->middleware('auth')
    ->name('perguntas.store');


public function show(Evento $evento)
{
    
    $perguntas = Pergunta::with('user')
        ->where('evento_id', $evento->id)
        ->where('is_public', true)
        ->latest()
        ->paginate(10);

    return view('eventos.show', compact('evento', 'perguntas'));
}


