<?php

/**
 *  Copyright (c) 2021 cooldogedev
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 */

declare(strict_types=1);

namespace cooldogedev\libPromise\error;

use Exception;
use Serializable;
use Threaded;

final class PromiseError implements Serializable
{
    protected Threaded $trace;

    public function __construct(
        protected string $message = "",
        protected int    $code = 0,
        protected string $file = "",
        protected int    $line = 0,
    )
    {
        $this->trace = new Threaded();
    }

    public static function fromException(Exception $exception): PromiseError
    {
        return new PromiseError($exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(), $exception->getTrace());
    }

    public function getTraceAsString(): string
    {
        return json_encode($this->getTrace());
    }

    public function getTrace(): Threaded
    {
        return $this->trace;
    }

    public function serialize(): string
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

    public function getMessage(): string
    {
        return $this->message;
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

    public function unserialize($data): PromiseError
    {
        return new PromiseError(... json_decode($data, true));
    }
}
