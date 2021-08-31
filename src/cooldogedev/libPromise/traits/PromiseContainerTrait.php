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

    /**
     * @return IPromise[]
     */
    public function getPromises(): array
    {
        return $this->promises;
    }

    public function clearSettledPromises(?Closure $then = null): void
    {
        foreach ($this->getSettledPromises() as $index => $_) {
            $this->removePromise($index);
        }
        $then && $then();
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
