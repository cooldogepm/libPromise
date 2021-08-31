<?php

declare(strict_types=1);

namespace cooldogedev\libPromise;

use cooldogedev\libPromise\error\PromiseError;

use Closure;
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
