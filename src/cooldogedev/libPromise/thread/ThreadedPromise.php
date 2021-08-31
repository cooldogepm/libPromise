<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\thread;

use Closure;
use cooldogedev\libPromise\constant\PromiseState;
use cooldogedev\libPromise\IPromise;
use cooldogedev\libPromise\traits\CommonPromisePartsTrait;
use Threaded;

final class ThreadedPromise extends Threaded implements IPromise
{
    use CommonPromisePartsTrait;

    public function __construct(protected Closure $executor, protected ?Closure $onCompletion = null)
    {
        $this->onSettlement = null;

        $this->response = null;
        $this->error = null;

        $this->settled = false;

        $this->state = PromiseState::PROMISE_STATE_PENDING;

        $this->thenables = [];
        $this->catchers = [];
    }

    /**
     * this method is ran on the main thread after settlement, it's similar to @see IPromise::finally()
     * except this one gets the response as a parameter.
     *
     * @return Closure|null
     */
    public function getOnCompletion(): ?Closure
    {
        return $this->onCompletion;
    }
}
