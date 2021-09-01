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

use Closure;
use cooldogedev\libPromise\constant\PromiseState;
use cooldogedev\libPromise\error\PromiseError;
use cooldogedev\libPromise\traits\SharedPromisePartsTrait;

final class Promise implements IPromise
{
    use SharedPromisePartsTrait;

    protected ?Closure $onSettlement;

    protected mixed $response;

    protected ?PromiseError $error;

    protected bool $settled;

    protected int $state;

    /**
     * @var Closure[]
     */
    protected array $thenables = [];

    /**
     * @var Closure[]
     */
    protected array $catchers = [];

    public function __construct(protected ?Closure $executor = null)
    {
        $this->onSettlement = null;

        $this->response = null;

        $this->error = null;

        $this->settled = false;

        $this->state = PromiseState::PROMISE_STATE_PENDING;

        $this->thenables = [];

        $this->catchers = [];
    }

    public function then(Closure $resolve): IPromise
    {
        $this->thenables[] = $resolve;
        return $this;
    }

    public function catch(Closure $closure): IPromise
    {
        $this->catchers[] = $closure;
        return $this;
    }

    public function getCatchers(): array
    {
        return $this->catchers;
    }

    public function getThenables(): array
    {
        return $this->thenables;
    }

    public function getResponse(): mixed
    {
        return $this->response;
    }

    public function setResponse(mixed $response): void
    {
        $this->response = $response;
    }
}
