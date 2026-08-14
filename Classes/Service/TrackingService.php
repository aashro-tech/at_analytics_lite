<?php
namespace Aashro\AtAnalyticsLite\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;

class TrackingService
{
    protected const SESSION_KEY = 'at_analytics_lite_last_tracked';

    public function __construct(
        protected ConnectionPool $connectionPool
    ) {}

    public function track(ServerRequestInterface $request): void
    {
        $tsfe = $GLOBALS['TSFE'] ?? null;
        if ($tsfe === null || (int)($tsfe->id ?? 0) <= 0) {
            return;
        }

        $pageUid = (int)$tsfe->id;
        if (!$this->shouldTrackPageVisit($pageUid)) {
            return;
        }

        $serverParams = $request->getServerParams();
        $connection = $this->connectionPool->getConnectionForTable('tx_atanalyticslite_visit');

        $connection->insert('tx_atanalyticslite_visit', [
            'page_uid' => $pageUid,
            'language_uid' => (int)($tsfe->sys_language_uid ?? 0),
            'visit_date' => date('Y-m-d'),
            'tstamp' => time(),
            'referrer_domain' => parse_url((string)($serverParams['HTTP_REFERER'] ?? ''), PHP_URL_HOST) ?: 'direct',
            'device_type' => $this->detectDevice((string)($serverParams['HTTP_USER_AGENT'] ?? '')),
            'ip_hash' => sha1((string)($serverParams['REMOTE_ADDR'] ?? '') . 'AT_SECRET_SALT')
        ]);

        $this->markPageVisitTracked($pageUid);
    }

    protected function detectDevice(string $userAgent): string
    {
        $mobileDetect = $this->createMobileDetect($userAgent);
        if ($mobileDetect !== null) {
            if ($mobileDetect->isTablet()) {
                return 'tablet';
            }
            if ($mobileDetect->isMobile()) {
                return 'mobile';
            }
            return 'desktop';
        }

        // Lightweight fallback when MobileDetect is not installed.
        if (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }
        return preg_match('/mobile|android|iphone/i', $userAgent) ? 'mobile' : 'desktop';
    }

    protected function createMobileDetect(string $userAgent): ?object
    {
        if (class_exists(\Detection\MobileDetect::class)) {
            $detector = new \Detection\MobileDetect();
            if (method_exists($detector, 'setUserAgent')) {
                $detector->setUserAgent($userAgent);
            }
            return $detector;
        }

        if (class_exists(\Mobile_Detect::class)) {
            return new \Mobile_Detect(null, $userAgent);
        }

        return null;
    }

    protected function shouldTrackPageVisit(int $pageUid): bool
    {
        if ($pageUid <= 0 || !$this->ensureSessionStarted()) {
            return false;
        }

        $intervalSeconds = $this->getTrackIntervalMinutes() * 60;
        $lastTrackedByPage = $_SESSION[self::SESSION_KEY] ?? [];
        $lastTrackedAt = (int)($lastTrackedByPage[$pageUid] ?? 0);

        return (time() - $lastTrackedAt) >= $intervalSeconds;
    }

    protected function markPageVisitTracked(int $pageUid): void
    {
        if ($pageUid <= 0 || !$this->ensureSessionStarted()) {
            return;
        }
        $_SESSION[self::SESSION_KEY][$pageUid] = time();
    }

    protected function getTrackIntervalMinutes(): int
    {
        $configured = (int) $extConfig['graphApiToken'] ?? 15;
        return max(1, $configured);
    }

    protected function ensureSessionStarted(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        return @session_start();
    }
}