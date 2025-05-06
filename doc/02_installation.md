# Guide d'Installation

## Prérequis

- PHP >= 8.2
- Composer >= 2.0
- MySQL >= 8.0
- Node.js >= 16.0
- Git

## Configuration Système Recommandée

- Mémoire RAM : 4GB minimum
- Espace disque : 2GB minimum
- Système d'exploitation : Linux, macOS, ou Windows avec WSL2

## Installation Pas à Pas

### 1. Cloner le Projet

```bash
git clone https://github.com/votre-organisation/UberEat.git
cd UberEat
```

### 2. Installation des Dépendances PHP

```bash
composer install
```

### 3. Installation des Dépendances Frontend

```bash
npm install
npm run build
```

### 4. Configuration de l'Environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### 5. Configuration de la Base de Données

Modifier le fichier `.env` avec vos paramètres de base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ClicknEat
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

### 6. Migration et Seeding

```bash
# Créer les tables
php artisan migrate

# Charger les données de test (optionnel)
php artisan db:seed
```

### 7. Configuration de Stripe

Ajouter vos clés Stripe dans le fichier `.env` :

```env
STRIPE_KEY=votre_cle_publique
STRIPE_SECRET=votre_cle_secrete
```

### 8. Configuration du Serveur Mail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
MAIL_ENCRYPTION=tls
```

### 9. Storage Link

```bash
php artisan storage:link
```

### 10. Démarrage du Serveur

```bash
# Démarrer le serveur de développement
php artisan serve

# Dans un autre terminal, lancer le compilateur d'assets
npm run dev
```

## Vérification de l'Installation

1. Accédez à `http://localhost:8000`
2. Vérifiez que la page d'accueil s'affiche correctement
3. Testez la création d'un compte
4. Vérifiez la connexion à la base de données

## Configuration Supplémentaire

### Cache et Optimisation

```bash
# Optimiser l'application
php artisan optimize

# Mettre en cache la configuration
php artisan config:cache

# Mettre en cache les routes
php artisan route:cache
```

### Configuration du Cron (Production)

Ajouter au crontab :

```bash
* * * * * cd /chemin/vers/projet && php artisan schedule:run >> /dev/null 2>&1
```

## Résolution des Problèmes Courants

### Erreur de Permissions

```bash
# Ajuster les permissions des dossiers
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Erreur de Composer

```bash
# Nettoyer le cache de Composer
composer clear-cache

# Mettre à jour les autoloads
composer dump-autoload
```

## Support

En cas de problème lors de l'installation, veuillez :

1. Consulter les logs dans `storage/logs/laravel.log`
2. Vérifier la documentation Laravel officielle
3. Contacter l'équipe de support technique

## Environnements de Développement Recommandés

- VS Code avec les extensions PHP
- PHPStorm
- Docker (optionnel) 