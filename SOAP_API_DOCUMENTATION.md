# 🧼 API SOAP - Service de Quiz Complet

## 📋 Vue d'ensemble

Le contrôleur SOAP `QuizSoapController` expose **7 méthodes** pour gérer complètement les quiz et les scores.

**Mode**: Non-WSDL (pas besoin de fichier WSDL)
**Endpoint**: `http://localhost:8000/soap/quiz`
**URI**: `http://localhost:8000/soap/quiz`

---

GenerateQuiz
GetQuizStatistics
CreateQuiz
StartQuiz
SubmitQuizAnswers
GetUserQuizHistory
GetQuizLeaderboard

## 🔧 Méthodes disponibles

### 1. GenerateQuiz
Génère un quiz aléatoire avec des questions filtrées.

**Paramètres**:
- `numberOfQuestions` (int, requis): Nombre de questions (1-100)
- `subjectId` (int, optionnel): Filtrer par thème
- `difficultyId` (int, optionnel): Filtrer par difficulté
- `questionTypeId` (int, optionnel): Filtrer par type

**Retour**:
```php
[
    'success' => true,
    'message' => 'Quiz généré avec succès',
    'quiz' => [
        'metadata' => [...],
        'questions' => [...]
    ]
]
```

**Exemple**:
```php
$quiz = $client->GenerateQuiz(10, 1, 2, null);
```

---

### 2. GetQuizStatistics
Récupère les statistiques des questions disponibles.

**Paramètres**: Aucun

**Retour**:
```php
[
    'success' => true,
    'statistics' => [
        'total_questions' => 150,
        'by_subject' => [...],
        'by_difficulty' => [...]
    ]
]
```

**Exemple**:
```php
$stats = $client->GetQuizStatistics();
```

---

### 3. CreateQuiz
Crée un quiz avec des questions spécifiques.

**Paramètres**:
- `name` (string, requis): Nom du quiz
- `description` (string, requis): Description
- `questionIds` (array, requis): IDs des questions à inclure

**Retour**:
```php
[
    'success' => true,
    'message' => 'Quiz créé avec succès',
    'quiz' => [
        'id' => 42,
        'name' => 'Mon Quiz',
        'description' => '...',
        'questions_count' => 10,
        'created_at' => '2025-11-28T10:30:00Z'
    ]
]
```

**Exemple**:
```php
$quiz = $client->CreateQuiz(
    "Quiz de Géographie",
    "10 questions sur les capitales",
    [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
);
```

---

### 4. StartQuiz
Démarre un quiz pour un utilisateur.

**Paramètres**:
- `userId` (int, requis): ID de l'utilisateur
- `quizId` (int, requis): ID du quiz à démarrer

**Retour**:
```php
[
    'success' => true,
    'message' => 'Quiz démarré avec succès',
    'user_quiz_id' => 15,
    'quiz' => [
        'id' => 42,
        'name' => 'Mon Quiz',
        'description' => '...',
        'max_score' => 100,
        'questions' => [...] // Sans les réponses correctes
    ]
]
```

**Exemple**:
```php
$started = $client->StartQuiz(1, 42);
$userQuizId = $started->user_quiz_id;
```

---

### 5. SubmitQuizAnswers
Soumet les réponses et calcule le score.

**Paramètres**:
- `userQuizId` (int, requis): ID de l'entrée UserQuiz
- `answers` (array, requis): Tableau associatif [questionId => answerId]

**Retour**:
```php
[
    'success' => true,
    'message' => 'Quiz complété avec succès',
    'results' => [
        'user_quiz_id' => 15,
        'score' => 80,
        'max_score' => 100,
        'percentage' => 80.0,
        'correct_answers' => 8,
        'total_questions' => 10,
        'completed_at' => '2025-11-28T11:00:00Z',
        'details' => [...]
    ]
]
```

**Exemple**:
```php
$answers = [
    1 => 5,  // Question 1 -> Réponse 5
    2 => 8,  // Question 2 -> Réponse 8
    3 => 12, // Question 3 -> Réponse 12
    // ...
];
$results = $client->SubmitQuizAnswers($userQuizId, $answers);
```

---

### 6. GetUserQuizHistory
Récupère l'historique des quiz d'un utilisateur.

**Paramètres**:
- `userId` (int, requis): ID de l'utilisateur

**Retour**:
```php
[
    'success' => true,
    'user_id' => 1,
    'total_quizzes' => 25,
    'history' => [
        [
            'user_quiz_id' => 15,
            'quiz_id' => 42,
            'quiz_name' => 'Quiz de Géographie',
            'score' => 80,
            'max_score' => 100,
            'percentage' => 80.0,
            'status' => 'completed',
            'started_at' => '...',
            'completed_at' => '...'
        ],
        // ...
    ]
]
```

