# 🐳 Guide Docker - Qwizzy API

## 📦 Architecture des Conteneurs

Votre projet utilise **3 conteneurs Docker** :

### 1. **qwizzy_app** - Application Laravel
- **Image** : PHP 8.2-FPM (personnalisée)
- **Port** : 8000
- **Rôle** : Exécute votre application Laravel avec le serveur de développement intégré
- **Extensions PHP installées** : PDO, PostgreSQL, mbstring, exif, pcntl, bcmath, gd, zip

### 2. **qwizzy_db** - Base de données PostgreSQL
- **Image** : postgres:16-alpine
- **Port** : 5432
- **Rôle** : Stocke toutes les données de votre application
- **Credentials** :
  - Database : `qwizzy_api`
  - User : `qwizzy_user`
  - Password : `qwizzy_password`

### 3. **qwizzy_pgadmin** - Interface de gestion PostgreSQL
- **Image** : dpage/pgadmin4:latest
- **Port** : 8080
- **Rôle** : Interface web pour gérer visuellement votre base de données
- **Credentials** :
  - Email : `admin@qwizzy.com`
  - Password : `admin`

---

## 🚀 Commandes Docker Essentielles

### **Démarrage et Arrêt**

#### Démarrer tous les conteneurs
```powershell
docker-compose up -d
```
- `-d` : mode détaché (en arrière-plan)

#### Démarrer avec reconstruction des images
```powershell
docker-compose up -d --build
```
- Utilisez cette commande après avoir modifié le `Dockerfile` ou `docker-compose.yml`

#### Arrêter tous les conteneurs
```powershell
docker-compose down
```

#### Arrêter et supprimer les volumes (⚠️ ATTENTION : perte de données)
```powershell
docker-compose down -v
```

#### Redémarrer un conteneur spécifique
```powershell
docker-compose restart app
docker-compose restart db
docker-compose restart pgadmin
```

---

### **Surveillance et Logs**

#### Voir l'état de tous les conteneurs
```powershell
docker-compose ps
```

#### Voir les logs en temps réel
```powershell
# Tous les conteneurs
docker-compose logs -f

# Un conteneur spécifique
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f pgadmin
```
- `-f` : suit les logs en temps réel (comme `tail -f`)
- **Ctrl + C** pour quitter

#### Voir les dernières lignes des logs
```powershell
docker-compose logs --tail=50 app
```

---

### **Accès aux Conteneurs**

#### Accéder au shell du conteneur PHP (app)
```powershell
docker-compose exec app bash
```

Une fois à l'intérieur, vous pouvez :
```bash
# Voir les fichiers
ls -la

# Exécuter des commandes artisan
php artisan --version
php artisan route:list

# Quitter le conteneur
exit
```

#### Accéder au shell PostgreSQL
```powershell
docker-compose exec db psql -U qwizzy_user -d qwizzy_api
```

Commandes PostgreSQL utiles :
```sql
-- Lister toutes les bases de données
\l

-- Lister toutes les tables
\dt

-- Décrire une table
\d nom_de_table

-- Voir les utilisateurs
\du

-- Exécuter une requête
SELECT * FROM users;

-- Quitter PostgreSQL
\q
```

---

## 🎨 Commandes Laravel (Artisan)

### **Syntaxe générale**
```powershell
docker-compose exec app php artisan [commande]
```

### **Base de données**

#### Exécuter les migrations
```powershell
docker-compose exec app php artisan migrate
```

#### Réinitialiser et migrer la base
```powershell
docker-compose exec app php artisan migrate:fresh
```

#### Réinitialiser, migrer et remplir avec les seeders
```powershell
docker-compose exec app php artisan migrate:fresh --seed
```

#### Rollback de la dernière migration
```powershell
docker-compose exec app php artisan migrate:rollback
```

#### Voir le statut des migrations
```powershell
docker-compose exec app php artisan migrate:status
```

### **Génération de code**

#### Créer un contrôleur
```powershell
docker-compose exec app php artisan make:controller UserController
docker-compose exec app php artisan make:controller Api/ProductController --api
```

#### Créer un modèle avec migration
```powershell
docker-compose exec app php artisan make:model Product -m
```

Options supplémentaires :
- `-m` : crée une migration
- `-f` : crée une factory
- `-s` : crée un seeder
- `-c` : crée un contrôleur
- `-a` : crée tout (migration, factory, seeder, controller)

