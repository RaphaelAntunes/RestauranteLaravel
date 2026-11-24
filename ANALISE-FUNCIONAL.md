# Análise funcional do RestauranteLaravel

## Estado do projeto

O repositório já continha uma aplicação Laravel para operação presencial e pedidos online. A base inclui cardápio público, carrinho, login de cliente por código OTP, endereços salvos, checkout delivery/retirada, acompanhamento de pedido, painel administrativo de pedidos online, fila da cozinha, PDV, mesas, produtos, tamanhos, sabores, usuários/garçons, permissões, pagamentos e relatórios.

As melhorias desta rodada completam lacunas do fluxo do cliente (perfil e endereços), endurecem validações/isolamento de dados e atualizam a apresentação do cardápio e área do cliente. O inventário abaixo foi levantado a partir dos modelos, migrations, controllers e rotas presentes no código.

## Recursos e atributos existentes

### Empresa e operação

| Área | Campos e recursos encontrados |
|---|---|
| Funcionários | Nome, e-mail, senha protegida, função, ativo/inativo, último acesso, token de sessão persistente. |
| Funções | Nome e descrição. Funções usadas pelo sistema: admin, caixa, cozinha e garçom. |
| Permissões do garçom | Lançar pedidos, fechar mesas, cancelar itens, cancelar pedidos; administração de usuários e status. |
| Autenticação facial | Vetor facial e opção de exigir autenticação facial. |
| Categorias | Nome, tipo (comida/bebida), descrição, ordem e ativo/inativo. |
| Produtos | Categoria, nome, descrição, preço, imagem, tempo de preparo, ativo/inativo, destaque e ordem. |
| Tamanhos | Produto, nome, descrição, preço, máximo de sabores, ativo/inativo e ordem. |
| Sabores | Categoria, nome, descrição, ingredientes, preços P/M/G/GG, imagem, ativo/inativo e ordem. |
| Mesas | Número, tipo, capacidade, status, localização, cliente atual, sessão e ativo/inativo. Pedidos online recebem mesa virtual para aparecer na operação existente. |
| Pedidos | Número, mesa/sessão, funcionário, cliente, tipo mesa/delivery/retirada, endereço, status, subtotal, taxa, observações, datas de abertura/finalização e previsão. |
| Itens do pedido | Produto e nome preservado, tamanho, quantidade, preço unitário, subtotal, observações e status de preparo; sabores selecionados. |
| Pagamentos | Pedido, funcionário, mesa, total/subtotal, método/forma, valor pago, troco, desconto, acréscimo, taxa de serviço, status, observações e data; suporta divisão em detalhes/métodos múltiplos. |
| Configuração de delivery | Ativo, taxa fixa ou grátis acima de um valor, taxa, pedido mínimo, tempo médio, horário de abertura/fechamento e dias da semana. |
| Relatórios e auditoria | Vendas, produtos mais vendidos, faturamento mensal, desempenho de garçons, logs de ação com tabela/registro, valores anteriores/novos, IP e agente do navegador. |

### Consumidor

| Área | Campos e recursos encontrados |
|---|---|
| Conta | Nome, celular único usado no login, e-mail, CPF, ativo/inativo e último acesso. Login por OTP de seis dígitos. |
| Endereços | Apelido, CEP, logradouro, número, complemento, bairro, cidade, estado, referência e endereço padrão. O primeiro endereço agora vira padrão automaticamente. |
| Compra | Cardápio, detalhe do produto, tamanhos/sabores, carrinho, observações, delivery ou retirada, endereço, subtotal/taxa, pedido mínimo e acompanhamento de status. |
| Histórico | Lista dos próprios pedidos e acompanhamento; a autorização limita acesso aos pedidos e endereços do titular. |

## Melhorias aplicadas

