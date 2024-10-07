REQ :
PHP 8.2.18
NODE 20.17.0
NPM 10.8.2

1. COMPOSER INSTALL
2. cp .env.example .env
3. php artisan key:generate
4. npm install --legacy-peer-deps
5. setup db in .env
6. setup app_url (php server & port, example: http://localhost:8000) in .env
7. php artisan migrate --seed

Untuk menjalankan aplikasi
1. npm run dev
2. php artisan serve


ADMIN:
    username: admin
    password: admin123
USER:
    username: user
    password: user1234