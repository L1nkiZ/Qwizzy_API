# 🤖 Audits de Sécurité Automatiques - Qwizzy API

## 📋 Vue d'ensemble

Votre API dispose maintenant de **4 niveaux d'audits de sécurité automatiques** :

| Niveau | Outil | Fréquence | Type |
|--------|-------|-----------|------|
| 🔴 **Niveau 1** | GitHub Actions | À chaque push | CI/CD |
| 🟠 **Niveau 2** | Dependabot | Hebdomadaire | Dépendances |
| 🟡 **Niveau 3** | Script local | À la demande | Manuel |
| 🟢 **Niveau 4** | PHPStan/Psalm | À chaque push | Code statique |

---

## 🔴 Niveau 1 : GitHub Actions Security Workflow

### Fichier : `.github/workflows/security-audit.yml`

**Déclenchement automatique** :
- ✅ À chaque `push` sur main/master/develop
- ✅ À chaque `pull request`
- ✅ Tous les **lundis à 9h** (audit planifié)
- ✅ Manuellement depuis l'onglet "Actions"

### Ce qui est vérifié :

#### 🔍 1. Composer Security Audit
```bash
composer audit
```
- Détecte les vulnérabilités connues dans vos dépendances PHP
- Compare avec la base CVE (Common Vulnerabilities and Exposures)

#### 🛡️ 2. Security Checker
```bash
vendor/bin/security-checker security:check composer.lock
```
- Double vérification avec un outil tiers
- Analyse le fichier `composer.lock`

#### 📊 3. Configuration Laravel
- ✅ Vérifie que `APP_DEBUG=false` en production
- ✅ Détecte si `.env` est commité
- ✅ Vérifie la configuration CORS

#### 🔐 4. TruffleHog (Secrets Detection)
- 🔎 Scanne tout le code pour des secrets hardcodés
- 🔎 Détecte : API keys, passwords, tokens, credentials
- 🔎 Vérifie l'historique Git complet

#### 📊 5. PHPStan (Analyse statique)
- Détecte les bugs potentiels
- Vérifie la cohérence des types
- Niveau 5 (assez strict)

#### 🛡️ 6. Psalm (Security scan)
- Analyse de sécurité du code
- Détecte les patterns dangereux
- Suggestions d'amélioration

#### 🔐 7. File Permissions
- Vérifie les permissions des fichiers sensibles
- Détecte les fichiers world-writable

### Comment voir les résultats ?

1. Allez sur GitHub → **Actions** → **Security Audit**
2. Cliquez sur la dernière exécution
3. Consultez le rapport téléchargeable

---

## 🟠 Niveau 2 : Dependabot

### Fichier : `.github/dependabot.yml`

**Surveillance automatique** :
- 📦 Dépendances Composer (PHP)
- 🐳 Images Docker
- 🔧 GitHub Actions

### Ce qui se passe :

1. **Tous les lundis à 9h** :
   - Dependabot vérifie vos dépendances
   - Compare avec les versions sécurisées

2. **Si une vulnérabilité est trouvée** :
   - 🚨 Crée automatiquement une **Pull Request**
   - 📝 Décrit la vulnérabilité et le fix
   - ✅ Exécute les tests automatiquement

3. **Vous recevez une notification** :
   - Email de GitHub
   - Notification dans l'interface

### Exemple de PR automatique :

```
🔒 Bump laravel/framework from 10.0.0 to 10.0.5

Security fixes:
- CVE-2024-XXXXX: SQL injection vulnerability
- Severity: HIGH
- Recommended action: Merge immediately

✅ Tests: Passed
📊 Changes: composer.json, composer.lock
```

---

## 🟡 Niveau 3 : Script Local

### Fichier : `security-check.sh`

**Utilisation** :

```bash
# Rendre le script exécutable (une fois)
chmod +x security-check.sh

# Exécuter l'audit
./security-check.sh

# Dans Docker
docker exec -it qwizzy_app bash -c "chmod +x security-check.sh && ./security-check.sh"
```

### Ce qui est vérifié :

```
✅ 1. Configuration environment (.env)
   - APP_DEBUG
   - APP_KEY
   - .env dans git

✅ 2. CORS configuration
   - allowed_origins
   - allowed_methods

✅ 3. Dépendances vulnérables
   - composer audit

✅ 4. Secrets hardcodés
   - Patterns de passwords
   - API keys
   - Tokens

✅ 5. Permissions fichiers
   - Fichiers world-writable
   - Dossier storage

✅ 6. Injection SQL
   - DB::raw usage
   - Requêtes brutes

✅ 7. Authentification
   - Laravel Sanctum
   - Routes protégées

✅ 8. Mass Assignment
   - $fillable dans models
   - $guarded dans models
```

