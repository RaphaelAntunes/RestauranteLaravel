# Sistema de Restaurante - Laravel

Sistema completo de gerenciamento de restaurante refatorado para Laravel com migrations, models Eloquent e seeders.

## Estrutura do Banco de Dados

### Tabelas Principais

- **roles**: Papéis e permissões (admin, garçom, cozinha, caixa)
- **users**: Usuários do sistema
- **mesas**: Mesas do restaurante
- **categorias**: Categorias do cardápio
- **produtos**: Itens do cardápio
- **pedidos**: Pedidos realizados
- **pedido_itens**: Itens de cada pedido
- **pagamentos**: Pagamentos/fechamentos
- **pagamento_detalhes**: Detalhes de pagamentos múltiplos
- **logs**: Histórico de ações do sistema
- **configuracoes**: Configurações do sistema

## Instalação

### 1. Configurar o arquivo .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Restaurante
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 2. Executar as migrations

```bash
php artisan migrate
```

### 3. Popular o banco de dados

```bash
php artisan db:seed
```

Ou executar tudo de uma vez (reset + migrate + seed):

```bash
php artisan migrate:fresh --seed
```

## Credenciais Padrão

- **Email**: admin@restaurante.com
- **Senha**: admin123

## Models e Relacionamentos

### Role (Papel/Permissão)

```php
// Relacionamentos
$role->users; // Usuários com este papel

// Papéis disponíveis
- admin: Administrador do Sistema
- garcom: Garçom
- cozinha: Cozinha
- caixa: Caixa/PDV
```

### User (Usuário)

```php
// Relacionamentos
$user->role; // Papel do usuário
$user->pedidos; // Pedidos criados
$user->pagamentos; // Pagamentos realizados
$user->logs; // Logs de ações

// Métodos auxiliares
$user->isAdmin();
$user->isGarcom();
$user->isCozinha();
$user->isCaixa();
```

### Mesa

```php
// Relacionamentos
$mesa->pedidos; // Todos os pedidos
$mesa->pedidoAtivo(); // Pedido atual (aberto/em preparo)

// Métodos auxiliares
$mesa->isDisponivel();
$mesa->isOcupada();

// Scopes
Mesa::ativas()->get();
Mesa::disponiveis()->get();
```

### Categoria

```php
// Relacionamentos
$categoria->produtos; // Todos os produtos
$categoria->produtosAtivos(); // Apenas produtos ativos

// Scopes
Categoria::ativas()->get();
Categoria::ordenadas()->get();
```

### Produto

```php
// Relacionamentos
$produto->categoria;
$produto->pedidoItens;

// Attributes
$produto->preco_formatado; // "R$ 25,00"

// Scopes
Produto::ativos()->get();
Produto::destaques()->get();
```

### Pedido

```php
// Relacionamentos
$pedido->mesa;
$pedido->user; // Garçom que criou
$pedido->itens; // Itens do pedido
$pedido->pagamento;

// Métodos
$pedido->calcularTotal(); // Recalcula total
$pedido->isAberto();
$pedido->isFinalizado();
$pedido->isCancelado();

// Attributes
$pedido->total_formatado; // "R$ 150,00"

// Scopes
Pedido::abertos()->get();
Pedido::emPreparo()->get();
Pedido::prontos()->get();
Pedido::finalizados()->get();
```

### PedidoItem

```php
// Relacionamentos
$item->pedido;
$item->produto;

// Observações
- O subtotal é calculado automaticamente ao salvar
- Atualiza o total do pedido automaticamente
- Ao deletar, recalcula o total do pedido

// Attributes
$item->subtotal_formatado; // "R$ 50,00"

// Scopes
PedidoItem::pendentes()->get();
PedidoItem::emPreparo()->get();
```

### Pagamento

```php
// Relacionamentos
$pagamento->pedido;
$pagamento->user; // Operador do caixa
$pagamento->detalhes; // Para pagamentos múltiplos

// Métodos
$pagamento->isMetodoMultiplo();

// Attributes
$pagamento->valor_total_formatado; // "R$ 150,00"
$pagamento->troco_formatado; // "R$ 10,00"
```

### Log

```php
// Relacionamentos
$log->user;

// Método estático para registrar ações
Log::registrar(
    acao: 'criar_pedido',
    tabela: 'pedidos',
    registroId: $pedido->id,
    dadosAnteriores: null,
    dadosNovos: $pedido->toArray()
);
```

### Configuracao

```php
// Obter configuração
$nomeRestaurante = Configuracao::obter('nome_restaurante', 'Padrão');
$taxaServico = Configuracao::obter('taxa_servico', 10);
$permitirDesconto = Configuracao::obter('permitir_desconto', false);

// Definir configuração
Configuracao::definir('taxa_servico', 10, 'number');
Configuracao::definir('permitir_desconto', true, 'boolean');
Configuracao::definir('nome_restaurante', 'Meu Restaurante', 'string');

// Nota: Configurações são armazenadas em cache por 1 hora
```

