# 🙋‍♂️❓ Qwizzy API - Documentation Complète 

## � Table des matières
- [Vue d'ensemble](#-vue-densemble)
- [Architecture Docker](#-architecture-docker)
- [Accès aux Services](#-accès-aux-services)
- [Installation et Démarrage](#-installation-et-démarrage)
- [Documentation API (Swagger)](#-documentation-api-swagger)
- [Gestion de la Base de Données](#-gestion-de-la-base-de-données)
- [Commandes Utiles](#-commandes-utiles)
- [Structure de l'API](#-structure-de-lapi)

---

## 🎯 Vue d'ensemble

Qwizzy API est une application Laravel pour la gestion de questions et de quiz. L'API utilise PostgreSQL comme base de données et est entièrement conteneurisée avec Docker.

**Technologies utilisées:**
- Laravel 13
- PHP 8.2
- PostgreSQL 16
- Swagger/OpenAPI 3.0
- Docker & Docker Compose

---

## 🐳 Architecture Docker

Le projet utilise **3 conteneurs Docker** orchestrés via `docker-compose.yml`:

### 1. **qwizzy_app** - Application Laravel
- **Image**: PHP 8.2-FPM
- **Port**: `8000`
- **Rôle**: Exécute l'API Laravel
- **Container**: `qwizzy_app`

### 2. **qwizzy_db** - Base de données PostgreSQL
- **Image**: `postgres:16-alpine`
- **Port**: `5432`
- **Rôle**: Stocke les données de l'application
- **Container**: `qwizzy_db`
- **Login/Mot de passe**:
  - Database: `qwizzy_api`
  - User: `qwizzy_user`
  - Password: `qwizzy_password`

### 3. **qwizzy_pgadmin** - Interface de gestion PostgreSQL
- **Image**: `dpage/pgadmin4:latest`
- **Port**: `8080`
- **Rôle**: Interface web pour gérer la base de données
- **Container**: `qwizzy_pgadmin`
- **Login/Mot de passe**:
  - Email: `admin@qwizzy.com`
  - Password: `admin`

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

---

## 🚀 Installation et Démarrage

### Prérequis
- `Docker`
- `Git`

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/L1nkiZ/Qwizzy_API.git
cd Qwizzy_API
```

2. **Configurer l'environnement**
```bash
# Copier le fichier .env.example
cp .env.example .env
```

3. **Démarrer les conteneurs Docker**
```bash
# Construire et démarrer tous les conteneurs
docker-compose up -d --build
```

4. **Accès au swagger de l'api**
`http://localhost:8000/`

---

## 🌐 Accès aux Services

Une fois les conteneurs démarrés, vous pouvez accéder à:

| Service | URL | Description |
|---------|-----|-------------|
| **API Laravel** | `http://localhost:8000` | Application principale, avec le swagger sur la page par default |
| **pgAdmin** | `http://localhost:8080` | Interface de gestion PostgreSQL → login plus haut [Vue d'ensemble](#-vue-densemble) |
| **PostgreSQL** | `localhost:5432` | Connexion directe à la base de données → login plus haut [Vue d'ensemble](#-vue-densemble) |

---

## 📖 Documentation API (Swagger)

### Accéder à Swagger
Ouvrez votre navigateur et accédez à:
```
http://localhost:8000/
```

### Régénérer la documentation Swagger
Après avoir modifié les annotations dans vos controllers:
```bash
docker exec -it qwizzy_app php artisan l5-swagger:generate
```

---

## 🗄️ Gestion de la Base de Données

### Se connecter à pgAdmin

1. Accédez à `http://localhost:8080`
2. Connectez-vous avec:
   - **Email**: `admin@qwizzy.com`
   - **Password**: `admin`

3. Ajoutez un nouveau serveur:
   - Clic droit sur "Servers" → "Register" → "Server..."
   
   **Onglet General:**
   - Name: `Qwizzy DB`
   
   **Onglet Connection:**
   - Host name/address: `db` (⚠️ Attention pas "localhost" ⚠️)
   - Port: `5432`
   - Maintenance database: `qwizzy_api`
   - Username: `qwizzy_user`
   - Password: `qwizzy_password`
   - (Optionnelle → Cochez "Save password")

### Se connecter directement à PostgreSQL

```bash
# Depuis votre machine locale
psql -h localhost -p 5432 -U qwizzy_user -d qwizzy_api

# Depuis le conteneur
docker exec -it qwizzy_db psql -U qwizzy_user -d qwizzy_api
```

---

## ⚙️ Commandes Utiles

### Docker

```bash
# Démarrer les conteneurs
docker-compose up -d

# Arrêter les conteneurs
docker-compose down

# Voir les logs
docker-compose logs -f

# Voir les logs d'un conteneur spécifique
docker logs -f qwizzy_app

# Redémarrer un conteneur
docker restart qwizzy_app

# Reconstruire les images
docker-compose up -d --build

# Supprimer tout (conteneurs + volumes)
docker-compose down -v
```

### Laravel (dans le conteneur)

```bash
# Exécuter des commandes Artisan
docker exec -it qwizzy_app php artisan <commande>

# Migrations
docker exec -it qwizzy_app php artisan migrate
docker exec -it qwizzy_app php artisan migrate:fresh  # ⚠️ Réinitialise la DB ⚠️
docker exec -it qwizzy_app php artisan migrate:rollback

# Cache
docker exec -it qwizzy_app php artisan cache:clear
docker exec -it qwizzy_app php artisan config:clear
docker exec -it qwizzy_app php artisan route:clear

# Générer Swagger
docker exec -it qwizzy_app php artisan l5-swagger:generate

# Accéder au shell du conteneur
docker exec -it qwizzy_app bash
```

### Composer

```bash
# Installer les dépendances
docker exec -it qwizzy_app composer install

# Mettre à jour les dépendances
docker exec -it qwizzy_app composer update

# Ajouter un package
docker exec -it qwizzy_app composer require nom/package
```

---

## 🏗️ Structure de l'API

### Endpoints disponibles

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| **Difficulties** |||
| GET | `/api/difficulties` | Liste des difficultés |
| POST | `/api/difficulties` | Créer une difficulté |
| GET | `/api/difficulties/{id}/edit` | Obtenir une difficulté pour édition |
| PUT | `/api/difficulties/{id}` | Modifier une difficulté |
| DELETE | `/api/difficulties/{id}` | Supprimer une difficulté |
| **Subjects** |||
| GET | `/api/subjects` | Liste des sujets |
| POST | `/api/subjects` | Créer un sujet |
| GET | `/api/subjects/{id}/edit` | Obtenir un sujet pour édition |
| PUT | `/api/subjects/{id}` | Modifier un sujet |
| DELETE | `/api/subjects/{id}` | Supprimer un sujet |
| **Question Types** |||
| GET | `/api/question-types` | Liste des types de questions |
| POST | `/api/question-types` | Créer un type de question |
| GET | `/api/question-types/{id}/edit` | Obtenir un type pour édition |
| PUT | `/api/question-types/{id}` | Modifier un type de question |
| DELETE | `/api/question-types/{id}` | Supprimer un type de question |
| **Questions** |||
| GET | `/api/questions` | Liste des questions |
| GET | `/api/questions/create` | Données pour créer une question |
| POST | `/api/questions` | Créer une question |
| GET | `/api/questions/{id}` | Afficher une question |
| GET | `/api/questions/{id}/edit` | Données pour éditer une question |
| PUT | `/api/questions/{id}` | Modifier une question |
| DELETE | `/api/questions/{id}` | Supprimer une question |
| **Answers** |||
| GET | `/api/answers` | Liste des réponses |

### Paramètres de pagination

Tous les endpoints de liste supportent ces paramètres:
- `current_sort`: Champ de tri (défaut: `id`)
- `current_sort_dir`: Direction du tri - `asc` ou `desc` (défaut: `asc`)
- `per_page`: Nombre d'éléments par page (défaut: `15`)

**Exemple:**
```
GET /api/questions?current_sort=created_at&current_sort_dir=desc&per_page=20
```

---

## 📝 Notes importantes

### Pour powershell

Si vous utilisez powershell, certaines commandes peuvent nécessiter des ajustements:

```powershell
# Restart et génération Swagger
docker restart qwizzy_app
docker exec -it qwizzy_app php artisan l5-swagger:generate

# Migration fresh
docker exec qwizzy_app php artisan migrate:fresh
```

---

**Développé avec ❤️ par l'équipe Qwizzy**
