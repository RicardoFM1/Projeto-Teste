# Senac Wedding Pass

O Intuito do projeto é criar um software que possa ser utilizado por usuários para cadastro de convidados e com afim de disponibilizar a opção de associar a um checkin.


## O que foi utilizado:

### Bibliotecas:
``` "vlucas/phpdotenv": "^5.6",
        "firebase/php-jwt": "^7.0",
        "respect/validation": "^2.4"
```

### Ferramentas:

``` - Composer;
    - Insomnia;
    - Mysql Server;
    - Mysql Workbench;
    - Git;
    - Github;
```

---

### Como testar o projeto: 

- Primeiramente clone o projeto pelo github, abrindo um bash no terminal.
Dentro do bash digite e instale as dependências:
```bash 
    cd Projeto-Teste/backend
    composer i
```

- Após instalar, entre nas 4 pastas de Rotas para iniciar o backend:
Abra 4 terminais e em cada um, separadamente, execute:

Rota de usuários:
```Terminal 1```

```bash
    cd API-Usuario/Route
    php -S localhost:3000

```

Rota de Convidados:
```Terminal 2```
```bash
    cd API-Convidado/Route
    php -S localhost:3001

```

Rota de Checkin:
```Terminal 3```
```bash
    cd API-Checkin/Route
    php -S localhost:3002

```
Rota de Acompanhante:
```Terminal 4```

```bash
    cd API-Acompanhante/Route
    php -S localhost:3003

```

---

# Uso no insomnia/postman:

As rotas são as seguintes:

*** Rota de usuários ***

Rota| Porta | Método | Auth |
----|-------|--------|------|
`/usuario|3000| GET  | Admin |
`/usuario|3000| POST | Admin |
`/usuario/login|3000| POST | Todos |
`/usuario?id_usuario={id}|3000| PUT  | Admin | 
`/usuario?id_usuario={id}|3000| DELETE | Admin |

<details>
<summary>Expanda para encontra o que enviar no corpo da requisição</summary>
```json
{
    "nome": "Ricardo",
	"sobrenome": "Fernandes",
	"email": "Ricardete1@gmail.com",
	"telefone": "51984018587",
	"cpf": "406.012.850-34"
}

```

</details>

---

*** Rota de convidados ***

Rota| Porta | Método | Auth |
----|-------|--------|------|
`/convidado|3001| GET  | Logado |
`/convidado|3001| POST | Logado |
`/convidado?id_convidado={id}|3001| PUT  | Logado | 
`/convidado?id_convidado={id}|3001| DELETE | Logado |

<details>
<summary>Expanda para encontra o que enviar no corpo da requisição</summary>
```json
{
    "nome": "Ricardo",
	"email": "Ricardete1@gmail.com",
	"senha": "12345678!",
	"cpf": "406.012.850-34",
    "cargo": "ceremonialista"
}

```

</details>

---



*** Rota de Checkins ***

Rota| Porta | Método | Auth |
----|-------|--------|------|
`/checkin|3002| GET  | Logado |
`/checkin|3002| POST | Logado |
`/checkin?id_checkin={id}|3002| PUT  | Logado | 
`/checkin?id_checkin={id}|3002| DELETE | Logado |


<details>
<summary>Expanda para encontra o que enviar no corpo da requisição</summary>
```json
{
    "convidado_idconvidado": 1
}

```

</details>

---



*** Rota de Acompanhantes ***

Rota| Porta | Método | Auth |
----|-------|--------|------|
`/acompanhante|3003| GET  | Logado |
`/acompanhante|3003| POST | Logado |
`/acompanhante?id_acompanhante={id}|3003| PUT  | Logado | 
`/acompanhante?id_acompanhante={id}|3003| DELETE | Logado |


<details>
<summary>Expanda para encontra o que enviar no corpo da requisição</summary>
```json
{
    "nome": "Ricardo",
	"sobrenome": "Fernandes",
	"telefone": "51984018587",
	"cpf": "406.012.850-34"
    "convidado_idconvidado": 1
}

```

</details>

---

# Lógicas de autenticação


## Precisa apenas estar logado:
- Rota de usuário em:
/usuario | GET
/usuario | POST
/usuario | PUT
/usuario | DELETE

Todos necessitam de acesso de ADMIN, menos /usuario/login acima.


- Rota de convidado em:
/convidado | GET
/convidado | POST
/convidado | PUT
/convidado | DELETE




- Rota de checkin em:
/checkin | GET
/checkin | POST
/checkin | PUT
/checkin | DELETE



- Rota de acompanhante em:
/acompanhante | GET
/acompanhante | POST
/acompanhante | PUT
/acompanhante | DELETE