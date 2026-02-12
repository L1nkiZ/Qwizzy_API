<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qwizzy API - Documentation</title>
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            width: 100%;
        }

        .header {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header p {
            font-size: 16px;
            color: #7f8c8d;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            border: 1px solid #e1e8ed;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card:hover {
            border-color: #3498db;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.1);
        }

        .card-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #2c3e50;
        }

        .card-description {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .card-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-rest {
            background: #3498db;
            color: white;
        }

        .badge-soap {
            background: #9b59b6;
            color: white;
        }

        .features {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 30px;
            color: #2c3e50;
        }

        .features h3 {
            font-size: 18px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .feature-item {
            text-align: center;
            padding: 15px;
        }

        .feature-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .feature-text {
            font-size: 13px;
            color: #7f8c8d;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container > * {
            animation: fadeIn 0.6s ease-out;
        }

        .cards-container .card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .cards-container .card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .features {
            animation-delay: 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🙋‍♂️❓ Qwizzy API</h1>
            <p>Choisissez votre type de documentation</p>
        </div>

        <div class="cards-container">
            <a href="/api/documentation" class="card">
                <div class="card-icon">📖</div>
                <h2 class="card-title">REST API</h2>
                <p class="card-description">
                    Documentation Swagger/OpenAPI interactive pour les endpoints REST.
                    Testez l'API directement depuis le navigateur.
                </p>
                <span class="card-badge badge-rest">Swagger UI</span>
            </a>

            <a href="/soap/documentation" class="card">
                <div class="card-icon">🧼</div>
                <h2 class="card-title">SOAP API</h2>
                <p class="card-description">
                    Documentation interactive pour le service SOAP.
                    Interface de test complète pour toutes les méthodes.
                </p>
                <span class="card-badge badge-soap">Interface SOAP</span>
            </a>
        </div>

        <!-- <div class="features">
            <h3>✨ Fonctionnalités disponibles</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">🎯</div>
                    <div class="feature-text">Génération de quiz</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <div class="feature-text">Gestion des scores</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📈</div>
                    <div class="feature-text">Statistiques</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🏆</div>
                    <div class="feature-text">Classements</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">👤</div>
                    <div class="feature-text">Historique utilisateur</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-text">Authentification</div>
                </div>
            </div>
        </div> -->
    </div>
</body>
</html>
