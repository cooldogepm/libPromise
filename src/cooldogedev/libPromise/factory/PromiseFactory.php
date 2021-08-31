<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\factory;

use cooldogedev\libPromise\IPromise;
use cooldogedev\libPromise\task\ThreadedPromiseSettler;
use cooldogedev\libPromise\thread\PromiseThread;
use cooldogedev\libPromise\thread\ThreadedPromise;
use cooldogedev\libPromise\traits\PromiseContainerTrait;
use pocketmine\plugin\Plugin;

final class PromiseFactory
{
    use PromiseContainerTrait {
        addPromise as protected _addPromise;
    }

    protected PromiseThread $thread;

    public function __construct(protected Plugin $plugin)
    {
        $this->thread = new PromiseThread();
        $this->promises = [];
        $this->getThread()->start();
        $this->getPlugin()->getScheduler()->scheduleRepeatingTask(new ThreadedPromiseSettler($this, $this->getThread()), 1);
    }

    protected function getThread(): PromiseThread
    {
        return $this->thread;
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
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
