# Simple Blog Project

## Features
* Reading Posts
* Commenting Posts
* Admin's dashboard for managing content

## Tech Stack
* Symfony
* PHP
* MySQL
* TailwindCSS
* CSS
* JS

## Setup
* install PHP and MySQL
* install symfony cli
* clone repo
* in .env add path to your MySQL database
* in project folder run ```composer install``` and ```npm run watch```
* run ```symfony console doctrine:database:create```
* run ```symfony console doctrine:migrations:migrate```
* run ```symfony console doctrine:fixtures:load```
* run ```symfony server:start``` and ```npm run watch```
* To access admin dashboard - create account and set roles as '["ROLES_USER", "ROLES_ADMIN"]' using SQL