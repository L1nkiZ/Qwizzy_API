# Qwizzy API - Monitoring & Rate Limiting

## 📊 Stack de Monitoring

Votre API Qwizzy est maintenant équipée d'une stack complète de monitoring et de limitation d'accès :

### Services Docker déployés

1. **Prometheus** (port 9090) - Collecte et stockage des métriques
2. **Grafana** (port 3000) - Visualisation des métriques

## 🚀 Démarrage

```bash
# Dans le dossier Qwizzy_API
docker compose up -d --build
```

## 📈 Accès aux dashboards

- **Prometheus** : http://localhost:9090
- **Grafana** : http://localhost:3000
  - Username: `admin`
  - Password: `admin`
- **API Metrics** : http://localhost:8000/api/metrics

## 🔍 Métriques collectées

Le middleware Prometheus collecte automatiquement :

1. **`qwizzy_http_requests_total`** - Nombre total de requêtes HTTP
   - Labels : `method`, `route`, `status`

2. **`qwizzy_http_request_duration_seconds`** - Durée des requêtes
   - Labels : `method`, `route`, `status`
   - Buckets : 0.005s à 10s

3. **`qwizzy_http_response_size_bytes`** - Taille des réponses
   - Labels : `method`, `route`, `status`

## 🛡️ Rate Limiting

Trois niveaux de limitation configurés :

### 1. API Standard (`throttle:api`)
- **Limite** : 100 requêtes/minute par IP ou utilisateur
- **Usage** : Appliqué par défaut à toutes les routes API

### 2. Strict (`throttle:strict`)
- **Limite** : 20 requêtes/minute
- **Usage** : Pour les opérations sensibles
- **Exemple d'application** :
```php
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:strict');
```

### 3. Guest (`throttle:guest`)
- **Limite** : 30 requêtes/minute par IP
- **Usage** : Pour les utilisateurs non authentifiés
- **Exemple** :
```php
Route::get('/public-data', [DataController::class, 'index'])
    ->middleware('throttle:guest');
```

## 📊 Dashboard Grafana

Le dashboard **"Qwizzy API Monitoring"** affiche :

1. **Request Rate** - Taux de requêtes par seconde
2. **Total Requests per Minute** - Gauge du volume de requêtes
3. **Response Time (P95/P99)** - Latence au 95e et 99e percentile
4. **HTTP Status Codes** - Distribution des codes de réponse
5. **Response Size** - Taille des réponses HTTP
6. **Error Rate (5xx)** - Taux d'erreurs serveur

## 🔧 Configuration

### Variables d'environnement (.env)

```env
# Cache & Session en mode fichier
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### Prometheus scraping

Prometheus collecte les métriques toutes les **5 secondes** via l'endpoint `/api/metrics`.

Configuration : `prometheus/prometheus.yml`

## 🧪 Test du système

```bash
# Test du rate limiting
for i in {1..150}; do curl http://localhost:8000/api/difficulties; done

# Vérifier les métriques
curl http://localhost:8000/api/metrics

# Vérifier Prometheus
curl http://localhost:9090/api/v1/query?query=qwizzy_http_requests_total
```

## 🎯 Personnalisation du Rate Limiting

Modifier `app/Providers/RouteServiceProvider.php` pour ajuster les limites :

```php
RateLimiter::for('custom', function (Request $request) {
    return Limit::perMinute(50)
        ->by($request->user()?->id ?: $request->ip());
});
```

Puis appliquer sur une route :

```php
Route::get('/custom-route', [Controller::class, 'method'])
    ->middleware('throttle:custom');
```

## 📝 Notes importantes

- Les métriques sont stockées dans APCu (en mémoire PHP) pour une performance optimale
- Grafana est pré-configuré avec la datasource Prometheus
- Le dashboard est automatiquement provisionné au démarrage
- Les données Prometheus sont persistées dans un volume Docker

## 🔒 Sécurité

En production, n'oubliez pas de :
1. Changer le mot de passe Grafana
2. Sécuriser l'accès à Prometheus (pas d'exposition publique)
3. Ajuster les limites de rate limiting selon votre usage
4. Activer l'authentification sur l'endpoint `/metrics`
