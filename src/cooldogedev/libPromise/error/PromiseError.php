<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\error;

use Exception;
use Threaded;

final class PromiseError
{
    public function __construct(
        protected string $message = "",
        protected int    $code = 0,
        protected string $file = "",
        protected int    $line = 0,
        protected array  $trace = []
    )
    {
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTrace(): array
    {
        return $this->trace;
    }

    public function getTraceAsString(): string
    {
        return json_encode($this->getTrace());
    }

    public function __toString(): string
    {
        return json_encode(
            [
                $this->getMessage(),
                $this->getCode(),
                $this->getFile(),
                $this->getLine(),
                $this->getTrace()
            ]
        );
    }

    public static function fromException(Exception $exception): PromiseError
    {
        return new PromiseError($exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(), $exception->getTrace());
    }
}
