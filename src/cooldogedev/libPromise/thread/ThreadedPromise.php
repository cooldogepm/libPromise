<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\thread;

use Closure;
use cooldogedev\libPromise\constant\PromiseState;
use cooldogedev\libPromise\IPromise;
use cooldogedev\libPromise\thread\context\VariableContext;
use cooldogedev\libPromise\traits\CommonPromisePartsTrait;
use Threaded;

final class ThreadedPromise extends Threaded implements IPromise
{
    use CommonPromisePartsTrait;

    /**
     * TODO: Find a usage for this
     *
     * @var VariableContext
     */
    protected VariableContext $variableContext;

    public function __construct(protected Closure $executor, protected ?Closure $onCompletion = null)
    {
        $this->onSettlement = null;

        $this->response = null;
        $this->error = null;

        $this->settled = false;

        $this->state = PromiseState::PROMISE_STATE_PENDING;

        $this->thenables = [];
        $this->catchers = [];

        $this->variableContext = new VariableContext();
    }

    public function getVariableContext(): VariableContext
    {
        return $this->variableContext;
    }

    public function getOnCompletion(): ?Closure
    {
        return $this->onCompletion;
    }
}