**Exemple**:
```php
$history = $client->GetUserQuizHistory(1);
```

---

### 7. GetQuizLeaderboard
Récupère le classement des meilleurs scores pour un quiz.

**Paramètres**:
- `quizId` (int, requis): ID du quiz
- `limit` (int, optionnel): Nombre de résultats (défaut: 10, max: 100)

**Retour**:
```php
[
    'success' => true,
    'quiz_id' => 42,
    'quiz_name' => 'Quiz de Géographie',
    'leaderboard' => [
        [
            'rank' => 1,
            'user_id' => 5,
            'user_name' => 'Alice',
            'score' => 100,
            'max_score' => 100,
            'percentage' => 100.0,
            'completed_at' => '...'
        ],
        [
            'rank' => 2,
            'user_id' => 3,
            'user_name' => 'Bob',
            'score' => 95,
            'max_score' => 100,
            'percentage' => 95.0,
            'completed_at' => '...'
        ],
        // ...
    ]
]
```

**Exemple**:
```php
$leaderboard = $client->GetQuizLeaderboard(42, 20);
```

---

## 🚀 Exemple de workflow complet

```php
<?php
// 1. Se connecter au service SOAP
$client = new SoapClient(null, [
    'location' => 'http://localhost:8000/soap/quiz',
    'uri' => 'http://localhost:8000/soap/quiz',
    'trace' => 1,
    'exceptions' => true
]);

// 2. Générer un quiz aléatoire
$generated = $client->GenerateQuiz(10, 1, null, null);
$questionIds = array_map(fn($q) => $q->id, $generated->quiz->questions);

// 3. Créer un quiz permanent
$quiz = $client->CreateQuiz(
    "Mon Super Quiz",
    "Un quiz de test",
    $questionIds
);
$quizId = $quiz->quiz->id;

// 4. Démarrer le quiz pour un utilisateur
$started = $client->StartQuiz(1, $quizId);
$userQuizId = $started->user_quiz_id;

// 5. L'utilisateur répond aux questions
$answers = [
    1 => 5,
    2 => 8,
    // ... (une réponse par question)
];

// 6. Soumettre les réponses
$results = $client->SubmitQuizAnswers($userQuizId, $answers);
echo "Score: {$results->results->score}/{$results->results->max_score}\n";

// 7. Voir l'historique
$history = $client->GetUserQuizHistory(1);

// 8. Voir le classement
$leaderboard = $client->GetQuizLeaderboard($quizId, 10);
```

---

## 🧪 Tester avec Docker

```bash
# Démarrer les services
docker-compose up -d

# Tester avec l'exemple simple
docker-compose exec app php soap_client_example.php

# Tester avec l'exemple complet (toutes les fonctionnalités)
docker-compose exec app php soap_client_complete_example.php
```

---

## 📊 Structure des données

### Quiz
- `id`: ID du quiz
- `name`: Nom du quiz
- `description`: Description
- `questions`: Collection de questions

### Question
- `id`: ID de la question
- `question`: Texte de la question
- `subject`: Nom du thème
- `difficulty`: Nom de la difficulté
- `question_type`: Type de question
- `proposal1`, `proposal2`, `proposal3`, `proposal4`: Propositions
- `answers`: Réponses possibles

### UserQuiz
- `id`: ID de l'entrée
- `user_id`: ID de l'utilisateur
- `quiz_id`: ID du quiz
- `score`: Score obtenu
- `max_score`: Score maximum
- `status`: 'in_progress' ou 'completed'
- `started_at`: Date de début
- `completed_at`: Date de fin

---

## ⚠️ Gestion des erreurs

Toutes les méthodes peuvent lancer une `SoapFault` en cas d'erreur :

```php
try {
    $result = $client->StartQuiz(999, 42);
} catch (SoapFault $e) {
    echo "Erreur: " . $e->getMessage();
    // Ex: "L'utilisateur avec l'ID 999 n'existe pas"
}
```

Types d'erreurs:
- **Client**: Erreur de validation (paramètres invalides)
- **Server**: Erreur interne du serveur

---

## 💡 Notes importantes

1. **Scores**: Basés sur les points de difficulté (table `difficulty.point`)
2. **UserQuiz**: Une entrée par tentative de quiz
3. **Status**:
   - `in_progress`: Quiz démarré mais non terminé
   - `completed`: Quiz terminé
4. **Classement**: Trié par score décroissant, puis par date (plus rapide = mieux)

---

## 🔗 Fichiers liés

- Contrôleur: `app/Http/Controllers/QuizSoapController.php`
- Modèles: `app/Models/Quiz.php`, `QuizQuestion.php`, `UserQuiz.php`
- Routes: `routes/web.php`
- Client exemple: `soap_client_complete_example.php`
