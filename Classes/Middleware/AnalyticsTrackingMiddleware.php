<?php
namespace Aashro\AtAnalyticsLite\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Aashro\AtAnalyticsLite\Service\TrackingService;

class AnalyticsTrackingMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        $tsfe = $GLOBALS['TSFE'] ?? null;
        if ($tsfe !== null && (int)($tsfe->id ?? 0) > 0) {
            $this->trackingService->track($request);
        }

        return $response;
    }
}