# travel-manager
Aplicação que gerencia pedidos de viagem corporativa.

---

## Como Executar

### 1. Pré-requisitos

- Docker instalado ([https://docs.docker.com/get-docker/](https://docs.docker.com/get-docker/))
- Docker Compose (plugin ou pacote clássico)
- Git

---

### 2. Clonar o repositório

git clone https://github.com/andreoreis/travel-manager.git
cd travel-manager

### 3. Estrutura do projeto

travel-manager/
├── backend/        # Laravel 11 + Dockerfile
├── frontend/       # Vue.js + Dockerfile
├── docker-compose.yml
└── README.md

### 4. Configurar variáveis de ambiente

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=travel_manager
DB_USERNAME=root
DB_PASSWORD=root

### 5. Subir os containers

cd /caminho/travel-manager  # substitua pelo caminho real
docker compose up --build -d

### 6. Configuração do MySQL no DBeaver ou outro gerenciador de banco de dados

Host: 127.0.0.1
Port: 3306
Database: travel_manager
Username: root
Password: root

### 7. Executar migrations do Laravel

docker compose exec app bash
php artisan migrate

### 8. Instalar dependências do frontend

Se você ainda não instalou as dependências do Vue, execute este comando na raiz do projeto:

cd travel-manager/frontend 
npm install

### 9. Rodar o frontend

Após instalar as dependências, inicie o servidor do Vue no caminho travel-manager/frontend :

npm run dev

### 10. Verificar portas

Após subir os containers, é importante conferir **quais portas estão realmente mapeadas**. Para isso, rode:

docker compose ps

Exemplo de saída:

NAME                      IMAGE                     COMMAND                  SERVICE    STATUS         PORTS
travel-manager-app        travel-manager-app        "docker-php-entrypoi…"   app        Up             9000/tcp
travel-manager-db         mysql:8.0                 "docker-entrypoint.s…"   db         Up             0.0.0.0:3306->3306/tcp, 33060/tcp
travel-manager-frontend   travel-manager-frontend   "docker-entrypoint.s…"   frontend   Up             0.0.0.0:5174->5174/tcp
travel-manager-redis      redis:7.0                 "docker-entrypoint.s…"   redis      Up             0.0.0.0:6379->6379/tcp
travel-manager-web        nginx:alpine              "/docker-entrypoint.…"   web        Up             0.0.0.0:8080->80/tcp


### 11. Identificando e acessando os serviços

Backend Laravel (API)

Porta exposta no host: 8080

Acesse pelo navegador ou via API client:

http://localhost:8080


A porta 9000 mostrada é interna do PHP-FPM e não é usada externamente.

Frontend Vue

Porta exposta no host: 5174

Acesse pelo navegador:

http://localhost:5174


MySQL (para ferramentas externas como DBeaver)

Host: 127.0.0.1

Porta: 3306

Usuário: root

Senha: root

Redis (para cache ou filas)

Host: 127.0.0.1

Porta: 6379

### 12. Rodando a Fila

Execute:

cd travel-manager/backend
docker compose exec app php artisan queue:work --tries=3 --sleep=3 --timeout=90

### 13. Configuração de CORS

Se ao consumir a API você receber erros de CORS (Cross-Origin Resource Sharing), verifique se a porta do frontend está correta na configuração de CORS do Laravel.

O arquivo de configuração é:

// config/cors.php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5174'], // frontend Vite
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];


Se você estiver rodando o frontend em outra porta (por exemplo, http://localhost:5173), altere a linha:

'allowed_origins' => ['http://localhost:5174'],


para a porta correta:

'allowed_origins' => ['http://localhost:5173'],


Depois disso, limpe o cache de configuração do Laravel:

php artisan config:clear

### 14. Executar os testes

Execute:

cd travel-manager/backend
php artisan test
