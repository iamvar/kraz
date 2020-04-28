<?php
declare(strict_types=1);

namespace Kraz\EventListener;

use Kraz\Command\Entity\CommandStatus;
use Kraz\Service\Monitoring\MonitoringInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class CommandListener
{
    private $logger;
    private $monitoring;

    private $start;

    public function __construct(LoggerInterface $logger, MonitoringInterface $monitoring)
    {
        $this->logger = $logger;
        $this->monitoring = $monitoring;
        $this->start = microtime(true);
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getInput()->getFirstArgument();
        if (!$command) {
            return;
        }

        $this->logger->info(
            sprintf('Command `%s` is running', (string)$event->getInput()),
            ['time' => $this->getExecutionTime()]
        );
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $command = $event->getInput()->getFirstArgument();
        if (!$command) {
            return;
        }

        $ts = $this->getExecutionTime();

        $message = sprintf(
            'Command `%s` exited with code "%d"',
            (string)$event->getInput(),
            $event->getExitCode()
        );

        if ($event->getExitCode() === 0 || $event->getExitCode() === CommandStatus::LOCK_FAILED) {
            $this->logger->info($message, ['time' => $ts]);

            $monitoringResponse = $this->monitoring->info(
                $message,
                [
                    'time' => $ts,
                ]
            );

            if ($monitoringResponse->hasError()) {
                $this->logMonitoringDoesNotResponse($monitoringResponse->getError());
            }
        } else {
            $this->logger->error($message, ['time' => $ts]);
        }
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $command = $event->getInput()->getFirstArgument();
        if (!$command) {
            return;
        }

        $monitoringResponse = $this->monitoring->alert(
            sprintf('`%s`: Error: %s', (string)$event->getInput(), $event->getError()->getMessage())
        );

        if ($monitoringResponse->hasError()) {
            $this->logMonitoringDoesNotResponse($monitoringResponse->getError());
        }
    }

    private function logMonitoringDoesNotResponse(string $error)
    {
        $this->logger->error('Failed sending alert to monitoring service. Error: ' . $error);
    }

    protected function getExecutionTime(): int
    {
        return (int)(microtime(true) - $this->start);
    }
}
