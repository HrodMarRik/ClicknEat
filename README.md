<p align="center"><img src="https://via.placeholder.com/400x100.png?text=FoodExpress" width="400" alt="FoodExpress Logo"></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# ClicknEat - Projet BTS SIO SLAM

## Présentation du projet

FoodExpress est une application web de réservation et commande de repas en ligne développée dans le cadre du BTS SIO option SLAM. Cette plateforme permet aux clients de réserver une table dans un restaurant et de commander leur repas à l'avance, afin que celui-ci soit prêt dès leur arrivée, optimisant ainsi l'expérience client et la gestion des restaurants.

## Contexte professionnel

Ce projet répond à une demande croissante dans le secteur de la restauration pour des solutions numériques permettant d'optimiser la gestion des établissements et d'améliorer l'expérience client. Dans un contexte post-pandémique où la digitalisation des services est devenue essentielle, FoodExpress offre une solution complète pour connecter les restaurants à leur clientèle.

## Compétences mises en œuvre

Ce projet mobilise plusieurs compétences du référentiel BTS SIO SLAM :
- **Développement d'applications** : Conception et développement d'une application web complète
- **Gestion de projet** : Planification, organisation et suivi du développement
- **Base de données** : Conception et manipulation d'une base de données relationnelle
- **Cybersécurité** : Mise en place d'un système d'authentification sécurisé
- **Intégration web** : Développement d'interfaces responsives et ergonomiques

## Fonctionnalités principales

### Pour les clients
- Création de compte et authentification sécurisée
- Recherche de restaurants par catégorie, localisation ou évaluation
- Réservation de table avec choix de date et heure
- Commande de plats en ligne avec personnalisation
- Paiement sécurisé via Stripe
- Suivi en temps réel de l'état des commandes
- Historique des commandes et réservations

### Pour les restaurateurs
- Tableau de bord de gestion personnalisé
- Gestion des menus, plats et catégories
- Configuration des horaires d'ouverture
- Suivi et gestion des réservations et commandes
- Statistiques et rapports d'activité
- Personnalisation de la présentation de leur établissement

### Pour les administrateurs
- Gestion complète des utilisateurs et de leurs droits
- Supervision de l'ensemble des restaurants
- Tableaux de bord analytiques
- Gestion des paramètres globaux de l'application

## Technologies utilisées

- **Backend** : Laravel 10 (PHP 8.2)
- **Frontend** : Blade, JavaScript, CSS, Bootstrap 5
- **Base de données** : MySQL
- **Authentification** : Laravel Breeze
- **Paiement** : API Stripe (via Laravel Cashier)
- **Déploiement** : Git, GitHub Actions (CI/CD)
- **Sécurité** : HTTPS, CSRF protection, validation des données

## Architecture technique

L'application suit le pattern MVC (Modèle-Vue-Contrôleur) de Laravel :
- **Modèles** : Représentation des données et des relations entre elles
- **Vues** : Templates Blade pour l'affichage des interfaces utilisateur
- **Contrôleurs** : Gestion de la logique métier et des interactions

## Installation et déploiement

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- Node.js et NPM
- MySQL

### Installation en local

1. Clonez le dépôt
   ```
   git clone https://github.com/votre-nom/foodexpress.git
   cd foodexpress
   ```

2. Installez les dépendances
   ```
   composer install
   npm install
   ```

3. Configurez l'environnement
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Configurez la base de données dans le fichier .env

5. Exécutez les migrations et les seeders
   ```
   php artisan migrate --seed
   ```

6. Configurez Stripe dans le fichier .env
   ```
   STRIPE_KEY=votre_clé_publique
   STRIPE_SECRET=votre_clé_secrète
   ```

7. Compilez les assets
   ```
   npm run dev
   ```

8. Lancez le serveur
   ```
   php artisan serve
   ```

## Documentation

La documentation complète du projet est disponible dans le dossier `/docs` et comprend :
- Documentation technique
- Manuel utilisateur
- Diagrammes UML (cas d'utilisation, classes, séquences)
- Modèle conceptuel de données

## Tests et qualité

Le projet inclut des tests unitaires et fonctionnels pour garantir la qualité du code :
```
php artisan test
```

## Perspectives d'évolution

- Intégration d'un système de notation et d'avis
- Application mobile (React Native)
- Système de fidélité et de promotions
- Intégration avec des services de livraison
- Analyse prédictive pour optimiser les stocks et la préparation

## Auteur

[Votre Nom] - BTS SIO SLAM - [Votre établissement]

## Licence

Ce projet est développé dans un cadre éducatif et est soumis à la licence MIT.

---

*Ce projet a été réalisé dans le cadre de l'épreuve E4 du BTS SIO option SLAM.*
