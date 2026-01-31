-- Script de inicialização do MySQL
-- Este arquivo é executado automaticamente quando o container é criado pela primeira vez

-- Configurar charset e collation padrão
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Criar banco de dados se não existir (já criado via variável de ambiente, mas por segurança)
CREATE DATABASE IF NOT EXISTS `restaurante`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Garantir permissões completas ao usuário
GRANT ALL PRIVILEGES ON `restaurante`.* TO 'restaurante'@'%';
FLUSH PRIVILEGES;

-- Mensagem de confirmação
SELECT 'Banco de dados inicializado com sucesso!' AS status;