```powershell
docker-compose exec app php artisan make:model Product -a
```

#### Créer une migration
```powershell
docker-compose exec app php artisan make:migration create_products_table
docker-compose exec app php artisan make:migration add_price_to_products_table
```

#### Créer un seeder
```powershell
docker-compose exec app php artisan make:seeder ProductSeeder
```

#### Créer un middleware
```powershell
docker-compose exec app php artisan make:middleware CheckAdmin
```

#### Créer une requête de validation
```powershell
docker-compose exec app php artisan make:request StoreProductRequest
```

### **Cache et configuration**

#### Nettoyer tous les caches
```powershell
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

#### Optimiser l'application (production)
```powershell
docker-compose exec app php artisan optimize
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

### **Informations et débogage**

#### Lister toutes les routes
```powershell
docker-compose exec app php artisan route:list
```

#### Mode interactif (Tinker)
```powershell
docker-compose exec app php artisan tinker
```

Dans Tinker :
```php
// Créer un utilisateur
$user = new App\Models\User;
$user->name = 'Test';
$user->email = 'test@example.com';
$user->save();

// Récupérer tous les utilisateurs
App\Models\User::all();

// Quitter Tinker
exit
```

---

## 📦 Commandes Composer

### **Syntaxe générale**
```powershell
docker-compose exec app composer [commande]
```

### **Installation et mise à jour**

#### Installer toutes les dépendances
```powershell
docker-compose exec app composer install
```

#### Ajouter un package
```powershell
docker-compose exec app composer require vendor/package
```

Exemples :
```powershell
# JWT pour l'authentification
docker-compose exec app composer require tymon/jwt-auth

# Laravel Sanctum (déjà inclus)
docker-compose exec app composer require laravel/sanctum

# Laravel Debugbar
docker-compose exec app composer require barryvdh/laravel-debugbar --dev
```

#### Mettre à jour les dépendances
```powershell
docker-compose exec app composer update
```

#### Supprimer un package
```powershell
docker-compose exec app composer remove vendor/package
```

#### Régénérer l'autoload
```powershell
docker-compose exec app composer dump-autoload
```

---

## 🌐 Accès aux Services

### **Application Laravel**
- URL : http://localhost:8000
- Serveur de développement Laravel intégré

### **pgAdmin** (Interface PostgreSQL)
1. Ouvrez : http://localhost:8080
2. Connectez-vous avec :
   - Email : `admin@qwizzy.com`
   - Password : `admin`
3. Ajoutez un serveur (première fois uniquement) :
   - Clic droit sur "Servers" → "Register" → "Server"
   - **General Tab** :
     - Name : `Qwizzy DB`
   - **Connection Tab** :
     - Host : `db` (nom du conteneur)
     - Port : `5432`
     - Database : `qwizzy_api`
     - Username : `qwizzy_user`
     - Password : `qwizzy_password`
   - Cochez "Save password"
   - Cliquez sur "Save"

### **Base de données PostgreSQL** (connexion externe)
- Host : `localhost`
- Port : `5432`
- Database : `qwizzy_api`
- Username : `qwizzy_user`
- Password : `qwizzy_password`

Utilisez un client comme **DBeaver**, **TablePlus**, ou **DataGrip**

---

## 🔧 Tests

### **Exécuter tous les tests**
```powershell
docker-compose exec app php artisan test
```

### **Exécuter avec PHPUnit**
```powershell
docker-compose exec app ./vendor/bin/phpunit
```

### **Exécuter des tests spécifiques**
```powershell
docker-compose exec app php artisan test --filter UserTest
```

---

## 🛠️ Scénarios Courants

### **Initialiser un nouveau projet**
```powershell
# 1. Démarrer les conteneurs
docker-compose up -d --build

# 2. Installer les dépendances
docker-compose exec app composer install

# 3. Générer la clé de l'application
docker-compose exec app php artisan key:generate

# 4. Exécuter les migrations
docker-compose exec app php artisan migrate

# 5. (Optionnel) Remplir avec des données de test
docker-compose exec app php artisan db:seed
```

