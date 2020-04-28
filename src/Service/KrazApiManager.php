<?php
declare(strict_types=1);

namespace Kraz\Service;

use Curl\Curl;
use Psr\Log\LoggerInterface;
use Throwable;

class KrazApiManager
{
    private $curl;
    private $apiKey;
    private $baseUrl;
    private $logger;

    public function __construct(string $baseUrl, string $apiKey, LoggerInterface $logger)
    {
        $baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->curl = new Curl($baseUrl);
        $this->curl->setHeader('Content-Type', 'application/json');
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        $this->logger = $logger;
    }

    public function sendEvents(array $data): bool
    {
        if (empty($data)) {
            return true;
        }

        return $this->updateUserData([
            'events' => $data,
        ]);
    }

    private function updateUserData(array $data): bool
    {
        return $this->send('/users/track', $data);
    }

    private function send(string $endpoint, $data): bool
    {
        $url = $this->getUrl($endpoint);
        $this->logger->info('Sending request', ['url' => $url, 'data' => $data]);
        $data['api_key'] = $this->apiKey; // apikey not to pass in graylog
        try {
            $response = $this->curl->post($url, $data);
            if ($response->message === 'success') {
                $this->logger->info('Success response', ['response' => $response]);
                return true;
            }

            if ($response->message !== 'success') {
                $this->logger->error('Error sending message', ['response' => $response]);
            }
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

    private function getUrl(string $endpoint): string
    {
        if ($endpoint && '/' !== $endpoint[0]) {
            $endpoint = '/' . $endpoint;
        }

        return $this->baseUrl . $endpoint;
    }
}