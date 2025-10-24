<?php

namespace fortytwostudio\passwordprotection\variables;

use fortytwostudio\passwordprotection\PasswordProtection;

class PasswordVariables
{
    public function getPassword(int $entryId)
    {
        $password = PasswordProtection::getInstance()->passwordEntry->get($entryId);

        return $password;
    }

    public function protect(?string $password = null, ?string $key = null)
    {
        PasswordProtection::getInstance()->passwordService->protect($password, $key);
    }

    public function stillProtected($entryId)
    {
        $password = PasswordProtection::getInstance()->passwordEntry->get($entryId);

        $protected = !empty($password) && $password !== '';

        return $protected;
    }
}
