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

use cooldogedev\libPromise\thread\ThreadedPromise;
use Stringable;
use Threaded;
use Throwable;
use Volatile;

/**
 * Replacement for exceptions, used in @see ThreadedPromise.
 */
final class ThreadedPromiseError extends Threaded implements Stringable
{
    protected string $message = "";
    protected int $code = 0;
    protected string $file = "";
    protected int $line = 0;
    protected Volatile $trace;

    public function __construct()
    {
        $this->trace = new Volatile();
    }

    public static function fromThrowable(Throwable $throwable): ThreadedPromiseError
    {
        $error = new ThreadedPromiseError();
        $error->updateParameters($throwable);
        return $error;
    }

    public function updateParameters(Throwable $throwable): void
    {
        $this->setMessage($throwable->getMessage());
        $this->setCode($throwable->getCode());
        $this->setFile($throwable->getFile());
        $this->setLine($throwable->getLine());
        $this->setTrace($throwable->getTrace());
    }

    public function getPrevious(): ?Throwable
    {
        return null;
    }

    /**
     * TODO: implement this properly
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getMessage();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getTraceAsString(): string
    {
        return json_encode(
            ... $this->getTrace()
        );
    }

    public function getTrace(): Volatile
    {
        return $this->trace;
    }

    public function setTrace(array $trace): void
    {
        $this->getTrace()->merge($trace, true);
    }

    public function getTraceAsArray(): array
    {
        return (array)$this->trace;
    }

    public function asException(): PromiseException
    {
        return new PromiseException($this->getMessage(), $this->getCode(), $this->getFile(), $this->getLine());
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }
}
