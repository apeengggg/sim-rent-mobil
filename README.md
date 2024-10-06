REQ :
PHP 8.2.18
NODE 20.17.0
NPM 10.8.2

1. COMPOSER INSTALL
2. cp .env.example .env
3. php artisan key:generate
4. npm install --legacy-peer-deps
5. setup db in .env
6. php artisan migrate --seed

ADMIN:
    username: admin
    password: admin123
USER:
    username: user
    password: user1234