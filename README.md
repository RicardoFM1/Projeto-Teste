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

### Estrutura:

O projeto foi divido em 4 APIs:

-API-Usuario;
-API-Convidado;
-API-Checkin;
-API-Acompanhante;

Cada uma com seu banco de dados, exceto de convidado e acompanhante, que estão no mesmo banco de dados

Os bancos de dados são:
db_usuario
db_convidado
db_checkin

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

## Uso no workbench:
- .SQL:

<details>
<summary>Clique para ver o .SQL</summary>
```sql
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema db_usuario
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_usuario
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_usuario` DEFAULT CHARACTER SET utf8 ;
-- -----------------------------------------------------
-- Schema db_convidado
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_convidado
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_convidado` ;
-- -----------------------------------------------------
-- Schema db_checkin
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_checkin
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_checkin` ;
USE `db_usuario` ;

-- -----------------------------------------------------
-- Table `db_usuario`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_usuario`.`usuario` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `senha` LONGTEXT NOT NULL,
  `cargo` VARCHAR(45) NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE INDEX `cpf_UNIQUE` (`cpf` ASC) VISIBLE,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB;

USE `db_convidado` ;

-- -----------------------------------------------------
-- Table `db_convidado`.`convidado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_convidado`.`convidado` (
  `id_convidado` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `sobrenome` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
  `telefone` VARCHAR(15) NOT NULL,
  `numero_mesa` INT NULL,
  PRIMARY KEY (`id_convidado`),
  UNIQUE INDEX `cpf_UNIQUE` (`cpf` ASC) VISIBLE,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_convidado`.`acompanhante`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_convidado`.`acompanhante` (
  `id_acompanhante` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `sobrenome` VARCHAR(45) NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
  `telefone` VARCHAR(45) NOT NULL,
  `convidado_idconvidado` INT NOT NULL,
  PRIMARY KEY (`id_acompanhante`),
  INDEX `fk_convidado_idconvidado_idx` (`convidado_idconvidado` ASC) VISIBLE,
  CONSTRAINT `fk_convidado_idconvidado`
    FOREIGN KEY (`convidado_idconvidado`)
    REFERENCES `db_convidado`.`convidado` (`id_convidado`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `db_checkin` ;

-- -----------------------------------------------------
-- Table `db_checkin`.`checkin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_checkin`.`checkin` (
  `id_checkin` INT NOT NULL AUTO_INCREMENT,
  `convidado_idconvidado` INT NOT NULL,
  `usuario_idusuario` INT NOT NULL,
  PRIMARY KEY (`id_checkin`),
  INDEX `fk_convidado_idconvidado_idx` (`convidado_idconvidado` ASC) VISIBLE,
  INDEX `fk_usuario_idusuario_idx` (`usuario_idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_convidado_idconvidado`
    FOREIGN KEY (`convidado_idconvidado`)
    REFERENCES `db_convidado`.`convidado` (`id_convidado`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuario_idusuario`
    FOREIGN KEY (`usuario_idusuario`)
    REFERENCES `db_usuario`.`usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

``` 
</details>

### Chaves de ambiente:

Crie um arquivo no raiz do projeto chamado .env e coloque:

```
DBUSUARIO_NAME="db_usuario"

DBCONVIDADO_NAME="db_convidado"

DBCHECKIN_NAME="db_checkin"


DB_HOST="localhost"
DB_USER="root"
DB_PASS="SUA_CHAVE_DO_BANCO"

JWT_SECRET_KEY=WDBAWHDBWADWAKJDANKJSBDJWKJANWDKBWJHVDBJHAWBDKWAJDKJAWCDKWA!!!!!@@@!@!@!!@1212144
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
	"cpf": "406.012.850-34",
    "convidado_idconvidado": 1
}

```

</details>

---

# Lógicas de autenticação


## Rotas:
- Rota de usuário em:

* PRECISA DE ADMIN:
/usuario | GET
/usuario | POST
/usuario | PUT
/usuario | DELETE

* Login:
/usuario/login | POST

---


- Rota de convidado em:

* PRECISA DE AUTENTICAÇÃO:

/convidado | GET
/convidado | POST
/convidado | PUT
/convidado | DELETE




- Rota de checkin em:

* PRECISA DE AUTENTICAÇÃO:

/checkin | GET
/checkin | POST
/checkin | PUT
/checkin | DELETE



- Rota de acompanhante em:

* PRECISA DE AUTENTICAÇÃO:

/acompanhante | GET
/acompanhante | POST
/acompanhante | PUT
/acompanhante | DELETE