# Projet Laravel - Gestion des commandes d'un restaurant à emporter

## Description
Ce projet est une application de gestion des commandes pour un restaurant proposant des plats à emporter. Il permet aux administrateurs de gérer les articles du menu, les catégories, les commandes et les utilisateurs.

## Technologies utilisées
- Laravel (Framework PHP)
- MySQL (Base de données)
- Blade (Moteur de template Laravel)
- Tailwind CSS (Style des pages)
- Livewire (Interaction dynamique sans rechargement de page)

## Fonctionnalités
- **Gestion des articles** : Création, modification, suppression et affichage des plats et boissons disponibles.
- **Gestion des catégories** : Organisation des articles par catégories (entrées, plats, desserts, boissons, etc.).
- **Gestion des utilisateurs** : Système d'authentification et de rôles (admin, employé).

## Installation
### Prérequis
- PHP (>= 8.0)
- Composer
- MySQL
- Node.js et NPM (pour les assets frontend)

### Étapes d'installation
1. Cloner le projet :
   ```sh
   git clone https://github.com/votre-repo/restaurant-orders.git
   cd restaurant-orders
   ```
2. Installer les dépendances PHP :
   ```sh
   composer install
   ```
3. Copier le fichier `.env.example` et configurer la base de données :
   ```sh
   cp .env.example .env
   ```
4. Générer la clé d'application :
   ```sh
   php artisan key:generate
   ```
5. Configurer la base de données dans `.env`, puis exécuter les migrations :
   ```sh
   php artisan migrate --seed
   ```
6. Installer les dépendances front-end :
   ```sh
   npm install && npm run dev
   ```
7. Lancer le serveur :
   ```sh
   php artisan serve
   ```

## Utilisation
- Accéder à l'application via `http://127.0.0.1:8000`
- Se connecter en tant qu'administrateur pour gérer le menu et les commandes.

## Auteur
Projet développé par **Chanavat Romaric** dans le cadre d'un projet scolaire.



