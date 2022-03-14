<?php

declare(strict_types=1);

namespace App\Lib\MultipagBradesco\Exception;

class InvalidAccountType extends BradescoMultipagException
{
    public function __construct(string $type)
    {
        parent::__construct("Account type '{$type}' is invalid");
    }
}
