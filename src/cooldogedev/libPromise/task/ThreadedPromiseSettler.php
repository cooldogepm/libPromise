<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\task;

use cooldogedev\libPromise\factory\PromiseFactory;
use cooldogedev\libPromise\thread\PromiseThread;
use pocketmine\scheduler\Task;

final class ThreadedPromiseSettler extends Task
{
    public function __construct(protected PromiseFactory $factory, protected PromiseThread $thread)
    {
    }

    public function onRun(): void
    {
        $this->getThread()->clearSettledPromises(fn () => $this->getFactory()->clearSettledPromises());
    }

    public function getThread(): PromiseThread
    {
        return $this->thread;
    }

    public function getFactory(): PromiseFactory
    {
        return $this->factory;
    }
}
