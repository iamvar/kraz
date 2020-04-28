<?php

declare(strict_types=1);

namespace Kraz\Entity\CT;

class LeaderAccount
{
    public const INVESTOR_SETTING_PUBLIC = 'Public';
    public const INVESTOR_SETTING_PRIVATE = 'Private';
    public const INVESTOR_SETTING_SWITCHED_OFF = 'Switched off';
    public const INVESTOR_SETTING_UNKNOWN = 'Unknown';

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $investorSetting;

    public function __construct(string $login, bool $isPublic, bool $isFollowable)
    {
        $this->login = $login;
        $this->investorSetting = $this->mapInvestorSetting($isPublic, $isFollowable);
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getInvestorSetting(): string
    {
        return $this->investorSetting;
    }

    private function mapInvestorSetting(bool $isPublic, bool $isFollowable): string
    {
        if ($isPublic && $isFollowable) {
            return self::INVESTOR_SETTING_PUBLIC;
        }

        if (!$isPublic && $isFollowable) {
            return self::INVESTOR_SETTING_PRIVATE;
        }

        if (!$isPublic && !$isFollowable) {
            return self::INVESTOR_SETTING_SWITCHED_OFF;
        }

        return self::INVESTOR_SETTING_UNKNOWN;
    }
}