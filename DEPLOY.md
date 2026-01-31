# Deploy com Docker no Ubuntu

## Requisitos do Servidor

- Ubuntu 20.04+ (LTS recomendado)
- Mínimo 2GB RAM
- 20GB de disco

## Passo a Passo

### 1. Preparar o Servidor

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Git
sudo apt install -y git
```

### 2. Clonar o Projeto

```bash
cd /var/www
sudo git clone <seu-repositorio> restaurante
cd restaurante
sudo chown -R $USER:$USER .
```

### 3. Configurar o .env

```bash
# Copiar arquivo de exemplo
cp .env.docker .env

# Editar configurações
nano .env
```

**Configurações importantes para alterar:**

```env
APP_URL=http://seu-dominio.com.br
DB_PASSWORD=SuaSenhaSegura123!
DB_ROOT_PASSWORD=SuaSenhaRootSegura123!
```

### 4. Executar Deploy

```bash
# Dar permissão de execução
chmod +x deploy.sh

# Executar
./deploy.sh
```

## Comandos Úteis

```bash
# Ver status dos containers
docker compose ps

# Ver logs em tempo real
docker compose logs -f

# Ver logs de um serviço específico
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f mysql

# Reiniciar todos os containers
docker compose restart

# Reiniciar um container específico
docker compose restart app

# Parar todos os containers
docker compose down

# Acessar container da aplicação
docker compose exec app sh

# Executar comandos Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan cache:clear

# Acessar MySQL
docker compose exec mysql mysql -u restaurante -p
```

## Configurar SSL (HTTPS)

### Opção 1: Certbot (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install -y certbot

# Parar nginx temporariamente
docker compose stop nginx

# Gerar certificado
sudo certbot certonly --standalone -d seu-dominio.com.br

# Copiar certificados
sudo cp /etc/letsencrypt/live/seu-dominio.com.br/fullchain.pem docker/nginx/ssl/
sudo cp /etc/letsencrypt/live/seu-dominio.com.br/privkey.pem docker/nginx/ssl/

# Editar nginx config para habilitar HTTPS
nano docker/nginx/default.conf

# Reiniciar nginx
docker compose restart nginx
```

### Opção 2: Cloudflare (Recomendado)

1. Adicione seu domínio no Cloudflare
2. Aponte o DNS para o IP do servidor
3. Ative o proxy (nuvem laranja)
4. Configure SSL como "Flexible" ou "Full"

## Backup do Banco de Dados

```bash
# Criar backup
docker compose exec mysql mysqldump -u root -p restaurante > backup_$(date +%Y%m%d).sql

# Restaurar backup
docker compose exec -T mysql mysql -u root -p restaurante < backup.sql
```

## Atualizar Aplicação

```bash
# Baixar atualizações
git pull origin main

# Rebuild e restart
docker compose build --no-cache app
docker compose up -d

# Executar migrations
docker compose exec app php artisan migrate --force
```

## Monitoramento

```bash
# Uso de recursos dos containers
docker stats

# Verificar espaço em disco
df -h

# Limpar imagens Docker não utilizadas
docker system prune -a
```

## Estrutura dos Containers

| Container | Porta | Descrição |
|-----------|-------|-----------|
| nginx | 80, 443 | Web Server |
| app | 9000 | PHP-FPM (Laravel) |
| mysql | 3306 | Banco de dados |
| redis | 6379 | Cache/Sessions |
| queue | - | Worker de filas |
| scheduler | - | Cron jobs |

## Troubleshooting

### Container não inicia

```bash
# Ver logs detalhados
docker compose logs app

# Verificar se portas estão em uso
sudo lsof -i :80
sudo lsof -i :3306
```

### Erro de permissão

```bash
# Corrigir permissões
sudo chown -R 1000:1000 storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### MySQL não conecta

```bash
# Verificar se MySQL está healthy
docker compose ps

# Ver logs do MySQL
docker compose logs mysql

# Testar conexão
docker compose exec app php artisan db:monitor
```

### Limpar cache

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```
