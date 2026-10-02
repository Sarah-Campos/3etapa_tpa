# Sprint 05: Proteção de Entidade Filha (Pergunta)

**Objetivo:** proteger a entidade filha `Pergunta` para que nenhum usuário delete perguntas alheias.

> Os nomes de rotas, relações e colunas abaixo são suposições. Ajuste para os do seu projeto.

---

## Ordem de execução

1. Criar o componente `danger-button` (Ticket 1)
2. Criar a `PerguntaPolicy` e escrever o método `delete` (Ticket 2)
3. Adicionar o `authorize` no controller (Ticket 3)
4. Envolver o botão no `@can` na view (Ticket 4)
5. Rodar `php artisan test --filter=AuthZTest`

---

## Ticket 1: O Componente Visual

Arquivo: `resources/views/components/danger-button.blade.php`

```blade
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded']) }}>
    {{ $slot }}
</button>
```

---

## Ticket 2: A Regra de Negócio (Policy)

Comando:

```bash
php artisan make:policy PerguntaPolicy --model=Pergunta
```

Arquivo: `app/Policies/PerguntaPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Pergunta;
use App\Models\User;

class PerguntaPolicy
{
    public function delete(User $user, Pergunta $pergunta): bool
    {
        return $user->id === $pergunta->user_id
            || $user->id === $pergunta->evento->user_id;
    }
}
```

Pressupostos:
- `Pergunta` tem a relação `evento()`.
- O dono do evento fica em `evento.user_id`.
- No Laravel 11 ou superior a Policy é descoberta automaticamente. Em versões anteriores, registre-a no `AuthServiceProvider`:

```php
protected $policies = [
    \App\Models\Pergunta::class => \App\Policies\PerguntaPolicy::class,
];
```

---

## Ticket 3: Protegendo o Back-end

Arquivo: `EventoController` (ou `PerguntaController`)

```php
public function destroyPergunta(Evento $evento, Pergunta $pergunta)
{
    $this->authorize('delete', $pergunta);

    $pergunta->delete();

    return back();
}
```

Se `$this->authorize` não existir, confira se o controller usa o trait `AuthorizesRequests`. No Laravel 11+ o controller base não o inclui mais por padrão:

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventoController extends Controller
{
    use AuthorizesRequests;
}
```

---

## Ticket 4: Protegendo a Interface (UI)

Arquivo: `resources/views/eventos/show.blade.php`

```blade
@can('delete', $pergunta)
    <form method="POST" action="{{ route('eventos.perguntas.destroy', [$evento, $pergunta]) }}">
        @csrf
        @method('DELETE')
        <x-danger-button>Excluir Pergunta</x-danger-button>
    </form>
@endcan
```

---

## Testes de apoio (opcional)

```php
public function test_usuario_nao_pode_deletar_pergunta_alheia(): void
{
    $intruso = User::factory()->create();
    $pergunta = Pergunta::factory()->create();

    $this->actingAs($intruso)
        ->delete(route('eventos.perguntas.destroy', [$pergunta->evento, $pergunta]))
        ->assertForbidden();

    $this->assertModelExists($pergunta);
}

public function test_autor_pode_deletar_sua_pergunta(): void
{
    $pergunta = Pergunta::factory()->create();

    $this->actingAs($pergunta->user)
        ->delete(route('eventos.perguntas.destroy', [$pergunta->evento, $pergunta]))
        ->assertRedirect();

    $this->assertModelMissing($pergunta);
}

public function test_dono_do_evento_pode_deletar_qualquer_pergunta(): void
{
    $pergunta = Pergunta::factory()->create();

    $this->actingAs($pergunta->evento->user)
        ->delete(route('eventos.perguntas.destroy', [$pergunta->evento, $pergunta]))
        ->assertRedirect();

    $this->assertModelMissing($pergunta);
}
```

---

## Critérios de Aceite

- [ ] `AuthZTest` passa com 100% de sucesso
- [ ] O botão "Excluir Pergunta" some para perguntas de outros usuários
- [ ] O Laravel retorna 403 quando forçam a URL de exclusão

## Erros comuns que derrubam o `AuthZTest`

- Esquecer o `evento->user_id` na Policy, de modo que o dono do evento não consiga excluir.
- Rota sem o `{pergunta}` vinculado ao modelo (route model binding), fazendo o `authorize` receber um valor errado.
- Esconder o botão na UI mas esquecer o `authorize` no back-end. O teste acessa a URL direto e espera 403.
