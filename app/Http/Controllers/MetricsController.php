<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\APC;

class MetricsController extends Controller
{
    /**
     * Export metrics in Prometheus format
     *
     * @return Response
     */
    public function metrics(): Response
    {
        $registry = new CollectorRegistry(new APC());

        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());

        return response($result, 200)
            ->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
