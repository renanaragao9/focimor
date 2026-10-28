---
name: crud-controller-astleta
description: >
  Padrão de CRUD via API REST usado no projeto astleta: Controller fino que apenas delega,
  um Service por ação (Index/View/Store/Update/Delete), FormRequests para validação,
  API Resource para a resposta e respostas padronizadas via BaseController.
  Use ao criar qualquer novo CRUD de recurso na API (ex.: PetController, SwipeController).
---

# CRUD com Controller — padrão astleta

Arquitetura em camadas finas. O Controller NÃO contém regra de negócio: ele valida
(via FormRequest injetado), chama um Service (injetado por ação) e serializa a saída
(via Resource). Toda a lógica vive nos Services.

Fluxo de uma requisição:

```
Request HTTP
  -> Route (routes/api.php)
  -> FormRequest (valida + autoriza)
  -> Controller (delega, 1 método por ação)
  -> Service (regra de negócio, dentro de DB::transaction nas escritas)
  -> Model (Eloquent)
  -> Resource (formata JSON)
  -> BaseController::successResponse (envelope padrão)
```

## Estrutura de pastas (por recurso `Foo`)

```
app/Http/Controllers/Api/{Contexto}/FooController.php
app/Http/Requests/Api/{Contexto}/Foo/IndexFooRequest.php
app/Http/Requests/Api/{Contexto}/Foo/StoreFooRequest.php
app/Http/Requests/Api/{Contexto}/Foo/UpdateFooRequest.php
app/Http/Resources/{Contexto}/FooResource.php
app/Services/Api/{Contexto}/Foo/IndexFooService.php
app/Services/Api/{Contexto}/Foo/ViewFooService.php
app/Services/Api/{Contexto}/Foo/StoreFooService.php
app/Services/Api/{Contexto}/Foo/UpdateFooService.php
app/Services/Api/{Contexto}/Foo/DeleteFooService.php
```

`{Contexto}` é o agrupamento (ex.: `Company`). Classes base compartilhadas:
`App\Http\Controllers\Api\BaseController`, `App\Services\BaseService`,
`App\Http\Requests\Api\Global\Base\BaseFormRequest`.

## Convenções-chave

- `declare(strict_types=1);` no topo de controllers, services e requests.
- Controller estende `BaseController` e injeta o Service e o Request por método.
- Cada método do controller tem no máximo 3 passos: `$data = $fooRequest->validated();`,
  `$result = $fooService->run($data);`, `return $this->successResponse(new FooResource($result), 'msg');`.
  Parâmetros em linhas separadas e variáveis com o MESMO nome da classe (ver seção 4).
- Services estendem `BaseService` e expõem um único método público `run(...)`.
- **Nunca use um valor de array direto** (`$data['x']`, `$result['x']`) no meio da lógica: importe cada valor
  para uma variável no início do método (`$unread = $data['filters']['unread'] ?? null;`) e use só a variável.
  Nos `Index*Service`, o bloco de importação segue sempre esta ordem: `$page`, `$perPage`, `$sort`, `$direction`, `$search`,
  depois os filtros (`$data['filters'][...]`) e, por fim, as variáveis derivadas (pula-se a linha que o service não usa).
  Nos Services de escrita, importe os valores de `$data` antes de autorização, `getUserId()` e `DB::transaction`.
  Ao criar registros, passe as variáveis (`'title' => $title`) ou monte com `array_merge`/`Arr::except`; não grave chaves em `$data`.
- Escritas (`store`/`update`/`destroy`) rodam dentro de `DB::transaction`.
- `index` usa paginação + filtros com `when()` + ordenação via `SortableTrait::applySorting`.
- Resource formata datas (`?->format('d/m/Y H:i:s')`) e usa `whenLoaded` para relações.
- Mensagens de validação em português no `messages()` do Request.
- Respostas sempre pelo envelope `successResponse`/`errorResponse`.

---

## 1. BaseController (já existe — não recriar)

```php
class BaseController extends Controller
{
    public function successResponse($data = null, string $message = 'Dados retornados com sucesso'): JsonResponse
    {
        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data]);
    }

    public function errorResponse(array $errors, string $message, int $statusCode = 500): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message, 'errors' => $errors], $statusCode);
    }
}
```

## 2. BaseService (já existe — não recriar)

