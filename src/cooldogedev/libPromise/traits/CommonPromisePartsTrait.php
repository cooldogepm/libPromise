<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\traits;

use Closure;
use cooldogedev\libPromise\IPromise;
use Exception;
use cooldogedev\libPromise\constant\PromiseState;
use cooldogedev\libPromise\error\PromiseError;

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

    public function getError(): ?PromiseError
    {
        return $this->error;
    }

    public function setError(?PromiseError $error): void
    {
        $this->error = $error;
    }

    public function getResponse(): mixed
    {
        return unserialize($this->response);
    }

    public function setResponse(mixed $response): void
    {
        $this->response = serialize($response);
    }

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

    public function getOnSettlement(): ?Closure
    {
        return $this->onSettlement;
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

    public function handleRejection(): void
    {
        foreach ($this->getCatchers() as $catcher) {
            $catcher($this->getError());
        }

        $this->setState(PromiseState::PROMISE_STATE_REJECTED);
    }

    public function handleResolve(): void
    {
        foreach ($this->getThenables() as $thenable) {
            $response = $thenable($this->getResponse());
            $response && $this->setResponse($response);
        }

        $this->setState(PromiseState::PROMISE_STATE_FULFILLED);
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

    public function getThenables(): array
    {
        return $this->thenables;
    }

    public function getCatchers(): array
    {
        return $this->catchers;
    }
}
