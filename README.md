# CorpFlow — Internal SaaS Platform

> Plataforma de gerenciamento interno para times corporativos.  
> Versão 2.4.1 | PHP 8+ | SQLite

---

## Instalação Rápida

```bash
# Clone o repositório
git clone https://github.com/corpflow/corpflow-app
cd corpflow-app

# Certifique-se de ter PHP 8+ com extensão SQLite3
php -S localhost:8080 -t .

# Acesse http://localhost:8080/corpflow
```

> O banco de dados SQLite é criado automaticamente na pasta `database/` na primeira execução.

---

## Credenciais Padrão

| Usuário | Email | Senha | Função |
|---|---|---|---|
| Administrator | admin@corpflow.io | Admin@2024! | admin |
| Demo User | demo@corpflow.io | demo1234 | user |

> ⚠️ Troque as credenciais padrão antes de usar em produção.

---

## Funcionalidades

- **Autenticação** — Login/logout com opção "lembrar de mim"
- **Dashboard** — Visão geral de tarefas e atividades
- **Tarefas** — Criação, edição e acompanhamento de tarefas
- **Documentos** — Upload e compartilhamento de arquivos
- **Busca** — Busca global por tarefas, usuários e documentos
- **Perfil** — Edição de dados, bio e foto de perfil
- **Admin** — Gerenciamento de usuários, logs e backups
- **Diagnósticos** — Ferramenta de teste de conectividade de rede (admin)
- **API REST** — Endpoints JSON em `/api/v1.php`

---

## API

Autentique via sessão ativa ou header `Authorization: Bearer {token}`.

```
GET  /api/v1.php?action=health
GET  /api/v1.php?action=me
GET  /api/v1.php?action=tasks
GET  /api/v1.php?action=get_task&id={id}
POST /api/v1.php?action=create_task
GET  /api/v1.php?action=search&q={query}
GET  /api/v1.php?action=stats          (admin)
```

---

## Estrutura do Projeto

```
corpflow/
├── index.php              # Front controller
├── config.php             # Configurações
├── .env                   # Variáveis de ambiente
├── api/
│   └── v1.php             # API REST
├── controllers/
│   ├── AuthController.php
│   ├── UserController.php
│   ├── AdminController.php
│   └── SearchController.php
├── models/
│   ├── Database.php
│   ├── User.php
│   └── Task.php
├── views/
│   ├── layout_header.php
│   ├── help/              # Seções de ajuda dinâmicas
│   └── reports/           # Templates de relatório
├── uploads/               # Arquivos enviados por usuários
├── logs/                  # Logs da aplicação
└── backups/               # Dumps do banco de dados
```

---

## Notas para Pentest (CorpFlow Security Challenge)

Esta instância foi configurada para análise de segurança. Abaixo estão algumas áreas que **podem** conter problemas de implementação, baseados em tickets abertos na época do desenvolvimento:

### Superfícies de ataque conhecidas pelo time

1. **Autenticação e sessões**  
   O mecanismo de "lembrar de mim" foi implementado com pressa antes do release. O time discutiu sobre a forma de armazenar os dados de sessão, mas optou pela abordagem mais simples (ticket CF-055).

2. **Upload de arquivos**  
   A validação de tipo foi implementada, mas houve debate no time sobre qual método usar. Alguém sugeriu usar `finfo_file()`, mas a implementação final ficou diferente (ticket CF-201).

3. **Busca e queries**  
   A funcionalidade de busca suporta sintaxe avançada para uso futuro. O desenvolvedor responsável tinha confiança que a abordagem escolhida era segura (ticket CF-402).

4. **Carregador de páginas de ajuda**  
   A central de ajuda suporta seções dinâmicas via parâmetro URL. O desenvolvedor removeu `../` das entradas, considerando isso suficiente.

5. **Controle de acesso em documentos e tarefas**  
   Durante refatoração, a verificação de propriedade de recursos foi movida "para o controller" — mas os tickets CF-312 e CF-188 ainda estão abertos.

6. **Ferramenta de diagnóstico**  
   O ping tool do painel admin aceita hosts como entrada. A validação foi delegada para o frontend via JavaScript.

7. **Logs e arquivos de configuração**  
   A pasta de backups e o arquivo `.env` estão no webroot por conveniência de desenvolvimento. Ticket aberto para mover antes da GA.

8. **Tokens de reset de senha**  
   Geração de token descrita no código com comentário explicando a lógica (ticket CF-301).

9. **Bio de usuário**  
   Aceita HTML para "permitir formatação" (ticket CF-177). Exibida sem sanitização adicional.

### Objetivo

Encontre pelo menos uma cadeia que leve a **Remote Code Execution** no servidor.  
Não existe um endpoint direto de RCE — a exploração requer análise do código e encadeamento de falhas.

### Dica de Ouro

> Às vezes, o caminho mais curto para comprometer um sistema não é a vulnerabilidade mais óbvia — é a combinação de duas falhas que, sozinhas, pareceriam inofensivas.

---

## Tecnologias

- PHP 8.x (puro, sem frameworks)
- SQLite 3
- Bootstrap 5
- Font Awesome 6

---

*CorpFlow Inc. — Internal Use Only*