- Criada área de perfil para o cliente atualizar nome, e-mail e CPF; o celular de login não pode ser alterado por esse formulário.
- Adicionada navegação para perfil e endereços nas telas do cliente.
- Normalizado o celular antes de salvar/login e restringido a formatos com 10 a 15 dígitos.
- Preservado o carrinho de visitante ao associá-lo à conta depois do login por OTP.
- Aplicado limite de tentativas ao envio e validação de OTP; código aleatório usa `random_int` e não é escrito em logs fora dos ambientes local/teste.
- Bloqueado login de cliente inativo e renovada a sessão ao autenticar.
- Endereços e carrinhos de outros clientes não podem ser alterados/usados por ID direto.
- No primeiro endereço, marcado automaticamente como padrão; estado salvo em maiúsculas.
- Checkout confere que o endereço pertence ao cliente autenticado.
- Carrinho valida produto, tamanho e sabores no servidor e usa preços calculados no servidor.
- Produto inativo ou em categoria inativa não pode ser aberto pelo cardápio.
- Adicionada suíte de testes de feature cobrindo OTP, login de funcionário, perfil, propriedade dos endereços/carrinho, criação do pedido e transição na cozinha.
- Atualizada a linguagem visual das telas de cardápio e cliente, com paleta quente, hierarquia de conteúdo, foco acessível, estados de formulário e ajustes responsivos.

## O que falta para um sistema de restaurante mais completo

| Prioridade | Recomendação | Motivo |
|---|---|---|
| Alta | Integrar envio real de OTP por SMS/WhatsApp e aplicar expiração, limite por telefone/IP e proteção contra abuso. | Hoje o serviço cria e valida o código no banco, mas não há provedor de entrega configurado; sem isso, o cliente não recebe o código em produção. |
| Alta | Integrar pagamento online (Pix/cartão) com confirmação por webhook e conciliação. | Pagamentos existentes cobrem o PDV presencial; o checkout web não oferece captura online. |
| Alta | Confirmar taxa e disponibilidade por CEP/bairro/área atendida. | A taxa atual é fixa ou grátis por valor mínimo; não há zonas de entrega. |
| Alta | Revisar estados do pedido e regras de transição comuns entre painel administrativo, cozinha e acompanhamento. | Há vocabulários distintos (`entregue`, `fechado`, `finalizado`) em pontos diferentes; uma máquina de estados central reduz divergência. |
| Média | Cadastro de entregador, atribuição, contato e registro de saída/entrega. | O fluxo registra “saiu para entrega”, mas não identifica entregador nem fornece gestão de rota. |
| Média | Estoque e ficha técnica de ingredientes, baixa por venda e alerta de ruptura. | Produtos podem ser ativados/desativados, mas não há estoque por ingrediente/insumo. |
| Média | Cupons, promoções por período, combos e fidelidade. | Não aparecem entidades/regras de desconto promocional ou relacionamento de recompra. |
| Média | Notificações ao cliente por WhatsApp/e-mail e aviso de mudança de status. | O acompanhamento existe por consulta; notificações proativas reduzem dúvidas. |
| Média | Exportação e indicadores operacionais de delivery (tempo real, cancelamento, ticket médio, bairro). | Os relatórios presentes focam operação/venda geral. |
| Média | Cancelamento com motivo, política de reembolso e trilha de alterações do pedido online. | Há status de cancelamento e logs gerais, mas o fluxo de atendimento/reembolso precisa de regra explícita. |
| Baixa | Avaliações, favoritos e repetição rápida do último pedido. | Melhoram descoberta e conveniência do consumidor. |
| Alta | Política de privacidade, retenção e exclusão/anonimização de dados pessoais. | O sistema armazena celular, CPF, endereço e dados faciais de funcionários; deve documentar finalidade, acesso e retenção. |
| Alta | Testes de autorização por perfil para cada rota administrativa e validação de regras de negócio/concorrência. | Há middleware de função e permissões, mas a cobertura precisa garantir cada operação e cenários simultâneos. |

## Limitações de execução nesta máquina

A suíte foi escrita, mas não foi possível executá-la neste ambiente porque PHP/Composer e as dependências `vendor` não estão instalados. A compilação de assets também depende de `node_modules`. É necessário instalar o ambiente Laravel configurado no projeto para confirmar migrations, testes HTTP e build em execução. O envio real de OTP também depende de escolher e configurar um provedor de SMS/WhatsApp.
