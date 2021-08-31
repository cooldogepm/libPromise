<?php

declare(strict_types=1);

namespace cooldogedev\libPromise;

use Closure;
use cooldogedev\libPromise\constant\PromiseState;
use cooldogedev\libPromise\traits\CommonPromisePartsTrait;

final class Promise implements IPromise
{
    use CommonPromisePartsTrait;

    public function __construct(protected Closure $executor)
    {
        $this->onSettlement = null;

        $this->response = null;
        $this->error = null;

        $this->settled = false;

        $this->state = PromiseState::PROMISE_STATE_PENDING;

        $this->thenables = [];
        $this->catchers = [];
    }
}
