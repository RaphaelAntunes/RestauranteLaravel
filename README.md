# Sistema de Gerenciamento de Restaurante

Sistema completo para gerenciamento de restaurante desenvolvido com Laravel 12, PHP 8.2+, MySQL e Tailwind CSS.

## 🚀 Funcionalidades

### Autenticação e Controle de Acesso
- Sistema completo de login e logout
- Controle de permissões por papel (roles):
  - **Admin**: Acesso total ao sistema
  - **Garçom**: Gerencia pedidos e mesas
  - **Cozinha**: Visualiza e prepara pedidos
  - **Caixa**: Realiza fechamentos e pagamentos
- Proteção de rotas por middleware
- Registro de último acesso

### Cardápio Digital
- CRUD completo de categorias
- CRUD completo de produtos
- Upload de imagens para produtos
- Controle de status (ativo/inativo)
- Produtos em destaque
- Tempo de preparo estimado

### Gestão de Mesas
- CRUD completo de mesas
- Status das mesas (disponível, ocupada, reservada, manutenção)
- Localização e capacidade
- Visualização de pedidos ativos por mesa

### Sistema de Pedidos
- Criação de pedidos por mesa
- Adição e remoção de itens
- Observações por item e por pedido
- Geração automática de número do pedido
- Status do pedido (aberto, em preparo, pronto, entregue, finalizado, cancelado)
- Cálculo automático de totais
- Vinculação automática de pedido à mesa

### Painel da Cozinha (KDS - Kitchen Display System)
- Visualização em tempo real de pedidos
- Atualização automática a cada 5 segundos (AJAX polling)
- Organização visual por status:
  - Aguardando (novos pedidos)
  - Em Preparo
  - Prontos para entrega
- Destacamento de pedidos novos
- Botões para gerenciar status dos pedidos
- Indicador visual de conexão
- Design otimizado para legibilidade

### PDV (Ponto de Venda)
- Lista de mesas com pedidos em aberto
- Fechamento de conta por mesa
- Formas de pagamento:
  - Dinheiro
  - PIX
  - Cartão de Crédito
  - Cartão de Débito
- Sistema de desconto percentual
- Cálculo automático de troco
- Geração de comprovante
- Histórico de pagamentos

### Relatórios Gerenciais
- **Vendas por Período**:
  - Faturamento total
  - Número de pedidos
  - Ticket médio
  - Vendas por dia
  - Vendas por forma de pagamento

- **Produtos Mais Vendidos**:
  - Ranking por quantidade
  - Faturamento por produto
  - Filtro por período

- **Faturamento Mensal**:
  - Visualização anual
  - Total por mês
  - Ticket médio mensal

- **Desempenho de Garçons**:
  - Total de pedidos por garçom
  - Faturamento gerado

## 📋 Requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- MySQL 5.7+ ou MariaDB 10.3+
- Extensões PHP: PDO, mbstring, openssl, tokenizer, xml, ctype, json

## 🔧 Instalação

### 1. Clone ou baixe o projeto

```bash
cd C:\Users\antunessx\Desktop\RestauranteLaravel
```

### 2. Instale as dependências do Composer

```bash
composer install
```

### 3. Instale as dependências do NPM

```bash
npm install
```

### 4. Configure o arquivo .env

O arquivo `.env` já está configurado. Verifique as seguintes configurações:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Restaurante
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 5. Crie o banco de dados

Certifique-se de que o banco de dados `Restaurante` existe no MySQL:

```sql
CREATE DATABASE Restaurante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Execute as migrations e seeders

```bash
php artisan migrate:fresh --seed
```

Este comando irá:
- Criar todas as tabelas necessárias
- Popular o banco com dados de exemplo:
  - 4 roles (admin, garcom, cozinha, caixa)
  - 1 usuário administrador
  - 10 mesas
  - 6 categorias
  - 19 produtos de exemplo

### 7. Crie o link simbólico para storage (imagens)

```bash
php artisan storage:link
```

### 8. Compile os assets

```bash
npm run build
```

Para desenvolvimento com hot-reload:

```bash
npm run dev
```

### 9. Inicie o servidor

```bash
php artisan serve
```

O sistema estará disponível em: `http://localhost:8000`

## 🔐 Credenciais Padrão

**Usuário Administrador:**
- **Email**: admin@restaurante.com
- **Senha**: admin123

## 📚 Estrutura do Banco de Dados

### Tabelas Principais

- **roles**: Papéis de usuário
- **users**: Usuários do sistema
- **mesas**: Mesas do restaurante
- **categorias**: Categorias de produtos
- **produtos**: Produtos/itens do cardápio
- **pedidos**: Pedidos realizados
- **pedido_itens**: Itens de cada pedido
- **pagamentos**: Pagamentos realizados
- **pagamento_detalhes**: Detalhes dos pagamentos (vínculo com pedidos)
- **logs**: Logs de atividades
- **configuracoes**: Configurações do sistema
- **sessions**: Sessões de usuário
- **cache**: Cache do sistema
- **jobs**: Fila de trabalhos

