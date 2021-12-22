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

use cooldogedev\libPromise\thread\PromiseSettlerThread;
use cooldogedev\libPromise\thread\ThreadedPromise;
use pocketmine\plugin\PluginBase;
use pocketmine\snooze\SleeperNotifier;

final class PromisePool
{
    /**
     * @var ThreadedPromise[]
     */
    protected array $executionQueue;

    protected PromiseSettlerThread $thread;

    public function __construct(protected PluginBase $plugin)
    {
        $this->executionQueue = [];

        $sleeperNotifier = new SleeperNotifier();

        $this->thread = new PromiseSettlerThread($sleeperNotifier);

        $this->getPlugin()->getServer()->getTickSleeper()->addNotifier(
            $sleeperNotifier,
            function (): void {
                if (count($this->getExecutionQueue()) > 0) {

                    $queue = array_keys($this->getExecutionQueue());
                    $promiseIndex = array_shift($queue);

                    $promise = $this->getFromExecutionQueue($promiseIndex);

                    if ($promise->isSettled()) {
                        // in case the onCompletion was manipulated after the submission
                        $promise->getOnCompletion() && $promise->getOnCompletion()($promise->getResponse());
                        $this->removeFromExecutionQueue($promiseIndex);
                    }

                }
            }
        );

        $this->getThread()->start();
    }

    public function getPlugin(): PluginBase
    {
        return $this->plugin;
    }

    /**
     * @return ThreadedPromise[]
     */
    public function getExecutionQueue(): array
    {
        return $this->executionQueue;
    }

    public function getFromExecutionQueue(int $promise): ?ThreadedPromise
    {
        return $this->executionQueue[$promise] ?? null;
    }

    public function removeFromExecutionQueue(int $promise): bool
    {
        if (!$this->isInExecutionQueue($promise)) {
            return false;
        }
        unset($this->executionQueue[$promise]);
        return true;
    }

    public function isInExecutionQueue(int $promise): bool
    {
        return isset($this->executionQueue[$promise]);
    }

    protected function getThread(): PromiseSettlerThread
    {
        return $this->thread;
    }

    /**
     * @param ThreadedPromise $promise
     *
     * @param bool $queueOnCompletionExecution used to determine whether you want to execute the
     * @return bool
     * @link ThreadedPromise::getOnCompletion() on the main thread or not.
     *
     */
    public function addPromise(ThreadedPromise $promise, bool $queueOnCompletionExecution = false): bool
    {
        $success = $this->getThread()->addPromise($promise);

        if ($success && $queueOnCompletionExecution) {
            $this->addToExecutionQueue($promise);
        }

        return $success;
    }

    public function addToExecutionQueue(ThreadedPromise $promise): void
    {
        $this->executionQueue[] = $promise;
    }
}
