<?php

declare(strict_types=1);

namespace Kraz\Command;

use Kraz\Command\Entity\CommandStatus;
use Kraz\Handler\HandlerFactory;
use Kraz\Service\ConnectionsConfiguratorService;
use Kraz\Service\MessageBrokerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListenCommand extends Command
{
    private const ARGUMENT_SOURCE = 'source';
    private const ARGUMENT_CLUSTER = 'cluster';
    private const ARGUMENT_TOPIC = 'topic';
    private const ARGUMENT_HANDLER = 'handler';

    private const OPTION_LIMIT = 'limit';
    private const OPTION_LIMIT_DEFAULT_VALUE = 1000;

    protected static $defaultName = 'kraz:listen';

    private $logger;
    private $configurator;
    private $messageBroker;
    private $handlerFactory;

    use LockableTrait;

    public function __construct(
        LoggerInterface $logger,
        ConnectionsConfiguratorService $configurator,
        MessageBrokerService $messageBroker,
        HandlerFactory $handlerFactory
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->configurator = $configurator;
        $this->messageBroker = $messageBroker;
        $this->handlerFactory = $handlerFactory;
    }

    protected function configure()
    {
        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Source for the clients data (source1, source2, etc...)');
        $this->addArgument(self::ARGUMENT_CLUSTER, InputArgument::REQUIRED, 'Kafka cluster name');
        $this->addArgument(self::ARGUMENT_TOPIC, InputArgument::REQUIRED, 'Kafka topic');
        $this->addArgument(self::ARGUMENT_HANDLER, InputArgument::REQUIRED, 'Handler used for received messages');

        $this->addOption(self::OPTION_LIMIT, 'l', InputOption::VALUE_OPTIONAL, '', self::OPTION_LIMIT_DEFAULT_VALUE);

        $this->addUsage('kraz:listen source1 trunk trunk_record order');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->configurator->init($input->getArgument(self::ARGUMENT_SOURCE));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cluster = $input->getArgument(self::ARGUMENT_CLUSTER);
        $topic = $input->getArgument(self::ARGUMENT_TOPIC);
        $handlerName = $input->getArgument(self::ARGUMENT_HANDLER);

        if (!$this->lock($this->getLockKey($cluster, $topic, $handlerName))) {
            $this->logger->info('The command is already running in another process.');

            return CommandStatus::LOCK_FAILED;
        }

        $limit = $input->getOption(self::OPTION_LIMIT);

        $consumer = $this->messageBroker->getConsumer($cluster);
        $handler = $this->handlerFactory->getHandler($handlerName);
        $consumer->handleMessages($topic, $handler, (int)$limit);

        $this->logger->info("All messages were processed successfully");


        return CommandStatus::OK;
    }

    private function getLockKey(string $cluster, string $topic, string $handlerName): string
    {
        return implode('-', [$cluster, $topic, $handlerName]);
    }
}