## Exemplos de Uso

### Criar um novo pedido

```php
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Support\Str;

// Criar pedido
$pedido = Pedido::create([
    'mesa_id' => 1,
    'user_id' => auth()->id(),
    'numero_pedido' => 'PED-' . Str::random(8),
    'status' => 'aberto',
]);

// Adicionar itens
$produto = Produto::find(1);
PedidoItem::create([
    'pedido_id' => $pedido->id,
    'produto_id' => $produto->id,
    'quantidade' => 2,
    'preco_unitario' => $produto->preco,
]);

// Total é calculado automaticamente
echo $pedido->fresh()->total_formatado;
```

### Listar produtos por categoria

```php
use App\Models\Categoria;

$categorias = Categoria::ativas()
    ->ordenadas()
    ->with('produtosAtivos')
    ->get();

foreach ($categorias as $categoria) {
    echo $categoria->nome;
    foreach ($categoria->produtosAtivos as $produto) {
        echo "- {$produto->nome}: {$produto->preco_formatado}";
    }
}
```

### Processar pagamento

```php
use App\Models\Pagamento;
use App\Models\PagamentoDetalhe;

$pedido = Pedido::find(1);

// Pagamento simples
$pagamento = Pagamento::create([
    'pedido_id' => $pedido->id,
    'user_id' => auth()->id(),
    'valor_total' => $pedido->total,
    'metodo_pagamento' => 'dinheiro',
    'valor_pago' => 100.00,
    'troco' => 100.00 - $pedido->total,
]);

// Pagamento múltiplo
$pagamento = Pagamento::create([
    'pedido_id' => $pedido->id,
    'user_id' => auth()->id(),
    'valor_total' => $pedido->total,
    'metodo_pagamento' => 'multiplo',
    'valor_pago' => $pedido->total,
]);

PagamentoDetalhe::create([
    'pagamento_id' => $pagamento->id,
    'metodo' => 'dinheiro',
    'valor' => 50.00,
]);

PagamentoDetalhe::create([
    'pagamento_id' => $pagamento->id,
    'metodo' => 'pix',
    'valor' => $pedido->total - 50.00,
]);

// Finalizar pedido
$pedido->update([
    'status' => 'finalizado',
    'data_finalizacao' => now(),
]);
```

### Verificar mesas disponíveis

```php
use App\Models\Mesa;

$mesasDisponiveis = Mesa::ativas()
    ->disponiveis()
    ->get();

foreach ($mesasDisponiveis as $mesa) {
    echo "Mesa {$mesa->numero} - Capacidade: {$mesa->capacidade} pessoas";
}
```

### Registrar ações no log

```php
use App\Models\Log;

// Ao criar um pedido
Log::registrar(
    acao: 'criar_pedido',
    tabela: 'pedidos',
    registroId: $pedido->id,
    dadosNovos: $pedido->toArray()
);

// Ao atualizar um pedido
Log::registrar(
    acao: 'atualizar_pedido',
    tabela: 'pedidos',
    registroId: $pedido->id,
    dadosAnteriores: $pedido->getOriginal(),
    dadosNovos: $pedido->getChanges()
);
```

## Características Especiais

### Cálculo Automático de Totais

O model `PedidoItem` possui eventos que calculam automaticamente:
- O subtotal ao salvar um item
- O total do pedido quando itens são adicionados/atualizados/removidos

### Cache de Configurações

O model `Configuracao` armazena automaticamente as configurações em cache por 1 hora para melhor performance.

### Autenticação Customizada

O model `User` usa o campo `senha` ao invés de `password` para autenticação, mantendo compatibilidade com o schema original.

### Formatação de Valores

Vários models incluem attributes para formatação de valores monetários em formato brasileiro (R$ 0,00).

## Status Disponíveis

### Mesa
- disponivel
- ocupada
- reservada
- manutencao

### Pedido
- aberto
- em_preparo
- pronto
- entregue
- finalizado
- cancelado

### PedidoItem
- pendente
- em_preparo
- pronto
- entregue
- cancelado

### Método de Pagamento
- dinheiro
- pix
- credito
- debito
- multiplo

## Comandos Úteis

```bash
# Criar uma nova migration
php artisan make:migration nome_da_migration

# Criar um novo model com migration
php artisan make:model NomeModel -m

# Criar um novo seeder
php artisan make:seeder NomeSeeder

# Resetar e recriar banco de dados
php artisan migrate:fresh --seed

# Ver status das migrations
php artisan migrate:status

# Rollback da última migration
php artisan migrate:rollback

# Limpar cache de configurações
php artisan cache:clear
```

## Próximos Passos

1. Criar Controllers para cada entidade
2. Criar Form Requests para validação
3. Criar Resources para APIs
4. Implementar autenticação com Sanctum/Passport
5. Criar testes unitários e de integração
6. Implementar sistema de filas para pedidos
7. Criar dashboard em tempo real com Broadcasting
