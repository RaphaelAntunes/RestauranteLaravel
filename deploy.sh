#!/bin/bash
# ===========================================
# Script de Deploy - Super Pizza
# Portas: 40123 (web) | 40306 (mysql) | 40379 (redis)
# ===========================================
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🚀 Deploy Super Pizza - Restaurante${NC}"
echo -e "${YELLOW}Portas: 40123 (web) | 40306 (mysql) | 40379 (redis)${NC}"
echo ""

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker não encontrado!${NC}"
    echo "Instale com: curl -fsSL https://get.docker.com | sh"
    exit 1
fi

# Verificar Docker Compose
if ! docker compose version &> /dev/null; then
    echo -e "${RED}❌ Docker Compose não encontrado!${NC}"
    echo "Instale com: sudo apt install docker-compose-plugin"
    exit 1
fi

# Verificar se portas estão livres
echo -e "${YELLOW}🔍 Verificando portas...${NC}"
for port in 40123 40306 40379; do
    if lsof -i :$port &> /dev/null; then
        echo -e "${RED}❌ Porta $port já está em uso!${NC}"
        lsof -i :$port
        exit 1
    fi
done
echo -e "${GREEN}✅ Portas disponíveis${NC}"

# Criar .env se não existir
if [ ! -f .env ]; then
    echo -e "${YELLOW}📝 Criando arquivo .env...${NC}"
    cp .env.docker .env

    # Gerar APP_KEY
    APP_KEY=$(openssl rand -base64 32)
    sed -i "s|APP_KEY=|APP_KEY=base64:$APP_KEY|g" .env

    echo -e "${RED}❗ IMPORTANTE: Edite o .env com suas senhas!${NC}"
    echo -e "${YELLOW}   nano .env${NC}"
    echo ""
    echo -e "Depois execute novamente: ${GREEN}./deploy.sh${NC}"
    exit 0
fi

# Criar diretórios
echo -e "${YELLOW}📁 Criando diretórios...${NC}"
mkdir -p storage/{logs,app/public,framework/{cache,sessions,views}} bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Parar containers antigos deste projeto (se existirem)
echo -e "${YELLOW}🛑 Parando containers antigos...${NC}"
docker compose down 2>/dev/null || true

# Build
echo -e "${YELLOW}🔨 Construindo imagem...${NC}"
docker compose build

# Subir containers
echo -e "${YELLOW}🚀 Iniciando containers...${NC}"
docker compose up -d

# Aguardar MySQL
echo -e "${YELLOW}⏳ Aguardando MySQL iniciar...${NC}"
sleep 15

# Status
echo ""
echo -e "${GREEN}=============================================${NC}"
echo -e "${GREEN}✅ Deploy concluído!${NC}"
echo -e "${GREEN}=============================================${NC}"
echo ""
docker compose ps
echo ""
echo -e "🌐 Acesso: ${GREEN}http://$(hostname -I | awk '{print $1}'):40123${NC}"
echo ""
echo -e "${YELLOW}Comandos úteis:${NC}"
echo "  docker compose logs -f          # Logs em tempo real"
echo "  docker compose exec app sh      # Acessar container"
echo "  docker compose restart          # Reiniciar"
echo "  docker compose down             # Parar"
echo ""
