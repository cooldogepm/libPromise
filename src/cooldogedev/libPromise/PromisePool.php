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

use cooldogedev\libPromise\thread\PromiseThread;
use cooldogedev\libPromise\thread\ThreadedPromise;
use cooldogedev\libPromise\traits\PromiseContainerTrait;
use pocketmine\plugin\PluginBase;
use pocketmine\snooze\SleeperNotifier;

final class PromisePool
{
    use PromiseContainerTrait {
        addPromise as protected _addPromise;
    }

    protected SleeperNotifier $sleeperNotifier;
    protected PromiseThread $thread;

    public function __construct(protected PluginBase $plugin)
    {
        $this->sleeperNotifier = new SleeperNotifier();
        $this->thread = new PromiseThread($this->getSleeperNotifier());
        $this->promises = [];

        $this->getPlugin()->getServer()->getTickSleeper()->addNotifier(
            $this->getSleeperNotifier(),
            function () {
                $this->getThread()->clearSettledPromises(fn() => $this->clearSettledPromises());
            }
        );
        $this->getSleeperNotifier()->wakeupSleeper();
        $this->getThread()->start();
    }

    public function getSleeperNotifier(): SleeperNotifier
    {
        return $this->sleeperNotifier;
    }

    public function getPlugin(): PluginBase
    {
        return $this->plugin;
    }

    protected function getThread(): PromiseThread
    {
        return $this->thread;
    }

    public function addPromise(IPromise $promise): bool
    {
        if ($promise instanceof ThreadedPromise) {
            $success = $this->getThread()->addPromise($promise);
            $success && $this->_addPromise($promise);
            return $success;
        }
        return $this->_addPromise($promise);
    }
}
