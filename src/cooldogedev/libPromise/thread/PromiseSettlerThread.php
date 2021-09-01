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

namespace cooldogedev\libPromise\thread;

use Closure;
use cooldogedev\libPromise\traits\PromiseContainerTrait;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;

final class PromiseSettlerThread extends Thread
{
    use PromiseContainerTrait;

    protected bool $running;

    public function __construct(protected SleeperNotifier $sleeperNotifier)
    {
        $this->running = true;
        $this->promises = [];
    }

    public function clearSettledPromises(?Closure $then = null): void
    {
        /**
         * @var ThreadedPromise $promise
         */
        foreach ($this->getSettledPromises() as $index => $promise) {
            $onCompletion = $promise->getOnCompletion();
            $onCompletion && $onCompletion($promise->getResponse());
            $this->removePromise($index);
        }
        $then && $then();
    }

    protected function onRun(): void
    {
        while ($this->isRunning()) {
            foreach ($this->getPendingPromises() as $promise) {
                $promise->settle();
            }
            $this->getSleeperNotifier()->wakeupSleeper();
        }
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    public function setRunning(bool $running): void
    {
        $this->running = $running;
    }

    public function getSleeperNotifier(): SleeperNotifier
    {
        return $this->sleeperNotifier;
    }
}
