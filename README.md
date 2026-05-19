# Mini CRM Contatos - Desafio Técnico (Laravel + DDD + TDD)

Este repositório reúne a solução completa do desafio técnico de um sistema de gerenciamento de contatos com cálculo de score assíncrono, fila e atualizações em tempo real.

## Visão geral

A aplicação oferece:

- CRUD completo de contatos via API
- Processamento assíncrono de score por fila
- Regras de cálculo de score baseadas em domínio
- Evento broadcast via Reverb para atualizações em tempo real
- Arquitetura em camadas (DDD / Clean Architecture)
- Documentação consolidada na raiz do repositório

## Estrutura do projeto

- `crm-contatos/app/`: controllers, requests, resources, jobs, events e listeners
- `crm-contatos/src/Domain/`: regras de negócio e value objects (`Email`, `Phone`, `Name`)
- `crm-contatos/src/Application/`: casos de uso e orquestração de fluxo
- `crm-contatos/src/Infrastructure/`: repositório Eloquent e infraestrutura
- `crm-contatos/config/`, `crm-contatos/database/`, `crm-contatos/public/`, `crm-contatos/resources/` e `crm-contatos/routes/`

## Pré-requisitos

- PHP 8.3+
- Composer
- Node.js & npm
- SQLite (ou outro banco de sua preferência)
- Redis (recomendado para fila)

## Instalação

No diretório raiz do repositório:

```bash
cd crm-contatos
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## Configuração do ambiente

Edite `.env` conforme necessário. Para usar Redis e Reverb em desenvolvimento, defina:

```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_APP_ID=local
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

> O `.env.example` já traz configuração padrão com `QUEUE_CONNECTION=database` e `BROADCAST_CONNECTION=log`.

## Banco de dados

Crie o arquivo SQLite e rode as migrations:

```bash
touch database/database.sqlite
php artisan migrate
```

## Executando a aplicação

Use terminais separados para cada processo:

1. Servidor HTTP

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

2. Compilação de assets

```bash
npm run dev
```

3. Worker de filas

```bash
php artisan queue:work
```

4. Reverb WebSocket (quando usar broadcast realtime)

```bash
php artisan reverb:start
```

## Endpoints principais

| Método | Rota | Descrição |
| --- | --- | --- |
| POST | `/api/contacts` | Cria contato (status `pending`, score `0`) |
| GET | `/api/contacts` | Lista contatos |
| GET | `/api/contacts/{id}` | Exibe contato |
| PUT | `/api/contacts/{id}` | Atualiza contato |
| DELETE | `/api/contacts/{id}` | Soft delete |
| POST | `/api/contacts/{id}/process-score` | Enfileira processamento de score |

### Exemplo de payload de criação

```json
{
  "name": "Bruno Melo",
  "email": "bruno@empresa.com.br",
  "phone": "11999999999"
}
```

## Regras de cálculo de score

- E-mail corporativo: +20 pontos
- E-mail `.br`: +10 pontos
- Nome completo: +10 pontos
- Telefone com DDD de São Paulo (11–19): +20 pontos
- Telefone de outros estados: +10 pontos

## Testes

Execute a suíte de testes com:

```bash
php artisan test
```

## Arquitetura e abordagem

O projeto usa DDD e TDD para separar responsabilidades e manter a lógica de domínio independente do framework.

### Camadas principais

- `src/Domain` — regras de negócio, value objects e validações de domínio
- `src/Application` — casos de uso que orquestram o fluxo
- `src/Infrastructure` — persistência e integração com Eloquent
- `app/Http` — controllers, requests e recursos de API
- `app/Jobs` — processamento assíncrono de fila
- `app/Events` / `app/Listeners` — eventos de domínio e reações

