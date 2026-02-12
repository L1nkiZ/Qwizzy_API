# 🙋‍♂️❓ Qwizzy API - Documentation Complète 

## 📋 Table des matières

- [Vue d'ensemble](#vue-densemble)
- [Architecture Docker](#architecture-docker)
- [Accès aux Services](#acces-aux-services)
- [Installation et Démarrage](#installation-et-demarrage)
- [Tableau récapitulatif des services](#tableau-récapitulatif-des-services)
- [Documentation API (Swagger)](#documentation-api-swagger)
- [Gestion de la Base de Données](#gestion-de-la-base-de-donnees)
- [Commandes Utiles](#commandes-utiles)
- [Structure de l'API](#structure-de-lapi)
- [Tests Automatisés](#tests-automatises)
- [Monitoring & Métriques](#monitoring--metriques)
- [Logs des conteneurs](#logs-des-conteneurs)
- [Notes importantes](#notes-importantes)
- [Analyse Comparative REST vs SOAP dans le cadre de Qwizzy](#analyse-comparative-rest-vs-soap-dans-le-cadre-de-qwizzy)

---

## Vue d'ensemble

Qwizzy API est une application Laravel pour la gestion de questions et de quiz. L'API utilise PostgreSQL comme base de données et est entièrement conteneurisée avec Docker.
4
**Technologies utilisées:**
- Laravel 13
- PHP 8.2
- PostgreSQL 16
- Swagger/OpenAPI 3.0
- Docker & Docker Compose

---

## Architecture Docker

Le projet utilise **5 conteneurs Docker** orchestrés via `docker-compose.yml`:

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

### 4. **qwizzy_prometheus** - Collecte des métriques
- **Image**: `prom/prometheus:latest`
- **Port**: `9090`
- **Rôle**: Collecte et stocke les métriques de l'API
- **Container**: `qwizzy_prometheus`

### 5. **qwizzy_grafana** - Visualisation des métriques
- **Image**: `grafana/grafana:latest`
- **Port**: `3000`
- **Rôle**: Dashboards de monitoring en temps réel
- **Container**: `qwizzy_grafana`
- **Login/Mot de passe**:
  - Username: `admin`
  - Password: `admin`

---

## Accès aux Services

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
   - (Facultatif) Cochez "Save password"
   - Cliquez sur "Save"

### **Base de données PostgreSQL** (connexion externe)
- Host : `localhost`
- Port : `5432`
- Database : `qwizzy_api`
- Username : `qwizzy_user`
- Password : `qwizzy_password`


### **Grafana** (Monitoring & Dashboards)
1. Ouvrez : http://localhost:3000
2. Connectez-vous avec :
   - Username : `admin`
   - Password : `admin`
3. Accédez au dashboard **"Qwizzy API Monitoring"** dans le menu Dashboards
4. Visualisez en temps réel :
   - Taux de requêtes par seconde
   - Temps de réponse (P95/P99)
   - Codes HTTP (200, 404, 500...)
   - Taux d'erreurs
   - Taille des réponses

### **Prometheus** (Métriques)
- URL : http://localhost:9090
- Collecte automatique des métriques toutes les 5 secondes
- Consultez les targets : Status → Targets
---

## Installation et Démarrage

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

## Accès aux Services

Une fois les conteneurs démarrés, vous pouvez accéder à:

| Service | URL | Description |
|---------|-----|-------------|
| **API Laravel** | `http://localhost:8000` | Application principale, avec le swagger sur la page par défaut |
| **pgAdmin** | `http://localhost:8080` | Interface de gestion PostgreSQL → login plus haut [Vue d'ensemble](#-vue-densemble) |
| **PostgreSQL** | `localhost:5432` | Connexion directe à la base de données → login plus haut [Vue d'ensemble](#-vue-densemble) |
| **Grafana** | `http://localhost:3000` | Dashboards de monitoring temps réel (admin/admin) |
| **Prometheus** | `http://localhost:9090` | Interface de collecte de métriques |
| **Métriques API** | `http://localhost:8000/api/metrics` | Endpoint des métriques Prometheus (format texte) |

---

## Documentation API (Swagger)

### Accéder aux choix du Swagger

Ouvrez votre navigateur et accédez à:
```
http://localhost:8000/
```

#### Swagger API REST
```
http://localhost:8000/api/documentation
```

#### Swagger API SOAP
```
http://localhost:8000/soap/documentation
```

### La route user (authentification)

Cliquer sur le bouton 🔓Authorize en haut du Swagger et le remplir avec "token123".
Tester la route user avec "Try it out" puis "Execute", 
la réponse si le token est bon : 
```
{
  "user": "ok"
}
```
la réponse si le token est invalide ou manquant : 
```
{
  "message": "Token invalide ou manquant"
}
```

---

## Gestion de la Base de Données

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

## Commandes Utiles

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
docker logs -f qwizzy_db
docker logs -f qwizzy_pgadmin
docker logs -f qwizzy_prometheus
docker logs -f qwizzy_grafana

# Voir les 50 dernières lignes de logs
docker logs qwizzy_app --tail 50

# Suivre les logs en temps réel
docker logs qwizzy_app -f --tail 100

# Redémarrer un conteneur
docker restart qwizzy_app

# Vérifier l'état des conteneurs
docker ps
docker ps -a  # Inclut les conteneurs arrêtés

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

### Tests

```bash
# Exécuter tous les tests
docker exec -it qwizzy_app php artisan test

# Exécuter les tests avec détails
docker exec -it qwizzy_app php artisan test --testdox

# Exécuter un fichier de test spécifique
docker exec -it qwizzy_app php artisan test --filter QuestionControllerTest

# Exécuter les tests avec couverture de code (nécessite xdebug)
docker exec -it qwizzy_app php artisan test --coverage

# Exécuter uniquement les tests d'un groupe spécifique
docker exec -it qwizzy_app php artisan test tests/Feature

# Exécuter les tests en mode parallèle (plus rapide)
docker exec -it qwizzy_app php artisan test --parallel
```

---

## Structure de l'API

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
| **Quiz** |||
| POST | `/api/quizzes` | Créer un quiz |
| PUT | `/api/quizzes/{id}` | Modifier un quiz |
| DELETE | `/api/quizzes/{id}` | Supprimer un quiz |
| POST | `/api/quizzes/{id}/questions` | Ajouter des questions à un quiz |
| **Import/Export** |||
| POST | `/api/import/questions` | Importer des questions |
| GET | `/api/export/questions` | Exporter les questions |
| **Quiz (SOAP)** |||
| POST | `QuizSoapController->GenerateQuiz` | Générer un quiz avec filtres optionnels (Testable depuis le Swagger SOAP) |
| POST | `QuizSoapController->SubmitQuizAnswers` | Soumettre les réponses et obtenir la correction (Testable depuis le Swagger SOAP) |
| GET | `QuizSoapController->GetUserQuizHistory` | Récupérer l'historique des quiz d'un utilisateur (Testable depuis le Swagger SOAP) |
| GET | `QuizSoapController->GetQuizLeaderboard` | Récupérer le classement général (top scores) (Testable depuis le Swagger SOAP) |

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

## Tests Automatisés

Le projet inclut **38 tests automatisés** couvrant tous les controllers de l'API.

### Exécution locale

```bash
# Tous les tests
docker exec -it qwizzy_app php artisan test

# Avec plus de détails
docker exec -it qwizzy_app php artisan test --testdox

# Test spécifique
docker exec -it qwizzy_app php artisan test --filter QuestionControllerTest
```

### GitHub Actions (CI/CD)

Les tests s'exécutent **automatiquement** sur GitHub lors de :
- Push sur, `master`, `develop`, `feat/*`, `fix/*`
- Pull Request vers, `master`, `develop`

Voir les résultats dans l'onglet **Actions** de votre repo GitHub.

### Fichiers de test

| Fichier | Tests | Description |
|---------|-------|-------------|
| `QuestionControllerTest.php` | 15 | CRUD questions, filtrage par thème |
| `SubjectControllerTest.php` | 6 | CRUD sujets, validation |
| `DifficultyControllerTest.php` | 7 | CRUD difficultés, validation points |
| `QuestionTypeControllerTest.php` | 6 | CRUD types de questions |
| `AnswerControllerTest.php` | 2 | Liste des réponses |

**Total : 38 tests**

---

## Monitoring & Métriques

### Accès au monitoring

Le projet inclut du monitoring avec **Prometheus** et **Grafana**.

**Dashboard Grafana** : http://localhost:3000
- Username: `admin`
- Password: `admin`

### Métriques collectées automatiquement

L'API expose des métriques Prometheus sur `/api/metrics` :

```bash
# Consulter les métriques brutes
curl http://localhost:8000/api/metrics

# Vérifier Prometheus
curl http://localhost:9090/api/v1/query?query=qwizzy_http_requests_total
```

### Rate Limiting

L'API implémente une limitation du nombre de requêtes :

| Niveau | Limite | Usage |
|--------|--------|-------|
| **API Standard** | 100 req/min | Appliqué par défaut à toutes les routes API |
| **Strict** | 20 req/min | Pour les opérations sensibles |
| **Guest** | 30 req/min | Pour les utilisateurs non authentifiés |

**Tester le rate limiting** :
```powershell
# PowerShell - Faire 150 requêtes pour dépasser la limite
for ($i=1; $i -le 150; $i++) { 
    curl http://localhost:8000/api/difficulties -UseBasicParsing
}
# Après 100 requêtes → Erreur 429 (Too Many Requests)
```

## Notes importantes

### Pour PowerShell

Si vous utilisez PowerShell, certaines commandes peuvent nécessiter des ajustements:

```powershell
# Restart et génération Swagger
docker restart qwizzy_app
docker exec -it qwizzy_app php artisan l5-swagger:generate

# Migration fresh
docker exec qwizzy_app php artisan migrate:fresh
```

## Analyse Comparative REST vs SOAP dans le cadre de Qwizzy

Contrairement à nos endpoints REST, ceux fait via un serveur SOAP ont l'air bien moins flexibles. Nous reléguons par exemple la tâche de la création d'un quiz à notre QuizGeneratorService pour REST, tandis que notre même endpoint SOAP le fait dans la même méthode. Ce qui signifierait que si notre application était complètement en SOAP, nos fichiers seraient plus verbeux de manière générale, et que l'on pourrait créer du couplage.

Nous avons dû créer une vue spécifique afin de pouvoir consulter et tester les endpoints SOAP, tandis que nous avons pu implémenter un Swagger opérationnel automatiquement avec les en-têtes des fonctions de nos Controllers REST.

Un des principaux inconvénients d'un service SOAP tient à ses réponses au format XML : il faut que le destinataire sache les interpréter et les déchiffrer. Elles sont également plus difficiles à lire telles quelles pour un humain, ce qui peut compliquer le débogage.

Nos endpoints REST sont aussi plus sécurisés grâce à un token d'authentification, qui manque à nos endpoints SOAP.

Etant donné que les systèmes SOAP sont implémentés de manière générale dans des services bancaires, notre application ne nécessite pas autant de complexité métier pour fonctionner.

---

**Développé avec ❤️ par l'équipe Qwizzy**
