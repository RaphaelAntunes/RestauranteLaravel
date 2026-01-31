#!/bin/sh
set -e

echo "🚀 Iniciando aplicação Laravel..."

# Aguardar MySQL estar pronto (por porta)
echo "⏳ Aguardando MySQL..."
while ! nc -z mysql 3306; do
    sleep 1
done
echo "✅ MySQL está pronto!"

# Criar link simbólico do storage se não existir
php artisan storage:link 2>/dev/null || true

# Limpar e cachear configurações para produção
echo "📦 Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Executar migrations
echo "🗄️  Executando migrations..."
php artisan migrate --force

echo "✅ Aplicação pronta!"

# Executar comando passado
exec "$@"
