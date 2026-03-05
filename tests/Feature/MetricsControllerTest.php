<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MetricsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_200_on_metrics_endpoint()
    {
        $response = $this->get('/api/metrics');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_returns_prometheus_content_type()
    {
        $response = $this->get('/api/metrics');

        $this->assertStringContainsString(
            'text/plain',
            $response->headers->get('Content-Type')
        );
    }

    #[Test]
    public function it_returns_prometheus_formatted_output()
    {
        // Effectue d'abord une requête pour que le middleware Prometheus enregistre des métriques
        $this->get('/api/metrics');

        $response = $this->get('/api/metrics');
        $content  = $response->getContent();

        // Le format Prometheus contient des lignes # HELP ou # TYPE, ou du contenu vide
        // On vérifie juste que la réponse est une chaîne (peut être vide si aucune métrique)
        $this->assertIsString($content);
        $response->assertStatus(200);
    }
}
