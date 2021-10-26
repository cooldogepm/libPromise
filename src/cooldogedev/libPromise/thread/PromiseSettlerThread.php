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

use cooldogedev\libPromise\IPromise;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;
use Volatile;
use const PTHREADS_INHERIT_NONE;

final class PromiseSettlerThread extends Thread
{
    protected bool $running;

    protected Volatile $promises;

    public function __construct(protected SleeperNotifier $sleeperNotifier)
    {
        $this->running = false;
        $this->promises = new Volatile();
    }

    public function addPromise(IPromise $promise): bool
    {
        if ($promise->isSettled()) {
            return false;
        }
        $this->promises[] = $promise;
        return true;
    }

    public function start(int $options = PTHREADS_INHERIT_NONE): bool
    {
        if (parent::start($options)) {
            $this->setRunning(true);
            return true;
        }
        return false;
    }

    protected function onRun(): void
    {
        while ($this->isRunning()) {
            while ($this->getPromises()->count() > 0) {
                /**
                 * @var $promise ThreadedPromise
                 */
                $promise = $this->getPromises()->shift();
                $promise->settle();
                $promise->getOnCompletion() && $this->getSleeperNotifier()->wakeupSleeper();
            }
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

    public function getPromises(): Volatile
    {
        return $this->promises;
    }

    public function getSleeperNotifier(): SleeperNotifier
    {
        return $this->sleeperNotifier;
    }
}
