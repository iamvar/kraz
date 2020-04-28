<?php
declare(strict_types=1);

namespace Kraz\Service\Monitoring;


use Curl\Curl;

class IcingaMonitoring implements MonitoringInterface
{
    private $url;
    private $login;
    private $password;
    private $hostName;

    public function __construct(string $url, string $login, string $password)
    {
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
        $this->hostName = gethostname();
    }

    public function info(string $message, array $performance = []): MonitoringResponse
    {
        return $this->send(0, 'Kraz-aggregator', $message, $performance);
    }

    public function alert(string $message, array $performance = []): MonitoringResponse
    {
        return $this->send(2, 'Kraz-aggregator Fatal', $message, $performance);
    }

    private function send(int $statusCode, string $service, string $message, array $performance = []): MonitoringResponse
    {
        $performance = $this->convertPerformance($performance);

        $curl = new Curl($this->url);
        $curl->setBasicAuthentication($this->login, $this->password);
        $curl->setHeader('Accept', 'application/json');
        $curl->setHeader('Content-Type', 'application/json');
        $url = sprintf('?service=%s!%s', $this->hostName, $service);
        $curl->post($url, [
            'exit_status' => $statusCode,
            'plugin_output' => $message,
            'performance_data' => $performance,
        ]);

        return new MonitoringResponse($curl->errorMessage);
    }

    private function convertPerformance(array $data): array
    {
        $result = [];

        foreach ($data as $key => $val) {
            $result[] = sprintf('%s=%s', $key, $val);
        }

        return $result;
    }
}
