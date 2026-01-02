# 📖 Sistema de Restaurante - Instruções de Instalação

## ⚙️ Requisitos
- WAMP Server instalado e rodando
- PHP 7.4 ou superior
- MySQL 5.7 ou superior

## 🚀 Instalação

### Passo 1: Iniciar o WAMP
Certifique-se de que o ícone do WAMP está **VERDE** na bandeja do sistema.

### Passo 2: Criar o Banco de Dados

1. Acesse: http://localhost/phpmyadmin
2. Clique em **"SQL"** no menu superior
3. Copie todo o conteúdo do arquivo `database/schema.sql`
4. Cole no campo SQL do phpMyAdmin
5. Clique em **"Executar"**

### Passo 3: Testar a Instalação

Acesse: http://localhost/Restaurante/test_connection.php

Este script verifica:
- ✅ Conexão com MySQL
- ✅ Banco de dados criado
- ✅ Tabelas criadas
- ✅ Dados iniciais inseridos

### Passo 4: Acessar o Sistema

**URL:** http://localhost/Restaurante/public/index.php

**Credenciais padrão:**
- Email: `admin@restaurante.com`
- Senha: `admin123`

## 👥 Perfis de Usuário

O sistema possui 4 perfis diferentes:

### 1. Administrador
- **Email:** admin@restaurante.com
- **Senha:** admin123
- **Acesso:** Dashboard com estatísticas, gerenciamento completo

### 2. Garçom
- **Tela:** Gerenciamento de mesas e pedidos
- **Funções:**
  - Visualizar mesas disponíveis/ocupadas
  - Criar novos pedidos
  - Adicionar itens ao pedido
  - Enviar pedido para cozinha

### 3. Cozinha
- **Tela:** Pedidos em preparo
- **Funções:**
  - Visualizar pedidos pendentes
  - Marcar itens como prontos
  - Finalizar pedidos para entrega

### 4. Caixa/PDV
- **Tela:** Fechamento de contas
- **Funções:**
  - Visualizar pedidos prontos
  - Aplicar descontos
  - Processar pagamentos (Dinheiro, PIX, Crédito, Débito)
  - Calcular troco
  - Finalizar conta

## 🗂️ Estrutura do Projeto

```
Restaurante/
├── api/                    # API REST
│   ├── controllers/        # Controllers da API
│   ├── models/            # Models (User, Mesa, Produto, etc)
│   └── index.php          # Rotas da API
├── config/
│   └── config.php         # Configurações do sistema
├── core/                  # Classes principais
│   ├── Auth.php           # Autenticação
│   ├── Database.php       # Conexão com banco
│   ├── Model.php          # Model base
│   ├── Controller.php     # Controller base
│   └── Router.php         # Sistema de rotas
├── database/
│   └── schema.sql         # Script SQL do banco
├── public/                # Arquivos públicos
│   ├── css/
│   │   └── style.css      # Estilos globais
│   ├── js/
│   │   └── app.js         # JavaScript global
│   ├── images/            # Imagens
│   ├── uploads/           # Upload de arquivos
│   └── index.php          # Página inicial
├── views/                 # Interfaces do sistema
│   ├── auth/
│   │   └── login.php      # Tela de login
│   ├── admin/
│   │   └── dashboard.php  # Dashboard admin
│   ├── garcom/
│   │   └── mesas.php      # Interface do garçom
│   ├── cozinha/
│   │   └── pedidos.php    # Interface da cozinha
│   └── pdv/
│       └── index.php      # Interface do PDV
├── test_connection.php    # Script de teste
└── INSTRUCOES.md         # Este arquivo
```

## 🔧 Configurações

### Banco de Dados
Edite o arquivo `config/config.php` se precisar alterar as configurações:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'Restaurante');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### URL Base
Se o projeto não estiver em `C:\wamp64\www\Restaurante`, ajuste:

```php
define('APP_URL', 'http://localhost/Restaurante');
```

## 📊 Dados Iniciais

O sistema já vem com:
- ✅ 4 perfis de usuário (roles)
- ✅ 1 usuário administrador
- ✅ 10 mesas cadastradas
- ✅ 6 categorias de produtos
- ✅ 18 produtos de exemplo
- ✅ Configurações básicas

## 🔐 Segurança

- Todas as senhas são criptografadas com `password_hash()` do PHP
- Sistema de sessões para autenticação
- Proteção contra SQL Injection usando PDO Prepared Statements
- Validação de dados no backend

## 🐛 Problemas Comuns

### Erro: "Access denied for user 'root'@'localhost'"
**Solução:** Verifique as credenciais em `config/config.php`

### Erro: "Unknown database 'Restaurante'"
**Solução:** Execute o `schema.sql` no phpMyAdmin

### Página em branco ou erro 500
**Solução:**
1. Verifique se o PHP está ativo no WAMP
2. Habilite exibição de erros em `config/config.php` (já está habilitado em development)
3. Verifique os logs de erro do Apache

### WAMP não inicia (ícone vermelho/laranja)
**Solução:**
1. Verifique se a porta 80 está livre (Skype pode usar)
2. Verifique se a porta 3306 do MySQL está livre
3. Reinicie o WAMP como administrador

## 📱 Funcionalidades

### ✅ Implementado
- Sistema de autenticação completo
- Gerenciamento de mesas
- Cardápio/catálogo de produtos
- Sistema de pedidos
- Interface da cozinha
- PDV para fechamento de contas
- Múltiplas formas de pagamento
- Cálculo automático de troco
- Sistema de descontos
- Atualização automática (real-time polling)
- Design responsivo

### 🔜 Melhorias Futuras (Sugestões)
- Dashboard com gráficos
- Relatórios avançados
- Impressão de comanda
- Notificações push (WebSocket)
- App mobile
- Sistema de reservas
- Controle de estoque
- Comissão de garçons

## 📞 Suporte

Para dúvidas ou problemas, verifique:
1. Este arquivo de instruções
2. O script `test_connection.php`
3. Os logs de erro do WAMP

## 📄 Licença

Sistema desenvolvido para fins educacionais e comerciais.

---

**Desenvolvido com ❤️ por Claude Code**
**Versão: 1.0.0**
**Data: 2024**