Expõe helpers de autenticação: `getUserId()`, `getUser()` e, no astleta, `getCompany()`.
Services concretos estendem esta classe.

## 3. BaseFormRequest (já existe — não recriar)

- `authorize(): true`
- `paginationRules()`: `direction|per_page|page|sort`
- `failedValidation()`: lança 422 com envelope `status/message/messages/errors`
- helpers de sanitização (`sanitizeJsonData`, `removeHtmlTags`, `removeJavaScriptFunctions`)

## 4. Controller

### Formato obrigatório do método

Todo método do controller segue EXATAMENTE este formato:

- Cada parâmetro em sua própria linha (indentado), com `): JsonResponse {` na linha seguinte.
- O parâmetro do FormRequest tem o **mesmo nome da classe** em camelCase
  (`RegisterRequest $registerRequest`) e o do Service também
  (`RegisterService $registerService`). Nada de `$request`/`$service` genéricos.
- Passo 1: `$data = $registerRequest->validated();`
- Passo 2: `$result = $registerService->run($data);` — o Service sempre recebe `$data`.
- Passo 3: `return $this->successResponse(...)`.
- Quando o Service pode falhar de forma esperada, o controller devolve
  `errorResponse([], 'mensagem', 422)` (nunca 500 para erro de validação/regra).
- Sem Request (ex.: `show`, `destroy`, `me`, `logout`) o método segue o mesmo estilo
  de parâmetros em linhas separadas quando houver mais de um; o Service continua
  nomeado pela classe (`$logoutService`).

```php
public function register(
    RegisterRequest $registerRequest,
    RegisterService $registerService
): JsonResponse {
    $data = $registerRequest->validated();
    $user = $registerService->run($data);

    return $this->successResponse([
        'me' => new AuthResource($user),
        'requires_verification' => true,
    ], 'Usuário registrado com sucesso! Verifique seu e-mail para ativar sua conta.', 201);
}
```

### Exemplo completo (CRUD)

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Expense\IndexExpenseRequest;
use App\Http\Requests\Api\Company\Expense\StoreExpenseRequest;
use App\Http\Requests\Api\Company\Expense\UpdateExpenseRequest;
use App\Http\Resources\Company\ExpenseResource;
use App\Models\Expense;
use App\Services\Api\Company\Expense\DeleteExpenseService;
use App\Services\Api\Company\Expense\IndexExpenseService;
use App\Services\Api\Company\Expense\StoreExpenseService;
use App\Services\Api\Company\Expense\UpdateExpenseService;
use App\Services\Api\Company\Expense\ViewExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseController extends BaseController
{
    public function index(
        IndexExpenseRequest $indexExpenseRequest,
        IndexExpenseService $indexExpenseService
    ): AnonymousResourceCollection {
        $data = $indexExpenseRequest->validated();
        $expenses = $indexExpenseService->run($data);

        return ExpenseResource::collection($expenses);
    }

    public function show(
        Expense $expense,
        ViewExpenseService $viewExpenseService
    ): JsonResponse {
        $expense = $viewExpenseService->run($expense);

        return $this->successResponse(new ExpenseResource($expense), 'Despesa encontrada com sucesso.');
    }

    public function store(
        StoreExpenseRequest $storeExpenseRequest,
        StoreExpenseService $storeExpenseService
    ): JsonResponse {
        $data = $storeExpenseRequest->validated();
        $expense = $storeExpenseService->run($data);

        return $this->successResponse(new ExpenseResource($expense), 'Despesa criada com sucesso.');
    }

    public function update(
        Expense $expense,
        UpdateExpenseRequest $updateExpenseRequest,
        UpdateExpenseService $updateExpenseService
    ): JsonResponse {
        $data = $updateExpenseRequest->validated();
        $expense = $updateExpenseService->run($expense, $data);

        return $this->successResponse(new ExpenseResource($expense), 'Despesa atualizada com sucesso.');
    }

    public function destroy(
        Expense $expense,
        DeleteExpenseService $deleteExpenseService
    ): JsonResponse {
        $deleteExpenseService->run($expense);

        return $this->successResponse(null, 'Despesa removida com sucesso.');
    }
}
```

Observações do `ProductController`: além do CRUD, métodos de ação extra (ex.:
`selectProducts`, `previewMenuImport`) seguem o MESMO formato — 1 Request + 1 Service + `successResponse`.

---

## 5. IndexService (listagem com filtros + paginação + ordenação)

```php
<?php

