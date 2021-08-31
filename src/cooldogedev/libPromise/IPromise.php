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

namespace cooldogedev\libPromise;

use Closure;
use cooldogedev\libPromise\error\PromiseError;
use Exception;

interface IPromise
{
    public function getError(): ?PromiseError;

    public function setError(?PromiseError $error): void;

    public function getResponse(): mixed;

    public function setResponse(mixed $response): void;

    public function then(Closure $resolve): IPromise;

    public function catch(Closure $closure): IPromise;

    public function getOnSettlement(): ?Closure;

    public function finally(Closure $closure): IPromise;

    public function isSettled(): bool;

    public function setSettled(bool $settled): void;

    public function isPending(): bool;

    public function getState(): int;

    public function setState(int $state): void;

    public function resolve(mixed $value): IPromise;

    public function reject(?Exception $exception): IPromise;

    public function handleRejection(): void;

    public function handleResolve(): void;

    public function settle(): void;

    public function getExecutor(): Closure;

    public function getThenables(): array;

    public function getCatchers(): array;
}
