<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;
use Symfony\Component\HttpFoundation\Response;

class PrometheusMiddleware
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        // Use InMemory storage for testing environment to avoid APCu dependency
        $storage = app()->environment('testing') ? new InMemory() : new APC();
        $this->registry = new CollectorRegistry($storage);
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        // Process the request
        $response = $next($request);

        // Calculate request duration
        $duration = microtime(true) - $start;

        // Get request details
        $method = $request->method();
        $route = $request->route() ? $request->route()->uri() : 'unknown';
        $statusCode = $response->getStatusCode();

        // Track HTTP request count
        $counter = $this->registry->getOrRegisterCounter(
            'qwizzy',
            'http_requests_total',
            'Total number of HTTP requests',
            ['method', 'route', 'status']
        );
        $counter->inc([$method, $route, $statusCode]);

        // Track HTTP request duration
        $histogram = $this->registry->getOrRegisterHistogram(
            'qwizzy',
            'http_request_duration_seconds',
            'HTTP request duration in seconds',
            ['method', 'route', 'status'],
            [0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10]
        );
        $histogram->observe($duration, [$method, $route, $statusCode]);

        // Track response size
        $gauge = $this->registry->getOrRegisterGauge(
            'qwizzy',
            'http_response_size_bytes',
            'HTTP response size in bytes',
            ['method', 'route', 'status']
        );
        $contentLength = $response->headers->get('Content-Length', strlen($response->getContent()));
        $gauge->set($contentLength, [$method, $route, $statusCode]);

        return $response;
    }
}