declare(strict_types=1);

namespace App\Services\Api\Company\Expense;

use App\Models\Expense;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexExpenseService extends BaseService
{
    use SortableTrait;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;
        $type = $data['filters']['type'] ?? null;

        $query = Expense::query()
            ->with(['expenseType'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($type, fn ($q, $value) => $q->where('type', $value));

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['name', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
```

`SortableTrait::applySorting($query, $sort, $direction, 'created_at', ['name', 'created_at'])` — ordena pelo
campo pedido SOMENTE se estiver na lista permitida (último argumento); senão ordena pelo campo default respeitando `direction` (os Services passam `desc` como padrão).
Nunca ordene por coluna vinda direto da query string.

## 6. Store / Update / View / Delete Services

```php
// Store — DB::transaction, injeta contexto (ex.: company_id), retorna model carregado
public function run(array $data): Expense
{
    return DB::transaction(function () use ($data): Expense {
        $data['company_id'] = $this->getCompany()->id;
        $data['paid_at'] = ($data['is_paid'] ?? false) ? now() : null;

        return Expense::create($data)->load('expenseType');
    });
}

// Update — DB::transaction, update + refresh + load
public function run(Expense $expense, array $data): Expense
{
    return DB::transaction(function () use ($expense, $data): Expense {
        $expense->update($data);

        return $expense->refresh()->load('expenseType');
    });
}

// View — apenas carrega relações
public function run(Expense $expense): Expense
{
    return $expense->load('expenseType');
}

// Delete — DB::transaction + soft delete
public function run(Expense $expense): void
{
    DB::transaction(fn () => $expense->delete());
}
```

## 7. FormRequests

```php
// StoreFooRequest
class StoreExpenseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_type_id' => 'required|integer|exists:expense_types,id',
            'is_paid' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da despesa é obrigatório.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'expense_type_id.exists' => 'O tipo de despesa selecionado não existe.',
        ];
    }
}

// IndexFooRequest — mescla paginationRules() + filtros
class IndexExpenseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => 'nullable|string|max:255',
            'filters.type' => 'sometimes|in:enter,exit',
            'filters.expense_type_ids' => 'sometimes|array',
            'filters.expense_type_ids.*' => 'integer|exists:expense_types,id',
        ]);
    }
}
```

Regra prática: `Update` geralmente repete as regras do `Store` trocando `required` por
`sometimes`/`nullable` nos campos que podem não vir.

## 8. Resource

```php
<?php

declare(strict_types=1);

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'due_date' => $this->due_date?->format('d/m/Y'),
            'is_paid' => $this->is_paid,
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),

            'expense_type' => $this->whenLoaded('expenseType', fn () => [
                'id' => $this->expenseType->id,
                'name' => $this->expenseType->name,
            ]),
        ];
    }
}
```

## 9. Rotas (routes/api.php)

```php
use App\Http\Controllers\Api\Company\ExpenseController;

Route::prefix('expenses')->group(function (): void {
    Route::get('/', [ExpenseController::class, 'index']);
    Route::get('{expense}', [ExpenseController::class, 'show']);
    Route::post('/', [ExpenseController::class, 'store']);
    Route::put('{expense}', [ExpenseController::class, 'update']);
    Route::delete('{expense}', [ExpenseController::class, 'destroy']);
});
```

O parâmetro `{expense}` usa route-model binding. Com `getRouteKeyName()` retornando
`uuid` no model, a rota resolve por uuid em vez de id.

## Checklist para um novo CRUD `Foo`

1. Criar os 3 FormRequests (Index/Store/Update) estendendo `BaseFormRequest`.
2. Criar os 5 Services (Index/View/Store/Update/Delete) estendendo `BaseService`,
   com escrita em `DB::transaction`.
3. Criar o `FooResource` formatando datas e usando `whenLoaded` para relações.
4. Criar o `FooController` estendendo `BaseController`, delegando por injeção.
5. Registrar o grupo de rotas em `routes/api.php`.
6. Garantir `declare(strict_types=1);` em controller/services/requests.
7. Validar com `./vendor/bin/pint` e testar os endpoints.
