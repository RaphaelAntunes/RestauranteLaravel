# Início Rápido - Sistema Restaurante Laravel

## Status da Instalação

✅ Projeto Laravel criado com sucesso
✅ Todas as migrations executadas
✅ Todos os seeders executados
✅ Banco de dados populado com dados de exemplo

## Credenciais de Acesso

**Usuário Administrador:**
- Email: `admin@restaurante.com`
- Senha: `admin123`

## Estrutura do Projeto

```
RestauranteLaravel/
├── app/
│   ├── Models/           # 11 Models Eloquent
│   ├── Services/         # PedidoService e PagamentoService
│   └── Traits/           # LogsActivity
├── database/
│   ├── migrations/       # 11 migrations
│   └── seeders/          # 6 seeders
├── README_LARAVEL.md     # Documentação completa
└── REFATORACAO_RESUMO.md # Resumo da refatoração
```

## Dados Populados no Banco

### Roles (Papéis)
- **admin** - Administrador do Sistema
- **garcom** - Garçom
- **cozinha** - Cozinha
- **caixa** - Caixa/PDV

### Usuários
- 1 usuário admin (admin@restaurante.com)

### Mesas
- 10 mesas configuradas
- Localizações: Salão Principal, Varanda, Área Externa, Salão VIP
- Capacidades: 2 a 8 pessoas

### Categorias
1. Entradas
2. Pratos Principais
3. Massas
4. Pizzas
5. Bebidas
6. Sobremesas

### Produtos
- 19 produtos de exemplo distribuídos nas categorias
- Preços de R$ 4,00 a R$ 85,00
- Tempos de preparo de 1 a 35 minutos

### Configurações
- Nome do restaurante
- Taxa de serviço (10%)
- Tempo de atualização da cozinha
- Permissão para desconto

## Comandos Úteis

### Iniciar o servidor de desenvolvimento

```bash
cd C:\Users\antunessx\Desktop\RestauranteLaravel
php artisan serve
```

Acesse: http://localhost:8000

### Ver todas as rotas

```bash
php artisan route:list
```

### Interagir com o banco via Tinker

```bash
php artisan tinker
```

Exemplos no Tinker:

```php
// Ver todos os produtos
App\Models\Produto::all();

// Ver produtos em destaque
App\Models\Produto::destaques()->get();

// Ver mesas disponíveis
App\Models\Mesa::disponiveis()->get();

// Obter configuração
App\Models\Configuracao::obter('nome_restaurante');

// Criar um pedido (exemplo)
$service = new App\Services\PedidoService();
$pedido = $service->criarPedido(mesaId: 1, userId: 1);
```

### Resetar e popular banco novamente

```bash
php artisan migrate:fresh --seed
```

### Limpar cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Testando o Sistema

### 1. Criar um Pedido

```php
// No Tinker (php artisan tinker)
$service = new App\Services\PedidoService();

// Criar pedido
$pedido = $service->criarPedido(
    mesaId: 1,
    userId: 1,
    observacoes: 'Sem cebola'
);

// Adicionar itens
$service->adicionarItem(
    pedidoId: $pedido->id,
    produtoId: 1,
    quantidade: 2
);

$service->adicionarItem(
    pedidoId: $pedido->id,
    produtoId: 5,
    quantidade: 1
);

// Ver total
$pedido->fresh()->total_formatado;

// Enviar para cozinha
$service->enviarParaCozinha($pedido->id);
```

### 2. Processar Pagamento

```php
// No Tinker
$pagamentoService = new App\Services\PagamentoService();

// Ver resumo antes de pagar
$resumo = $pagamentoService->calcularResumo($pedido->id, desconto: 5.00);

// Processar pagamento
$pagamento = $pagamentoService->processarPagamentoSimples(
    pedidoId: $pedido->id,
    userId: 1,
    metodoPagamento: 'dinheiro',
    valorPago: 100.00,
    desconto: 5.00
);

echo $pagamento->troco_formatado;
```

### 3. Consultas Úteis

```php
// Produtos de uma categoria
$categoria = App\Models\Categoria::find(1);
$categoria->produtosAtivos;

// Pedidos de uma mesa
$mesa = App\Models\Mesa::find(1);
$mesa->pedidos;

// Pedidos abertos
App\Models\Pedido::abertos()->with('mesa', 'itens.produto')->get();

// Histórico de um usuário
$user = App\Models\User::find(1);
$user->pedidos;
$user->logs;
```

## Próximos Passos

### Controllers (Recomendado)

Crie controllers para expor as funcionalidades via API:

```bash
php artisan make:controller Api/PedidoController
php artisan make:controller Api/ProdutoController
php artisan make:controller Api/MesaController
php artisan make:controller Api/PagamentoController
```

### Resources (Serialização)

```bash
php artisan make:resource PedidoResource
php artisan make:resource ProdutoResource
```

### Form Requests (Validação)

```bash
php artisan make:request StorePedidoRequest
php artisan make:request StorePagamentoRequest
```

### Testes

```bash
php artisan make:test PedidoServiceTest --unit
php artisan make:test PedidoApiTest
```

### Autenticação com Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## Estrutura de API Sugerida

```
POST   /api/pedidos                # Criar pedido
GET    /api/pedidos                # Listar pedidos
GET    /api/pedidos/{id}           # Ver pedido
POST   /api/pedidos/{id}/itens     # Adicionar item
DELETE /api/pedidos/{id}/itens/{itemId}  # Remover item
POST   /api/pedidos/{id}/enviar-cozinha  # Enviar para cozinha
POST   /api/pedidos/{id}/cancelar  # Cancelar pedido

GET    /api/mesas                  # Listar mesas
GET    /api/mesas/disponiveis      # Mesas disponíveis

GET    /api/produtos               # Listar produtos
GET    /api/categorias             # Listar categorias com produtos

POST   /api/pagamentos             # Processar pagamento
GET    /api/pagamentos/{id}/resumo # Ver resumo do pagamento
```

## Documentação Completa

Para documentação detalhada sobre models, relacionamentos, services e exemplos de uso, consulte:

- **README_LARAVEL.md** - Documentação completa do projeto
- **REFATORACAO_RESUMO.md** - Resumo da refatoração do SQL para Laravel

## Suporte

O projeto está 100% funcional com:
- ✅ 11 Migrations criadas e executadas
- ✅ 11 Models com relacionamentos
- ✅ 2 Services para lógica de negócio
- ✅ 6 Seeders com dados de exemplo
- ✅ Trait para logging automático
- ✅ Sistema de configurações com cache

## Troubleshooting

### Erro de conexão com banco

Verifique o arquivo `.env` e confirme:
- `DB_CONNECTION=mysql`
- `DB_DATABASE=Restaurante`
- `DB_USERNAME=root`
- `DB_PASSWORD=` (sua senha)

### Resetar banco de dados

```bash
php artisan migrate:fresh --seed
```

### Erro de key length

Já corrigido no `AppServiceProvider.php` com:
```php
Schema::defaultStringLength(191);
```

## Licença

Sistema desenvolvido para gerenciamento de restaurantes.
