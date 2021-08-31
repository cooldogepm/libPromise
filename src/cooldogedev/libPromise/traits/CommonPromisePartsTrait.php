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

namespace cooldogedev\libPromise\traits;

use Closure;
use cooldogedev\libPromise\constant\PromiseState;
use cooldogedev\libPromise\error\PromiseError;
use cooldogedev\libPromise\IPromise;
use Exception;

trait CommonPromisePartsTrait
{
    protected ?Closure $onSettlement;

    protected ?string $response;
    protected ?PromiseError $error;

    protected bool $settled;

    protected int $state;

    /**
     * @var Closure[]
     */
    protected array $thenables;

    /**
     * @var Closure[]
     */
    protected array $catchers;

    public function then(Closure $resolve): IPromise
    {
        $this->thenables[] = $resolve;
        return $this;
    }

    public function catch(Closure $closure): IPromise
    {
        $this->catchers[] = $closure;
        return $this;
    }

    public function finally(Closure $closure): IPromise
    {
        $this->onSettlement = $closure;
        return $this;
    }

    public function isSettled(): bool
    {
        return $this->settled;
    }

    public function setSettled(bool $settled): void
    {
        $this->settled = $settled;
    }

    public function isPending(): bool
    {
        return $this->getState() === PromiseState::PROMISE_STATE_PENDING;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function settle(): void
    {
        try {
            $executor = $this->getExecutor();

            if ($executor) {
                $response = $executor(
                    fn(mixed $response) => $this->resolve($response),
                    fn(Exception $exception) => $this->reject($exception)
                );
                if ($response) {
                    $this->setResponse($response);
                }
            }

            $this->getError() ? $this->handleRejection() : $this->handleResolve();
        } catch (Exception $exception) {
            $this->reject($exception);
            $this->handleRejection();
        } finally {
            $onSettlement = $this->getOnSettlement();
            $onSettlement && $onSettlement();
            $this->setSettled(true);
        }
    }

    public function getExecutor(): Closure
    {
        return $this->executor;
    }

    public function resolve(mixed $value): IPromise
    {
        $this->setResponse($value);
        return $this;
    }

    public function reject(?Exception $exception): IPromise
    {
        $this->setError(new PromiseError($exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(), $exception->getTrace()));
        return $this;
    }

    public function getError(): ?PromiseError
    {
        return $this->error;
    }

    public function setError(?PromiseError $error): void
    {
        $this->error = $error;
    }

    public function handleRejection(): void
    {
        foreach ($this->getCatchers() as $catcher) {
            $catcher($this->getError());
        }

        $this->setState(PromiseState::PROMISE_STATE_REJECTED);
    }

    public function getCatchers(): array
    {
        return $this->catchers;
    }

    public function handleResolve(): void
    {
        foreach ($this->getThenables() as $thenable) {
            $response = $thenable($this->getResponse());
            $response && $this->setResponse($response);
        }

        $this->setState(PromiseState::PROMISE_STATE_FULFILLED);
    }

    public function getThenables(): array
    {
        return $this->thenables;
    }

    public function getResponse(): mixed
    {
        return unserialize($this->response);
    }

    public function setResponse(mixed $response): void
    {
        $this->response = serialize($response);
    }

    public function getOnSettlement(): ?Closure
    {
        return $this->onSettlement;
    }
}