## 🎯 Como Usar

### Login
1. Acesse `http://localhost:8000`
2. Digite o email e senha do usuário
3. Clique em "Entrar"

### Criar um Pedido
1. Acesse "Pedidos" > "Novo Pedido"
2. Selecione uma mesa disponível
3. Adicione produtos ao pedido
4. Adicione observações se necessário
5. Clique em "Criar Pedido"

### Painel da Cozinha
1. Acesse "Cozinha" no menu
2. Visualize os pedidos organizados por status
3. Clique em "Iniciar Preparo" para pedidos aguardando
4. Clique em "Marcar Pronto" quando o pedido estiver pronto
5. Clique em "Entregar" quando o pedido for entregue ao cliente
6. O painel atualiza automaticamente a cada 5 segundos

### Fechar Conta (PDV)
1. Acesse "PDV" no menu
2. Selecione a mesa para fechar
3. Confira os pedidos e o total
4. Selecione a forma de pagamento
5. Informe o valor pago
6. Adicione desconto se necessário
7. Clique em "Processar Pagamento"
8. Visualize e imprima o comprovante

### Relatórios (Apenas Admin)
1. Acesse "Relatórios" no menu
2. Selecione o tipo de relatório desejado
3. Defina o período (quando aplicável)
4. Visualize os dados e gráficos

## 🏗️ Arquitetura

### Backend
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Banco de Dados**: MySQL
- **Autenticação**: Laravel Auth customizado
- **Validação**: Form Requests e validação inline

### Frontend
- **Template Engine**: Blade
- **CSS Framework**: Tailwind CSS 4.x
- **Build Tool**: Vite
- **JavaScript**: Vanilla JS para funcionalidades dinâmicas
- **AJAX**: Fetch API para atualização em tempo real

### Padrões
- **Arquitetura**: MVC (Model-View-Controller)
- **Routes**: RESTful
- **Controllers**: Resource Controllers
- **Models**: Eloquent ORM com relacionamentos
- **Migrations**: Versionamento de banco de dados
- **Seeders**: Dados iniciais e de exemplo

## 📁 Estrutura de Diretórios

```
RestauranteLaravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── HomeController.php
│   │       ├── CategoriaController.php
│   │       ├── ProdutoController.php
│   │       ├── MesaController.php
│   │       ├── PedidoController.php
│   │       ├── PDVController.php
│   │       ├── CozinhaController.php
│   │       └── RelatorioController.php
│   └── Models/
│       ├── User.php
│       ├── Role.php
│       ├── Mesa.php
│       ├── Categoria.php
│       ├── Produto.php
│       ├── Pedido.php
│       ├── PedidoItem.php
│       ├── Pagamento.php
│       └── PagamentoDetalhe.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── categorias/
│   │   ├── produtos/
│   │   ├── mesas/
│   │   ├── pedidos/
│   │   ├── cozinha/
│   │   ├── pdv/
│   │   └── relatorios/
│   └── css/
│       └── app.css
├── routes/
│   └── web.php
└── public/
    └── storage/ (link simbólico)
```

## 🔒 Segurança

- Senhas criptografadas com bcrypt
- Proteção CSRF em todos os formulários
- Middleware de autenticação
- Autorização baseada em roles
- Validação de dados em todas as operações
- Proteção contra SQL Injection (Eloquent ORM)
- Sanitização de inputs

## 🚀 Próximos Passos / Melhorias Futuras

- Implementar WebSockets para atualização em tempo real (pusher/socket.io)
- Adicionar exportação de relatórios em PDF
- Implementar sistema de reservas de mesas
- Adicionar gestão de estoque
- Criar aplicativo mobile para garçons
- Implementar QR Code para pedidos pelos clientes
- Sistema de avaliação de pratos
- Integração com sistemas de pagamento online

## 📝 Notas Importantes

1. **Desenvolvimento Local**: Este sistema foi desenvolvido para rodar localmente. Para produção, configure adequadamente:
   - APP_ENV=production
   - APP_DEBUG=false
   - Configure HTTPS
   - Use servidor web adequado (Apache/Nginx)
   - Configure cache e otimizações

2. **Backup**: Faça backups regulares do banco de dados

3. **Credenciais**: Altere as credenciais padrão em produção

4. **Imagens**: As imagens dos produtos ficam em `storage/app/public/produtos/`

5. **Sessões**: As sessões são armazenadas no banco de dados

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs em `storage/logs/laravel.log`
2. Verifique se o servidor MySQL está rodando
3. Verifique se todas as dependências foram instaladas
4. Execute `php artisan config:cache` se houver problemas de configuração

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais e comerciais.

---

**Desenvolvido com Laravel 12, PHP 8.2+, MySQL e Tailwind CSS**

*Sistema Completo de Gerenciamento de Restaurante - 2025*
