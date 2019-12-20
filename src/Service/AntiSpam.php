<?php


namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AntiSpam
{
    private $forbiddenStrings;
    private $logger;
    private $requestStack;

    public function __construct(array $forbiddenStrings, LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->forbiddenStrings = $forbiddenStrings;
        $this->logger=$logger;
        $this->requestStack=$requestStack;
    }

    public function isSpam(string $mess): bool
    {
        $isSpam = false;

        foreach ($this->forbiddenStrings as $string) {
            if (strstr($mess, $string)){
                $isSpam = true;
                $this->logger->info($mess . $this->requestStack->getCurrentRequest()->getClientIp());
            }
        }
        return $isSpam;
    }
}