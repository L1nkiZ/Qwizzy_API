<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;

class MetricsController extends Controller
{
    /**
     * Export metrics in Prometheus format
     *
     * @return Response
     */
    public function metrics(): Response
    {
        if (extension_loaded('apcu') && apcu_enabled()) {
            $storage = new APC();
        } else {
            $storage = new InMemory();
        }

        $registry = new CollectorRegistry($storage);

        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());

        return response($result, 200)
            ->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
