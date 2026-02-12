<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qwizzy - Documentation SOAP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e1e8ed;
            overflow-y: auto;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 18px;
            background: #34495e;
            color: #ecf0f1;
            border-bottom: 1px solid #2c3e50;
        }

        .sidebar-header h1 {
            font-size: 19px;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .sidebar-header p {
            font-size: 11px;
            opacity: 0.85;
            color: #bdc3c7;
        }

        .btn-home {
            display: block;
            margin-top: 15px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-size: 12px;
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-home:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .top-header {
            position: fixed;
            top: 0;
            right: 0;
            left: 280px;
            height: 60px;
            background: white;
            border-bottom: 1px solid #e1e8ed;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 100;
        }

        .header-title {
            font-size: 18px;
            font-weight: 500;
            color: #2c3e50;
        }

        .header-subtitle {
            font-size: 13px;
            color: #7f8c8d;
            margin-left: 15px;
        }

        .btn-menu {
            padding: 8px 16px;
            background: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            transition: background 0.2s;
        }

        .btn-menu:hover {
            background: #2c3e50;
        }

        .content {
            margin-left: 280px;
            margin-top: 60px;
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .nav-section {
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .nav-section-title {
            padding: 10px 20px;
            font-size: 12px;
            font-weight: 600;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: #f4f6f7;
            border-left-color: #2980b9;
        }

        .nav-item.active {
            background: #e8eaed;
            border-left-color: #2980b9;
            color: #2c3e50;
            font-weight: 500;
        }

        .nav-item-title {
            font-size: 14px;
            margin-bottom: 3px;
        }

        .nav-item-desc {
            font-size: 11px;
            color: #999;
        }

        .content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .method-container {
            background: white;
            border-radius: 8px;
            border: 1px solid #e1e8ed;
            padding: 30px;
            margin-bottom: 20px;
            display: none;
        }

        .method-container.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .method-header {
            border-bottom: 2px solid #2980b9;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }

        .method-title {
            font-size: 23px;
            color: #34495e;
            margin-bottom: 9px;
            font-weight: 500;
        }

        .method-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #3498db;
            color: white;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .method-description {
            color: #666;
            margin-top: 10px;
            line-height: 1.6;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .param-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .param-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .param-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .param-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .param-type {
            display: inline-block;
            padding: 2px 8px;
            background: #e0e0e0;
            border-radius: 4px;
            font-size: 11px;
            font-family: monospace;
            margin-left: 8px;
        }

        .param-required {
            display: inline-block;
            padding: 2px 8px;
            background: #ff6b6b;
            color: white;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 5px;
        }

        .param-optional {
            display: inline-block;
            padding: 2px 8px;
            background: #51cf66;
            color: white;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 5px;
        }

        .param-desc {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }

        .test-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-input-small {
            font-size: 13px;
            color: #999;
            margin-top: 5px;
        }

        .btn-test {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-test:hover {
            background: #2980b9;
        }

        .btn-test:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .response-container {
            margin-top: 25px;
            border-radius: 8px;
            overflow: hidden;
            display: none;
        }

        .response-container.show {
            display: block;
        }

        .response-header {
            padding: 12px 20px;
            font-weight: 600;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .response-header.success {
            background: #51cf66;
        }

        .response-header.error {
            background: #ff6b6b;
        }

        .response-body {
            padding: 20px;
            background: #f8f9fa;
            max-height: 400px;
            overflow-y: auto;
        }

        .response-json {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
        }

        .loading {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            margin-top: 10px;
        }

        .home-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .home-container.active {
            display: block;
        }

        .home-title {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
        }

        .home-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px;
            border-radius: 10px;
            color: white;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div>
            <span class="header-title">Qwizzy - API SOAP</span>
            <span class="header-subtitle">Services web XML</span>
        </div>
        <a href="/" class="btn-menu">← Menu</a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>API SOAP</h1>
            <p>Tests webservices</p>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">📋 Quiz</div>
            <div class="nav-item active" onclick="showSection('generateQuiz')">
                <div class="nav-item-title">GenerateQuiz</div>
                <div class="nav-item-desc">Générer un quiz</div>
            </div>
            <div class="nav-item" onclick="showSection('submitAnswers')">
                <div class="nav-item-title">SubmitQuizAnswers</div>
                <div class="nav-item-desc">Corriger et noter</div>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">📊 Historique</div>
            <div class="nav-item" onclick="showSection('getUserHistory')">
                <div class="nav-item-title">GetUserQuizHistory</div>
                <div class="nav-item-desc">Mes résultats</div>
            </div>
            <div class="nav-item" onclick="showSection('getLeaderboard')">
                <div class="nav-item-title">GetQuizLeaderboard</div>
                <div class="nav-item-desc">Top scores</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- GenerateQuiz -->
        <div id="generateQuiz" class="method-container active">
            <div class="method-header">
                <h2 class="method-title">GenerateQuiz</h2>
                <span class="method-badge">SOAP Method</span>
                <p class="method-description">
                    Génère un quiz aléatoire avec des questions filtrées selon les critères fournis.
                </p>
            </div>

            <div class="section">
                <h3 class="section-title">Paramètres</h3>
                <div class="param-list">
                    <div class="param-item">
                        <div class="param-name">
                            numberOfQuestions
                            <span class="param-type">int</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">Nombre de questions à générer (entre 1 et 100)</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            subjectId
                            <span class="param-type">int</span>
                            <span class="param-optional">optionnel</span>
                        </div>
                        <p class="param-desc">ID du thème pour filtrer les questions</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            difficultyId
                            <span class="param-type">int</span>
                            <span class="param-optional">optionnel</span>
                        </div>
                        <p class="param-desc">ID de la difficulté pour filtrer les questions</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            questionTypeId
                            <span class="param-type">int</span>
                            <span class="param-optional">optionnel</span>
                        </div>
                        <p class="param-desc">ID du type de question pour filtrer</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">🧪 Tester la méthode</h3>
                <div class="test-form">
                    <div class="form-group">
                        <label class="form-label">Nombre de questions *</label>
                        <input type="number" class="form-input" id="gen_numberOfQuestions" placeholder="10" min="1" max="100" value="10">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID du thème (optionnel)</label>
                        <input type="number" class="form-input" id="gen_subjectId" placeholder="Laisser vide pour tous">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID de la difficulté (optionnel)</label>
                        <input type="number" class="form-input" id="gen_difficultyId" placeholder="Laisser vide pour tous">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID du type de question (optionnel)</label>
                        <input type="number" class="form-input" id="gen_questionTypeId" placeholder="Laisser vide pour tous">
                    </div>
                    <button class="btn-test" onclick="testGenerateQuiz()">
                        <span class="btn-text">Tester GenerateQuiz</span>
                    </button>
                </div>
                <div class="response-container" id="gen_response"></div>
            </div>
        </div>

        <!-- GetQuizStatistics -->
        <div id="getStatistics" class="method-container">
            <div class="method-header">
                <h2 class="method-title">GetQuizStatistics</h2>
                <span class="method-badge">SOAP Method</span>
                <p class="method-description">
                    Récupère les statistiques des questions disponibles dans la base de données.
                </p>
            </div>

            <div class="section">
                <h3 class="section-title">Paramètres</h3>
                <div class="param-list">
                    <p style="color: #999; font-style: italic;">Cette méthode ne nécessite aucun paramètre.</p>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">🧪 Tester la méthode</h3>
                <div class="test-form">
                    <button class="btn-test" onclick="testGetStatistics()">
                        <span class="btn-text">Tester GetQuizStatistics</span>
                    </button>
                </div>
                <div class="response-container" id="stats_response"></div>
            </div>
        </div>

        <!-- CreateQuiz -->
        <div id="createQuiz" class="method-container">
            <div class="method-header">
                <h2 class="method-title">CreateQuiz</h2>
                <span class="method-badge">SOAP Method</span>
                <p class="method-description">
                    Crée un nouveau quiz avec des questions spécifiques.
                </p>
            </div>

            <div class="section">
                <h3 class="section-title">Paramètres</h3>
                <div class="param-list">
                    <div class="param-item">
                        <div class="param-name">
                            name
                            <span class="param-type">string</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">Nom du quiz</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            description
                            <span class="param-type">string</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">Description du quiz</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            questionIds
                            <span class="param-type">array</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">Liste des IDs de questions (séparés par des virgules)</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">🧪 Tester la méthode</h3>
                <div class="test-form">
                    <div class="form-group">
                        <label class="form-label">Nom du quiz *</label>
                        <input type="text" class="form-input" id="create_name" placeholder="Mon Super Quiz">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description *</label>
                        <input type="text" class="form-input" id="create_description" placeholder="Description du quiz">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IDs des questions (séparés par des virgules) *</label>
                        <input type="text" class="form-input" id="create_questionIds" placeholder="1,2,3,4,5">
                        <small class="form-input-small">Exemple: 1,2,3,4,5</small>
                    </div>
                    <button class="btn-test" onclick="testCreateQuiz()">
                        <span class="btn-text">Tester CreateQuiz</span>
                    </button>
                </div>
                <div class="response-container" id="create_response"></div>
            </div>
        </div>

        <!-- StartQuiz -->
        <div id="startQuiz" class="method-container">
            <div class="method-header">
                <h2 class="method-title">StartQuiz</h2>
                <span class="method-badge">SOAP Method</span>
                <p class="method-description">
                    Démarre un quiz pour un utilisateur spécifique.
                </p>
            </div>

            <div class="section">
                <h3 class="section-title">Paramètres</h3>
                <div class="param-list">
                    <div class="param-item">
                        <div class="param-name">
                            userId
                            <span class="param-type">int</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">ID de l'utilisateur qui démarre le quiz</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            quizId
                            <span class="param-type">int</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">ID du quiz à démarrer</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">🧪 Tester la méthode</h3>
                <div class="test-form">
                    <div class="form-group">
                        <label class="form-label">ID de l'utilisateur *</label>
                        <input type="number" class="form-input" id="start_userId" placeholder="1" value="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID du quiz *</label>
                        <input type="number" class="form-input" id="start_quizId" placeholder="1">
                    </div>
                    <button class="btn-test" onclick="testStartQuiz()">
                        <span class="btn-text">Tester StartQuiz</span>
                    </button>
                </div>
                <div class="response-container" id="start_response"></div>
            </div>
        </div>

        <!-- SubmitQuizAnswers -->
        <div id="submitAnswers" class="method-container">
            <div class="method-header">
                <h2 class="method-title">SubmitQuizAnswers</h2>
                <span class="method-badge">SOAP Method</span>
                <p class="method-description">
                    Soumet les réponses d'un quiz et calcule le score.
                </p>
            </div>

            <div class="section">
                <h3 class="section-title">Paramètres</h3>
                <div class="param-list">
                    <div class="param-item">
                        <div class="param-name">
                            userQuizId
                            <span class="param-type">int</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">ID de l'entrée UserQuiz (retourné par StartQuiz)</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            answers
                            <span class="param-type">object</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">Objet JSON des réponses {questionId: answerId}</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">🧪 Tester la méthode</h3>
                <div class="test-form">
                    <div class="form-group">
                        <label class="form-label">ID UserQuiz *</label>
                        <input type="number" class="form-input" id="submit_userQuizId" placeholder="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Réponses (JSON) *</label>
                        <input type="text" class="form-input" id="submit_answers" placeholder='{"1": 5, "2": 8}'>
                        <small class="form-input-small">Format: {"questionId": answerId, ...}</small>
                    </div>
                    <button class="btn-test" onclick="testSubmitAnswers()">
                        <span class="btn-text">Tester SubmitQuizAnswers</span>
                    </button>
                </div>
                <div class="response-container" id="submit_response"></div>
            </div>
        </div>

        <!-- GetUserQuizHistory -->
        <div id="getUserHistory" class="method-container">
            <div class="method-header">
                <h2 class="method-title">GetUserQuizHistory</h2>
                <span class="method-badge">SOAP Method</span>
                <p class="method-description">
                    Récupère l'historique complet des quiz d'un utilisateur.
                </p>
            </div>

            <div class="section">
                <h3 class="section-title">Paramètres</h3>
                <div class="param-list">
                    <div class="param-item">
                        <div class="param-name">
                            userId
                            <span class="param-type">int</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">ID de l'utilisateur</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">🧪 Tester la méthode</h3>
                <div class="test-form">
                    <div class="form-group">
                        <label class="form-label">ID de l'utilisateur *</label>
                        <input type="number" class="form-input" id="history_userId" placeholder="1" value="1">
                    </div>
                    <button class="btn-test" onclick="testGetUserHistory()">
                        <span class="btn-text">Tester GetUserQuizHistory</span>
                    </button>
                </div>
                <div class="response-container" id="history_response"></div>
            </div>
        </div>

        <!-- GetQuizLeaderboard -->
        <div id="getLeaderboard" class="method-container">
            <div class="method-header">
                <h2 class="method-title">GetQuizLeaderboard</h2>
                <span class="method-badge">SOAP Method</span>
                <p class="method-description">
                    Récupère le classement des meilleurs scores pour un quiz.
                </p>
            </div>

            <div class="section">
                <h3 class="section-title">Paramètres</h3>
                <div class="param-list">
                    <div class="param-item">
                        <div class="param-name">
                            quizId
                            <span class="param-type">int</span>
                            <span class="param-required">requis</span>
                        </div>
                        <p class="param-desc">ID du quiz</p>
                    </div>
                    <div class="param-item">
                        <div class="param-name">
                            limit
                            <span class="param-type">int</span>
                            <span class="param-optional">optionnel</span>
                        </div>
                        <p class="param-desc">Nombre de résultats (défaut: 10, max: 100)</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">🧪 Tester la méthode</h3>
                <div class="test-form">
                    <div class="form-group">
                        <label class="form-label">ID du quiz *</label>
                        <input type="number" class="form-input" id="leader_quizId" placeholder="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Limite (optionnel)</label>
                        <input type="number" class="form-input" id="leader_limit" placeholder="10" value="10">
                    </div>
                    <button class="btn-test" onclick="testGetLeaderboard()">
                        <span class="btn-text">Tester GetQuizLeaderboard</span>
                    </button>
                </div>
                <div class="response-container" id="leader_response"></div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.method-container').forEach(el => {
                el.classList.remove('active');
            });

            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(el => {
                el.classList.remove('active');
            });

            // Show selected section
            document.getElementById(sectionId).classList.add('active');

            // Add active class to clicked nav item
            event.target.closest('.nav-item').classList.add('active');
        }

        function showResponse(containerId, data, isError = false) {
            const container = document.getElementById(containerId);

            let xmlContent = '';
            let jsonContent = '';

            if (data.xml) {
                // Afficher le XML formaté
                xmlContent = `
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">📄 Réponse XML SOAP :</h4>
                        <pre style="background: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; overflow-x: auto; font-size: 12px;">${escapeHtml(data.xml)}</pre>
                    </div>
                `;
                jsonContent = `
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">📊 Données parsées :</h4>
                        <pre class="response-json">${JSON.stringify(data.data, null, 2)}</pre>
                    </div>
                `;
            } else {
                jsonContent = `<pre class="response-json">${JSON.stringify(data, null, 2)}</pre>`;
            }

            container.innerHTML = `
                <div class="response-header ${isError ? 'error' : 'success'}">
                    <span>${isError ? '❌ Erreur' : '✅ Réponse SOAP'}</span>
                </div>
                <div class="response-body">
                    ${xmlContent}
                    ${jsonContent}
                </div>
            `;
            container.classList.add('show');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function setLoading(btnElement, isLoading) {
            if (!btnElement) return;

            const textSpan = btnElement.querySelector('.btn-text');
            if (isLoading) {
                btnElement.disabled = true;
                if (textSpan) {
                    textSpan.innerHTML = '<span class="loading"></span> Chargement...';
                } else {
                    btnElement.innerHTML = '<span class="loading"></span> Chargement...';
                }
            } else {
                btnElement.disabled = false;
            }
        }

        async function callSoapMethod(method, params) {
            const response = await fetch('/api/soap/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    method: method,
                    params: params
                })
            });
            return response.json();
        }

        async function testGenerateQuiz() {
            const btn = event.target;
            setLoading(btn, true);

            try {
                const params = {
                    numberOfQuestions: parseInt(document.getElementById('gen_numberOfQuestions').value),
                    subjectId: document.getElementById('gen_subjectId').value || null,
                    difficultyId: document.getElementById('gen_difficultyId').value || null,
                    questionTypeId: document.getElementById('gen_questionTypeId').value || null
                };

                const result = await callSoapMethod('GenerateQuiz', params);
                const hasError = result.error || (result.data && result.data.error);
                showResponse('gen_response', result, hasError);
            } catch (error) {
                showResponse('gen_response', {error: error.message}, true);
            } finally {
                setLoading(btn, false);
                const textSpan = btn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.textContent = 'Tester GenerateQuiz';
                } else {
                    btn.textContent = 'Tester GenerateQuiz';
                }
            }
        }

        async function testGetStatistics() {
            const btn = event.target;
            setLoading(btn, true);

            try {
                const result = await callSoapMethod('GetQuizStatistics', {});
                showResponse('stats_response', result, !result.success);
            } catch (error) {
                showResponse('stats_response', {error: error.message}, true);
            } finally {
                setLoading(btn, false);
                const textSpan = btn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.textContent = 'Tester GetQuizStatistics';
                } else {
                    btn.textContent = 'Tester GetQuizStatistics';
                }
            }
        }

        async function testCreateQuiz() {
            const btn = event.target;
            setLoading(btn, true);

            try {
                const questionIdsStr = document.getElementById('create_questionIds').value;
                const questionIds = questionIdsStr.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));

                const params = {
                    name: document.getElementById('create_name').value,
                    description: document.getElementById('create_description').value,
                    questionIds: questionIds
                };

                const result = await callSoapMethod('CreateQuiz', params);
                showResponse('create_response', result, !result.success);
            } catch (error) {
                showResponse('create_response', {error: error.message}, true);
            } finally {
                setLoading(btn, false);
                const textSpan = btn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.textContent = 'Tester CreateQuiz';
                } else {
                    btn.textContent = 'Tester CreateQuiz';
                }
            }
        }

        async function testStartQuiz() {
            const btn = event.target;
            setLoading(btn, true);

            try {
                const params = {
                    userId: parseInt(document.getElementById('start_userId').value),
                    quizId: parseInt(document.getElementById('start_quizId').value)
                };

                const result = await callSoapMethod('StartQuiz', params);
                showResponse('start_response', result, !result.success);
            } catch (error) {
                showResponse('start_response', {error: error.message}, true);
            } finally {
                setLoading(btn, false);
                const textSpan = btn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.textContent = 'Tester StartQuiz';
                } else {
                    btn.textContent = 'Tester StartQuiz';
                }
            }
        }

        async function testSubmitAnswers() {
            const btn = event.target;
            setLoading(btn, true);

            try {
                const answersStr = document.getElementById('submit_answers').value;
                const answers = JSON.parse(answersStr);

                const params = {
                    userQuizId: parseInt(document.getElementById('submit_userQuizId').value),
                    answers: answers
                };

                const result = await callSoapMethod('SubmitQuizAnswers', params);
                showResponse('submit_response', result, !result.success);
            } catch (error) {
                showResponse('submit_response', {error: error.message}, true);
            } finally {
                setLoading(btn, false);
                const textSpan = btn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.textContent = 'Tester SubmitQuizAnswers';
                } else {
                    btn.textContent = 'Tester SubmitQuizAnswers';
                }
            }
        }

        async function testGetUserHistory() {
            const btn = event.target;
            setLoading(btn, true);

            try {
                const params = {
                    userId: parseInt(document.getElementById('history_userId').value)
                };

                const result = await callSoapMethod('GetUserQuizHistory', params);
                showResponse('history_response', result, !result.success);
            } catch (error) {
                showResponse('history_response', {error: error.message}, true);
            } finally {
                setLoading(btn, false);
                const textSpan = btn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.textContent = 'Tester GetUserQuizHistory';
                } else {
                    btn.textContent = 'Tester GetUserQuizHistory';
                }
            }
        }

        async function testGetLeaderboard() {
            const btn = event.target;
            setLoading(btn, true);

            try {
                const params = {
                    quizId: parseInt(document.getElementById('leader_quizId').value),
                    limit: parseInt(document.getElementById('leader_limit').value) || 10
                };

                const result = await callSoapMethod('GetQuizLeaderboard', params);
                showResponse('leader_response', result, !result.success);
            } catch (error) {
                showResponse('leader_response', {error: error.message}, true);
            } finally {
                setLoading(btn, false);
                const textSpan = btn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.textContent = 'Tester GetQuizLeaderboard';
                } else {
                    btn.textContent = 'Tester GetQuizLeaderboard';
                }
            }
        }
    </script>
</body>
</html>
