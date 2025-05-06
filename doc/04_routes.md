# Routes de l'Application

## Routes Web Publiques

### Page d'Accueil et Authentification
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | / | HomeController@index | Page d'accueil |
| GET | /login | Auth\LoginController@showLoginForm | Formulaire de connexion |
| POST | /login | Auth\LoginController@login | Traitement de la connexion |
| POST | /logout | Auth\LoginController@logout | Déconnexion |
| GET | /register | Auth\RegisterController@showRegistrationForm | Formulaire d'inscription |
| POST | /register | Auth\RegisterController@register | Traitement de l'inscription |

### Restaurants et Menus
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /restaurants | RestaurantController@index | Liste des restaurants |
| GET | /restaurants/{id} | RestaurantController@show | Détails d'un restaurant |
| GET | /restaurants/{id}/menu | MenuController@show | Menu d'un restaurant |

## Routes Client Authentifiées

### Gestion du Panier
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /cart | CartController@index | Afficher le panier |
| POST | /cart/add | CartController@add | Ajouter au panier |
| PUT | /cart/update | CartController@update | Modifier le panier |
| DELETE | /cart/remove | CartController@remove | Retirer du panier |

### Commandes
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /orders | OrderController@index | Liste des commandes |
| POST | /orders | OrderController@store | Créer une commande |
| GET | /orders/{id} | OrderController@show | Détails d'une commande |
| GET | /orders/{id}/track | OrderController@track | Suivi de commande |

### Profil Client
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /profile | ProfileController@show | Voir le profil |
| PUT | /profile | ProfileController@update | Modifier le profil |
| GET | /addresses | AddressController@index | Gérer les adresses |
| POST | /addresses | AddressController@store | Ajouter une adresse |

## Routes Restaurateur

### Gestion du Restaurant
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /restaurant/dashboard | Restaurant\DashboardController@index | Tableau de bord |
| PUT | /restaurant/profile | Restaurant\ProfileController@update | Modifier profil restaurant |
| GET | /restaurant/orders | Restaurant\OrderController@index | Gérer les commandes |
| PUT | /restaurant/orders/{id} | Restaurant\OrderController@update | Mettre à jour statut commande |

### Gestion des Menus
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /restaurant/menu | Restaurant\MenuController@index | Gérer le menu |
| POST | /restaurant/menu/items | Restaurant\MenuController@store | Ajouter un plat |
| PUT | /restaurant/menu/items/{id} | Restaurant\MenuController@update | Modifier un plat |
| DELETE | /restaurant/menu/items/{id} | Restaurant\MenuController@destroy | Supprimer un plat |

## Routes Administrateur

### Gestion des Utilisateurs
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /admin/users | Admin\UserController@index | Liste des utilisateurs |
| GET | /admin/users/{id} | Admin\UserController@show | Détails utilisateur |
| PUT | /admin/users/{id} | Admin\UserController@update | Modifier utilisateur |
| DELETE | /admin/users/{id} | Admin\UserController@destroy | Supprimer utilisateur |

### Gestion des Restaurants
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /admin/restaurants | Admin\RestaurantController@index | Liste des restaurants |
| POST | /admin/restaurants | Admin\RestaurantController@store | Créer restaurant |
| PUT | /admin/restaurants/{id} | Admin\RestaurantController@update | Modifier restaurant |
| DELETE | /admin/restaurants/{id} | Admin\RestaurantController@destroy | Supprimer restaurant |

## Routes API

### API Publique
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| GET | /api/restaurants | Api\RestaurantController@index | Liste restaurants |
| GET | /api/restaurants/{id} | Api\RestaurantController@show | Détails restaurant |
| GET | /api/menu/{id} | Api\MenuController@show | Menu restaurant |

### API Authentifiée
| Méthode | URI | Action | Description |
|---------|-----|--------|-------------|
| POST | /api/orders | Api\OrderController@store | Créer commande |
| GET | /api/orders/{id} | Api\OrderController@show | Détails commande |
| GET | /api/user/orders | Api\UserController@orders | Commandes utilisateur |

## Middleware Appliqués

### Routes Publiques
```php
Route::middleware(['web'])->group(function () {
    // Routes publiques
});
```

### Routes Authentifiées
```php
Route::middleware(['web', 'auth'])->group(function () {
    // Routes authentifiées
});
```

### Routes Restaurateur
```php
Route::middleware(['web', 'auth', 'restaurant'])->group(function () {
    // Routes restaurateur
});
```

### Routes Admin
```php
Route::middleware(['web', 'auth', 'admin'])->group(function () {
    // Routes admin
});
```

## Notes sur la Sécurité

- Toutes les routes POST/PUT/DELETE sont protégées par CSRF
- Rate limiting appliqué sur les routes d'API
- Validation des permissions via middleware
- Sanitization des entrées utilisateur 
