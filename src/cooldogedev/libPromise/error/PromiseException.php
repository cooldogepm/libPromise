<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\error;

use Exception;

final class PromiseException extends Exception
{
    public function __construct(
        string $message = "",
        int    $code = 0,
        string $file = "",
        int    $line = 0
    )
    {
        parent::__construct($message, $code);
        $this->file = $file;
        $this->line = $line;
    }
}
