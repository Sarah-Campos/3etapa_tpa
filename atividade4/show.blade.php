{{-- resources/views/eventos/show.blade.php --}}
{{-- Ajuste os nomes de rota/variáveis ($evento, $evento->perguntas) conforme o seu projeto --}}

<div class="max-w-2xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">{{ $evento->titulo }}</h1>

    {{-- ===== Ticket #007 + #008: Formulário de envio de perguntas ===== --}}
    <form action="{{ route('perguntas.store', $evento) }}" method="POST" class="mb-8">
        @csrf

        <label for="conteudo" class="block text-sm font-medium text-gray-700 mb-2">
            Faça sua pergunta
        </label>

        {{-- Borda vermelha condicional + old() para não perder o texto --}}
        <textarea
            name="conteudo"
            id="conteudo"
            rows="4"
            class="w-full rounded-md border p-3 focus:outline-none focus:ring-2 focus:ring-blue-500
                   @error('conteudo') border-red-500 @enderror"
            placeholder="Digite sua pergunta..."
        >{{ old('conteudo') }}</textarea>

        {{-- Mensagem de erro em vermelho --}}
        @error('conteudo')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        {{-- Botão estilizado com Tailwind --}}
        <button
            type="submit"
            class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
        >
            Enviar pergunta
        </button>
    </form>

    {{-- ===== Ticket #008: Mural de perguntas (cards estilo balão de chat) ===== --}}
    <h2 class="text-xl font-semibold mb-4">Perguntas do público</h2>

    @forelse ($evento->perguntas as $pergunta)
        <div class="mb-4 p-4 bg-gray-100 rounded-lg shadow-sm">
            <p class="text-gray-800">{{ $pergunta->conteudo }}</p>
            <span class="text-xs text-gray-500">{{ $pergunta->created_at->diffForHumans() }}</span>
        </div>
    @empty
        <p class="text-gray-500">Nenhuma pergunta ainda. Seja o primeiro!</p>
    @endforelse

</div>

{{-- ===== No Controller (PerguntaController@store), garanta a validação ===== --}}
{{--
public function store(Request $request, Evento $evento)
{
    $request->validate([
        'conteudo' => 'required|string|min:3|max:500',
    ], [
        'conteudo.required' => 'A pergunta não pode estar em branco.',
    ]);

    $evento->perguntas()->create([
        'conteudo' => $request->conteudo,
    ]);

    return redirect()->route('eventos.show', $evento);
}
--}}
