# ESGI4-ROOMIES

## /backend
symfony server:start
docker compose up
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

php bin/phpunit tests/Service/GameServiceTest.php


## /frontend
npm install
npm run dev

npx playwright test


user@example.com
password123

test@test.fr
test