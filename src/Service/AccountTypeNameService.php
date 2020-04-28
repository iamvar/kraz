<?php

declare(strict_types=1);

namespace Kraz\Service;

use Kraz\Entity\AccountMode;

class AccountTypeNameService
{
    public static function getAccountTypeForKraz(string $accountTypeName, bool $isDe): string
    {
        $suffix = $isDe ? AccountMode::DE_SUFFIX : AccountMode::LI_SUFFIX;
        return $accountTypeName . $suffix;
    }
}