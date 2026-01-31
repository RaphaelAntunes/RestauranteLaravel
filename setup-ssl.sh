#!/bin/bash
# ===========================================
# Script para configurar SSL auto-assinado
# ===========================================
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🔐 Configurando SSL auto-assinado...${NC}"

# Pegar IP do servidor
SERVER_IP=$(curl -s -4 ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')
echo -e "${YELLOW}IP detectado: ${SERVER_IP}${NC}"

# Criar pasta para certificados
mkdir -p docker/nginx/ssl

# Gerar certificado auto-assinado
echo -e "${YELLOW}📜 Gerando certificado SSL...${NC}"
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/privkey.pem \
  -out docker/nginx/ssl/fullchain.pem \
  -subj "/C=BR/ST=Estado/L=Cidade/O=SuperPizza/CN=${SERVER_IP}" \
  2>/dev/null

echo -e "${GREEN}✅ Certificado gerado!${NC}"

# Verificar se porta 40443 está livre
if lsof -i :40443 &> /dev/null; then
    echo -e "${RED}⚠️  Porta 40443 já está em uso!${NC}"
else
    echo -e "${GREEN}✅ Porta 40443 disponível${NC}"
fi

# Reiniciar containers
echo -e "${YELLOW}🔄 Reiniciando containers...${NC}"
docker compose down
docker compose up -d

# Aguardar containers iniciarem
sleep 5

# Verificar status
echo ""
echo -e "${GREEN}=============================================${NC}"
echo -e "${GREEN}✅ SSL configurado com sucesso!${NC}"
echo -e "${GREEN}=============================================${NC}"
echo ""
docker compose ps
echo ""
echo -e "🔒 Acesse via HTTPS: ${GREEN}https://${SERVER_IP}:40443${NC}"
echo ""
echo -e "${YELLOW}NOTA: O navegador vai mostrar aviso de segurança.${NC}"
echo -e "${YELLOW}Clique em 'Avançado' → 'Continuar mesmo assim'${NC}"
echo ""
