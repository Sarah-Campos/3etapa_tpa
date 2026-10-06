<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerguntaRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        return [
           
            'texto' => 'required|string|min:10|max:255',

        
            'evento_id' => 'required|exists:eventos,id',
        ];
    }
}



namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Pergunta;

class EventoController extends Controller
{
    public function show($id)
    {
        
        $evento = Evento::findOrFail($id);

        $perguntas = Pergunta::where('evento_id', $evento->id)
            ->latest()
            ->paginate(10);

        
        return view('eventos.show', compact('evento', 'perguntas'));
    }
}


