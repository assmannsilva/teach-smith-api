# Teach Smith – API (Back-end)

Back-end do sistema **Teach Smith**, desenvolvido com [Laravel](https://laravel.com/) e estruturado como uma **API RESTful**.

## 🧾 Sobre o Projeto

Este back-end é responsável por toda a lógica e persistência de dados do sistema Teach Smith. Dentre as funcionalidades principais:

- Autenticação de usuários (formulário e OAuth com Google)
- Envio de convites (via formulário ou importação de CSV)
- Sistema de filas assíncronas para envio de convites
- Integração com Mailhog para testes de e-mails
- Suporte a multi-organização
- API protegida com Laravel Sanctum

👉 Front-end disponível aqui:  
[🔗 Teach Smith Frontend (Vue 3)](https://github.com/assmannsilva/teach-smith-front)

## 🚀 Tecnologias Utilizadas

- **PHP 8.4**
- **Laravel 12**
- **Laravel Sanctum** – autenticação SPA
- **PostgreSQL** – banco de dados relacional
- **Redis** – filas e cache
- **Mailhog** – visualização de e-mails em ambiente de desenvolvimento
- **Docker + Docker Compose**
- **Makefile** – automação de comandos
- **Pest** – testes

## 📂 Estrutura de Diretórios

- app/
- Http/
- Controllers/ # Controladores da API
- Models/ # Modelos Eloquent
- Services/ # Lógica de negócio
- Jobs/ # Jobs assíncronos (convites)
- Lib/

- routes/
- api.php # Rotas da API

- database/
- migrations/ # Estrutura do banco

## ▶️ Executando com Docker

> **Pré-requisitos:** Docker e Docker Compose instalados.

```bash
# Subir os containers
make up

# Instalar dependências
make install

# Rodar migrations
make migrate

# Rodar seeders (opcional)
make seed

# Acessar o container PHP
make bash

# Gerar Key (dentro do container)
php artisan key:generate
---
```
### 6. **🧪 Testes**

```bash
make test

---
```
### 7. **🔁 Fila (Queue Worker)**

```bash
make queue

---

```
### 8. **📬 Mailhog**

Todos os e-mails enviados em ambiente local são capturados pelo Mailhog.

- Acesse via navegador: [http://localhost:8025](http://localhost:8025)

## 🔐 Autenticação

- **Sanctum SPA**: autenticação baseada em sessão e cookies, ideal para integração com o front-end Vue 3.
- **OAuth Google**: login via conta Google disponível nas rotas específicas de autenticação.


---

### 11. **📥 Endpoints Principais**
## 📥 Endpoints Principais

### 🔓 Autenticação (Pública)

| Recurso        | Método | Rota                                       | Descrição                              |
|----------------|--------|--------------------------------------------|----------------------------------------|
| Login padrão   | POST   | /standard-auth/login                       | Login com e-mail/senha                 |
| Registro       | POST   | /standard-auth/register                    | Registro padrão                        |
| Registro c/ convite | POST | /standard-auth/register-invited       | Registro com convite                   |
| Google Login   | GET    | /google-auth/generate-login-url            | Gera URL de login Google               |
| Google Register| GET    | /google-auth/generate-register-url         | Gera URL de registro Google            |
| Google Callback| GET    | /google-auth/login                         | Callback do Google login               |
| Registro convidado Google | GET | /google-auth/generate-regiter-invited-url | Gera URL de convite via Google   |

### 🏢 Organização

| Método | Rota                  | Descrição                      |
|--------|-----------------------|--------------------------------|
| POST   | /create-organization  | Criação de uma organização     |

### 🔐 Endpoints Protegidos (`auth:sanctum`)

#### 👤 Perfil do Usuário

| Método | Rota          | Descrição                     |
|--------|---------------|-------------------------------|
| GET    | /profile/     | Dados do usuário autenticado  |
| DELETE | /profile/     | Logout                        |

#### 🧑‍🏫 Professores

| Método | Rota                         | Descrição                        |
|--------|------------------------------|----------------------------------|
| POST   | /teachers/invite             | Envia convite para professor     |
| POST   | /teachers/bulk-invite        | Importa professores via CSV      |
| GET    | /teachers/search             | Busca professores (autocomplete) |

#### 🧑‍🎓 Alunos

| Método | Rota                         | Descrição                        |
|--------|------------------------------|----------------------------------|
| POST   | /students/invite             | Envia convite para aluno         |
| POST   | /students/bulk-invite        | Importa alunos via CSV           |

#### 🏫 Salas de Aula (Classrooms)

| Método | Rota                  | Descrição                       |
|--------|-----------------------|---------------------------------|
| GET    | /classrooms/          | Lista todas as salas            |
| POST   | /classrooms/          | Cria nova sala                  |
| GET    | /classrooms/{id}      | Mostra detalhes de uma sala     |
| PUT    | /classrooms/{id}      | Atualiza uma sala               |
| DELETE | /classrooms/{id}      | Remove uma sala                 |

#### 📚 Disciplinas (Subjects)

| Método | Rota                  | Descrição                       |
|--------|-----------------------|---------------------------------|
| GET    | /subjects/            | Lista todas as disciplinas      |
| POST   | /subjects/            | Cria nova disciplina            |
| GET    | /subjects/{id}        | Mostra detalhes da disciplina   |
| PUT    | /subjects/{id}        | Atualiza disciplina             |
| DELETE | /subjects/{id}        | Remove disciplina               |

## 👤 Autor

Desenvolvido por **Cauê Assmann Silva**