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
     * this method is ran on the main thread after settlement, it's similar to
     * @link IPromise::finally() except this one gets the response as a parameter.
     *
     * @return Closure|null
     */
    public function getOnCompletion(): ?Closure
    {
        return $this->onCompletion;
    }
}
