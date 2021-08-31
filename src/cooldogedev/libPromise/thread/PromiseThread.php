<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\thread;

use Closure;
use cooldogedev\libPromise\traits\PromiseContainerTrait;
use pocketmine\thread\Thread;

final class PromiseThread extends Thread
{
    use PromiseContainerTrait;

    protected bool $running;

    public function __construct()
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
            $onCompletion && $onCompletion();
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
}
