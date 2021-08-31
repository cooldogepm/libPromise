<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\traits;

use Closure;
use cooldogedev\libPromise\IPromise;

trait PromiseContainerTrait
{
    protected array $promises;

    public function addPromise(IPromise $promise): bool
    {
        if ($promise->isSettled()) {
            return false;
        }
        $this->promises[] = $promise;
        return true;
    }

    public function getPromise(int $promise): ?IPromise
    {
        return $this->promises[$promise] ?? null;
    }

    /**
     * @return IPromise[]
     */
    public function getSettledPromises(): array
    {
        $promises = [];
        foreach ($this->getPromises() as $promise) {
            if (!$promise->isSettled()) {
                continue;
            }
            $promises[] = $promise;
        }
        return $promises;
    }

    /**
     * @return IPromise[]
     */
    public function getPromises(): array
    {
        return $this->promises;
    }

    /**
     * @return IPromise[]
     */
    public function getPendingPromises(): array
    {
        $promises = [];
        foreach ($this->getPromises() as $promise) {
            if (!$promise->isPending()) {
                continue;
            }
            $promises[] = $promise;
        }
        return $promises;
    }

    public function clearSettledPromises(?Closure $then = null): void
    {
        foreach ($this->getSettledPromises() as $index => $_) {
            $this->removePromise($index);
        }
        $then && $then();
    }

    public function removePromise(int $promise): bool
    {
        if (!$this->hasPromise($promise)) {
            return false;
        }
        unset($this->promises[$promise]);
        return true;
    }

    public function hasPromise(int $promise): bool
    {
        return isset($this->promises[$promise]);
    }
}
