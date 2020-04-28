<?php

namespace Kraz\Processor;

use Kraz\Entity\CT\LeaderAccount;
use Kraz\Entity\Dictionary;
use Kraz\Message\OrderPlacedMessage;
use Kraz\Repository\CT\FollowerCachingRepository;
use Kraz\Repository\CT\LeaderCachingRepository;
use Kraz\Repository\My\AllAccountCachingRepository;
use Kraz\Service\KrazApiManager;
use Kraz\Service\DeduplicationService;
use Kraz\Service\FormatHelper;
use Kraz\Service\AccountTypeNameService;

class OrderPlacedProcessor
{
    // We are keep in memory all tracked orders during the one week.
    private const DEDUPLICATION_TIMEOUT = 604800;

    private $deduplicationService;
    private $apiClient;
    private $allAccountRepository;
    private $followerRepository;
    private $leaderRepository;

    /**
     * @TODO Replace kraz api manager to events consumer
     *
     * @param DeduplicationService $deduplicationService
     * @param KrazApiManager $apiClient
     * @param AllAccountCachingRepository $allAccountRepository
     * @param FollowerCachingRepository $followerRepository
     * @param LeaderCachingRepository $leaderRepository
     */
    public function __construct(
        DeduplicationService $deduplicationService,
        KrazApiManager $apiClient,
        AllAccountCachingRepository $allAccountRepository,
        FollowerCachingRepository $followerRepository,
        LeaderCachingRepository $leaderRepository
    ) {
        $this->deduplicationService = $deduplicationService;
        $this->apiClient = $apiClient;
        $this->allAccountRepository = $allAccountRepository;
        $this->followerRepository = $followerRepository;
        $this->leaderRepository = $leaderRepository;
    }

    public function process(OrderPlacedMessage $message): void
    {
        if ($this->deduplicationService->isProcessed($message)) {
            return;
        }

        $event = $this->createEvent($message);
        if (!$event) {
            return;
        }

        $this->apiClient->sendEvents([$event]);

        // We remember processed order and will not push it twice
        $this->deduplicationService->markAsProcessed($message, self::DEDUPLICATION_TIMEOUT);
    }

    private function createEvent(OrderPlacedMessage $message): ?array
    {
        $account = $this->allAccountRepository->getAccountByLogin($message->getLogin());
        if (!$account) {
            return null;
        }

        $follower = $this->followerRepository->getAccountByLogin($message->getLogin());
        if ($follower) {
            // We have to skip orders placed by ct. And we don't have a info to filter them before.
            return null;
        }

        $leader = $this->leaderRepository->getAccountByLogin($message->getLogin());

        $eventName = $this->getEvent($account, $leader);

        return [
            'external_id' => $account['client_id'],
            'name' => $eventName,
            'time' => FormatHelper::getIsoDateFromTimestamp($message->getTimestamp()),
            'properties' => [
                'account_type' => AccountTypeNameService::getAccountTypeForKraz($account['account_type_display_name'], $account['is_de']),
                'account_login' => $message->getLogin(),
                'account_leverage' => $account['leverage'] ?? 0,
                'is_pending' => $message->isPending(),
                'volume' => $message->getVolume(),
                'instrument' => $message->getSymbol(),
            ]
        ];
    }

    private function getEvent(?array $account, ?LeaderAccount $leaderAccount): string
    {
        if ($account['is_de']) {
            return Dictionary::EVENT_DE_ORDER_PLACED;
        }

        if ($leaderAccount && $leaderAccount->getInvestorSetting() === LeaderAccount::INVESTOR_SETTING_PUBLIC) {
            return Dictionary::EVENT_MA_ORDER_PLACED;
        }

        return Dictionary::EVENT_LI_ORDER_PLACED;
    }
}