### Résultat :

```bash
============================================
📊 SECURITY AUDIT SUMMARY
============================================
🔴 Critical: 2
🟠 High: 1
🟡 Medium: 3
🟢 Low: 1

❌ AUDIT FAILED - Critical issues found!
```

---

## 🟢 Niveau 4 : Analyse Statique Continue

### PHPStan (`phpstan.neon`)

**Exécution** :

```bash
# Local
./vendor/bin/phpstan analyse app --level=5

# Docker
docker exec -it qwizzy_app ./vendor/bin/phpstan analyse app --level=5
```

**Détecte** :
- 🐛 Bugs potentiels
- 🔧 Erreurs de type
- 📊 Code mort
- 🔒 Patterns dangereux

### Psalm (`psalm.xml`)

**Exécution** :

```bash
# Local
./vendor/bin/psalm

# Docker
docker exec -it qwizzy_app ./vendor/bin/psalm
```

**Focus sécurité** :
- 🔐 Vulnérabilités courantes
- 🛡️ Validation des inputs
- 🔒 Gestion des erreurs

---

## 📊 Dashboard de Sécurité

### Sur GitHub :

1. **Security Tab** → Vue d'ensemble des vulnérabilités
2. **Actions Tab** → Historique des audits
3. **Dependabot Tab** → Alertes de dépendances
4. **Code Scanning** → Résultats des analyses

### Notifications :

Vous recevrez des emails pour :
- 🚨 Nouvelles vulnérabilités détectées
- 📦 Dépendances obsolètes
- ❌ Échec d'audit de sécurité
- ✅ Pull Requests de correction automatiques

---

## 🚀 Configuration Initiale

### 1. Activer les features GitHub

```bash
# Sur GitHub.com → Votre repo → Settings → Security

✅ Cocher "Dependabot alerts"
✅ Cocher "Dependabot security updates"
✅ Cocher "Dependency graph"
✅ Cocher "Code scanning"
```

### 2. Installer les outils locaux

```bash
cd C:\Users\guill\Documents\.Code\.GitHub\Qwizzy\Qwizzy_API

# Dans Docker
docker exec -it qwizzy_app bash

# Installer les outils
composer require --dev phpstan/phpstan
composer require --dev vimeo/psalm
composer require --dev enlightn/security-checker

# Rendre le script exécutable
chmod +x security-check.sh
```

### 3. Première exécution

```bash
# Test local
./security-check.sh

# Voir le résultat
echo $?  # 0 = OK, 1 = Problèmes trouvés
```

---

## 📅 Planning des Audits

| Jour | Heure | Action |
|------|-------|--------|
| **Lundi** | 9h00 | 🔍 Audit GitHub Actions complet |
| **Lundi** | 9h00 | 📦 Scan Dependabot |
| **À chaque push** | - | ✅ Tests + Audit rapide |
| **À chaque PR** | - | 🔎 Dependency Review |
| **À la demande** | - | 🛠️ Script local |

---

## 🎯 Actions Recommandées

### Quotidien
```bash
# Avant de commiter
./security-check.sh
git add .
git commit -m "..."
```

### Hebdomadaire
- ✅ Vérifier les alertes Dependabot
- ✅ Merger les PRs de sécurité
- ✅ Consulter le rapport GitHub Actions

### Mensuel
- ✅ Relire `SECURITY_AUDIT.md`
- ✅ Mettre à jour les dépendances
- ✅ Revoir les configurations

---

## 🔗 Ressources

- [GitHub Security Features](https://docs.github.com/en/code-security)
- [Dependabot Documentation](https://docs.github.com/en/code-security/dependabot)
- [PHPStan Rules](https://phpstan.org/rules)
- [Psalm Security](https://psalm.dev/docs/security_analysis/)

---

## ⚡ Commandes Rapides

```bash
# Audit local complet
docker exec -it qwizzy_app ./security-check.sh

# Vérifier les dépendances
docker exec -it qwizzy_app composer audit

# Analyse statique
docker exec -it qwizzy_app ./vendor/bin/phpstan analyse app

# Scan sécurité avec Psalm
docker exec -it qwizzy_app ./vendor/bin/psalm --taint-analysis

# Voir les vulnérabilités connues
docker exec -it qwizzy_app ./vendor/bin/security-checker security:check
```

---

**Note** : Ces audits sont **complémentaires** au fichier `SECURITY_AUDIT.md` qui contient l'analyse manuelle détaillée.

**Prochaine action** : Activez les features de sécurité GitHub et lancez votre premier audit !
