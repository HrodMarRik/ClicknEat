# ClicknEat - Documentation Technique

## Vue d'ensemble

ClicknEat est une application web de réservation et commande de repas en ligne développée dans le cadre du BTS SIO option SLAM. Cette plateforme permet aux clients de réserver une table dans un restaurant et de commander leur repas à l'avance, afin que celui-ci soit prêt dès leur arrivée, optimisant ainsi l'expérience client et la gestion des restaurants.

## Contexte professionnel

Ce projet répond à une demande croissante dans le secteur de la restauration pour des solutions numériques permettant d'optimiser la gestion des établissements et d'améliorer l'expérience client. Dans un contexte post-pandémique où la digitalisation des services est devenue essentielle, ClicknEat offre une solution complète pour connecter les restaurants à leur clientèle.

## Compétences mises en œuvre

Ce projet mobilise plusieurs compétences du référentiel BTS SIO SLAM :
- **Développement d'applications** : Conception et développement d'une application web complète
- **Gestion de projet** : Planification, organisation et suivi du développement
- **Base de données** : Conception et manipulation d'une base de données relationnelle
- **Cybersécurité** : Mise en place d'un système d'authentification sécurisé
- **Intégration web** : Développement d'interfaces responsives et ergonomiques

## Objectifs du Projet

- Permettre aux clients de commander des repas en ligne
- Offrir aux restaurants une interface de gestion de leurs commandes
- Fournir un système de paiement sécurisé
- Gérer les réservations de tables
- Optimiser l'expérience utilisateur dans la restauration

## Architecture Technique

### Stack Technologique

- **Backend**: Laravel 10 (PHP 8.2)
- **Frontend**: Blade, JavaScript, CSS, Bootstrap 5
- **Base de données**: MySQL
- **Authentification**: Laravel Breeze
- **Paiement**: Stripe (via Laravel Cashier)
- **Déploiement**: Git, GitHub Actions (CI/CD)

### Composants Principaux

1. **Système d'authentification**
   - Gestion des rôles (Admin, Restaurateur, Client)
   - Authentification sécurisée
   - Vérification d'email

2. **Gestion des restaurants**
   - CRUD des restaurants
   - Gestion des menus et plats
   - Configuration des horaires et disponibilités

3. **Système de commande**
   - Panier d'achat
   - Processus de checkout
   - Intégration Stripe

4. **Gestion des utilisateurs**
   - Profils utilisateurs
   - Historique des commandes
   - Préférences et adresses

## Fonctionnalités Principales

### Pour les Clients
- Création de compte et authentification
- Recherche de restaurants
- Commande de plats
- Paiement sécurisé
- Suivi des commandes

### Pour les Restaurateurs
- Gestion du menu
- Suivi des commandes
- Configuration du restaurant
- Statistiques de vente

### Pour les Administrateurs
- Gestion globale des utilisateurs
- Supervision des restaurants
- Rapports et statistiques
- Configuration système

## Structure du Projet

```
UberEat/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Auth/
│   │   │   └── Client/
│   ├── Models/
│   └── Services/
├── resources/
│   └── views/
├── routes/
│   └── web.php
└── config/
```

## Sécurité

- Protection CSRF
- Validation des données
- Authentification sécurisée
- Gestion des permissions
- Sécurisation des paiements

## Maintenance et Support

Le projet est maintenu activement avec :
- Mises à jour de sécurité régulières
- Corrections de bugs
- Améliorations continues
- Support technique disponible
