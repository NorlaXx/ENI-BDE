name: Symfony project

on:
  push:
    branches:
      - main
  

jobs:
  build_and_deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3

      - name: update http-client
        run: composer require amphp/http-client

      - name: Validate composer.json and composer.lock
        run: composer validate --strict

      - name: Cache Composer packages
        id: composer-cache
        uses: actions/cache@v3
        with:
          path: vendor
          key: ${{ runner.os }}-php-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-php-

      - name: Install dependencies
          run: composer install --prefer-dist --no-progress

      - name: Deploy to server
        run: |
          sshpass -p ${{ secrets.SERVER_PASSWORD }} ssh -p 22103 -o StrictHostKeyChecking=no root@private.blizzfull.fr "cd /var/www/enibde && git pull"

      - name: Migrations
        run: |
          sshpass -p ${{ secrets.SERVER_PASSWORD }} ssh -p 22103 -o StrictHostKeyChecking=no root@private.blizzfull.fr "cd /var/www/enibde && php bin/console doctrine:migrations:migrate --no-interaction"

      - name: Clear cache
        run: |
          sshpass -p ${{ secrets.SERVER_PASSWORD }} ssh -p 22103 -o StrictHostKeyChecking=no root@private.blizzfull.fr "cd /var/www/enibde && php bin/console cache:clear"
      
      - name: Add permission
        run: |
          sshpass -p ${{ secrets.SERVER_PASSWORD }} ssh -p 22103 -o StrictHostKeyChecking=no root@private.blizzfull.fr "cd /var/www/enibde && sudo find /var/www/enibde -type d -exec chmod 755 {} \; && sudo find /var/www/enibde -type f -exec chmod 644 {} \; && sudo chown -R www-data:www-data /var/www/enibde && sudo chmod -R g+w /var/www/enibde/var"

      - name: Restart server
        run: |
          sshpass -p ${{ secrets.SERVER_PASSWORD }} ssh -p 22103 -o StrictHostKeyChecking=no root@private.blizzfull.fr "cd /var/www/enibde && sudo service apache2 restart"