### **Réinitialiser complètement la base de données**
```powershell
# Arrêter les conteneurs et supprimer les volumes
docker-compose down -v

# Redémarrer
docker-compose up -d

# Attendre quelques secondes que PostgreSQL démarre

# Recréer la structure
docker-compose exec app php artisan migrate:fresh --seed
```

### **Ajouter une nouvelle fonctionnalité**
```powershell
# 1. Créer le modèle, migration, contrôleur et seeder
docker-compose exec app php artisan make:model Task -a

# 2. Éditer la migration dans database/migrations/

# 3. Exécuter la migration
docker-compose exec app php artisan migrate

# 4. Développer votre fonctionnalité...
```

### **Résoudre les problèmes de permissions**
```powershell
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

### **Voir les erreurs de l'application**
```powershell
# Logs Laravel
docker-compose exec app tail -f storage/logs/laravel.log

# Logs du conteneur
docker-compose logs -f app
```

---

## 🆘 Dépannage

### **Problème : Les conteneurs ne démarrent pas**
```powershell
# Voir les erreurs détaillées
docker-compose up

# Ou voir les logs
docker-compose logs
```

### **Problème : Port déjà utilisé**
Si vous avez l'erreur "port already in use", modifiez les ports dans `docker-compose.yml` :
```yaml
app:
  ports:
    - "9000:8000"  # Utiliser 9000 au lieu de 8000

db:
  ports:
    - "5433:5432"  # Utiliser 5433 au lieu de 5432

pgadmin:
  ports:
    - "8081:80"    # Utiliser 8081 au lieu de 8080
```

Puis redémarrez :
```powershell
docker-compose down
docker-compose up -d
```

### **Problème : Erreur de connexion à la base de données**
```powershell
# Vérifier que PostgreSQL est bien démarré
docker-compose ps

# Vérifier les logs de PostgreSQL
docker-compose logs db

# Vérifier que la base existe
docker-compose exec db psql -U qwizzy_user -d qwizzy_api -c "\dt"

# Vérifier le fichier .env
cat .env | grep DB_
```

### **Problème : Composer très lent**
```powershell
# Désactiver xdebug (si installé)
docker-compose exec app php -d xdebug.mode=off /usr/bin/composer install
```

### **Problème : Reconstruire complètement**
```powershell
# Tout arrêter et supprimer
docker-compose down -v --rmi all

# Nettoyer le cache Docker
docker system prune -a

# Reconstruire
docker-compose up -d --build
```

---

## 📝 Fichiers de Configuration

### **docker-compose.yml**
Définit les 3 services et leur configuration

### **Dockerfile**
Définit l'image personnalisée PHP avec toutes les extensions nécessaires

### **.env**
Configuration Laravel, notamment :
```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=qwizzy_api
DB_USERNAME=qwizzy_user
DB_PASSWORD=qwizzy_password
```

---

## 🎯 Commandes Rapides (Cheat Sheet)

```powershell
# Démarrer
docker-compose up -d

# Arrêter
docker-compose down

# Voir les logs
docker-compose logs -f

# Accéder au conteneur PHP
docker-compose exec app bash

# Artisan
docker-compose exec app php artisan [commande]

# Composer
docker-compose exec app composer [commande]

# PostgreSQL
docker-compose exec db psql -U qwizzy_user -d qwizzy_api

# Reconstruire
docker-compose up -d --build

# État des conteneurs
docker-compose ps
```

---

## 🚦 Workflow de Développement Quotidien

### **Matin - Démarrer le travail**
```powershell
# 1. Démarrer les conteneurs
docker-compose up -d

# 2. Voir que tout est OK
docker-compose ps

# 3. Voir les logs si besoin
docker-compose logs -f
```

### **Pendant le développement**
```powershell
# Créer une nouvelle migration
docker-compose exec app php artisan make:migration create_posts_table

# Exécuter les migrations
docker-compose exec app php artisan migrate

# Voir les routes
docker-compose exec app php artisan route:list

# Tester
docker-compose exec app php artisan test
```

### **Soir - Fin de journée**
```powershell
# Arrêter les conteneurs (garde les données)
docker-compose down
```

---

**Besoin d'aide ?** Consultez :
- [Documentation Laravel](https://laravel.com/docs)
- [Documentation PostgreSQL](https://www.postgresql.org/docs/)
- [Documentation Docker](https://docs.docker.com/)

**Bon développement ! 🚀**
