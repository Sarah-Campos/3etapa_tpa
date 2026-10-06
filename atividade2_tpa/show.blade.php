<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $evento->titulo ?? 'Evento' }} - FalaQ</title>
</head>
<body>

    <h1>{{ $evento->titulo ?? 'Evento' }}</h1>

    <h2>Perguntas</h2>

    @forelse ($perguntas as $pergunta)
        <div class="card" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
            <p>{{ $pergunta->texto }}</p>

            {{-- Ticket #003 - nome do autor, com "Anônimo" caso nao exista --}}
            <small>Enviada por: {{ $pergunta->user->name ?? 'Anônimo' }}</small>
        </div>
    @empty
        <p>Nenhuma pergunta ainda. Seja o primeiro a perguntar!</p>
    @endforelse

    {{-- Paginacao (10 em 10) --}}
    {{ $perguntas->links() }}

</body>
</html>
