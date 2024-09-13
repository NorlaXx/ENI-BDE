# ENI-BDE

[![Coverage](https://img.shields.io/badge/coverage-83%25-brightgreen)](#)

## Description du projet

**ENI-BDE** est une plateforme web développée en Symfony pour la gestion des sorties pour chaque campus de l'École ENI. Ce projet a pour but de faciliter la gestion des évènement organisées par le BDE.

## Technologies utilisées

- **Symfony**
- **PHP** 
- **MySQL**
- **Twig**
- **JavaScript**

## Installation et configuration

### Prérequis

- **PHP** (v8.2 ou supérieur)
- **Composer**
- **MySQL** ou **PostgreSQL** (ou toute autre base de données compatible Symfony)

### Étapes d'installation

1. Clonez le projet depuis le dépôt Git et installer les dépendances:
   ```bash
   git clone https://github.com/NorlaXx/ENI-BDE.git &&
   cd ENI-BDE &&
   composer install
    ```

2. Créer le fichier `.env.local` et la base de données (pour MySQL):
    ```.env
      DATABASE_URL="mysql://{user}:{password}@localhost:3306/{database}?serverVersion=8.0.32&charset=utf8mb4"
    ```
   ```bash
    php bin/console doctrine:database:create &&
    php bin/console doctrine:migrations:migrate
    ```
3. Lancer le serveur:
    ```bash
    symfony server:start
    ```

## Collaborateurs

Voici la liste des collaborateurs ayant participé au développement du projet **ENI-BDE** :

- **Théo Wincke** - https://github.com/sawmodz
- **Maxime Heim** - https://github.com/HeimEni