#!/bin/sh
set -e

echo "🚀 Iniciando aplicação Laravel..."

# Aguardar MySQL estar pronto
echo "⏳ Aguardando MySQL..."
while ! mysqladmin ping -h"mysql" --silent 2>/dev/null; do
    sleep 1
done
echo "✅ MySQL está pronto!"

# Criar link simbólico do storage se não existir
if [ ! -L "/var/www/html/public/storage" ]; then
    php artisan storage:link 2>/dev/null || true
fi

# Verificar se o arquivo .env existe
if [ ! -f "/var/www/html/.env" ]; then
    echo "⚠️  Arquivo .env não encontrado. Copiando .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Gerar chave da aplicação se necessário
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Gerando chave da aplicação..."
    php artisan key:generate --force
fi

# Limpar e cachear configurações para produção
echo "📦 Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Executar migrations
echo "🗄️  Executando migrations..."
php artisan migrate --force

# Executar seeders se banco estiver vazio (opcional)
# php artisan db:seed --force

echo "✅ Aplicação pronta!"

# Executar comando passado
exec "$@"
