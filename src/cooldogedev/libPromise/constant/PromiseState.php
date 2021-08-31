<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\constant;

final class PromiseState
{
    public const PROMISE_STATE_PENDING = 0;
    public const PROMISE_STATE_FULFILLED = 1;
    public const PROMISE_STATE_REJECTED = 2;
}
