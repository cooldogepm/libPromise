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

namespace cooldogedev\libPromise\thread\store;

use Threaded;

final class VariableStore extends Threaded
{
    protected Threaded $store;

    public function __construct()
    {
        $this->store = new Threaded();
    }

    public function setVariable(string $variable, mixed $value, bool $override = false, bool $serialize = false): bool
    {
        if ($this->hasVariable($variable) && !$override) {
            return false;
        }
        $this->store[$variable] = $serialize ? serialize($value) : $value;
        return true;
    }

    public function hasVariable(string $variable): bool
    {
        return isset($this->store[$variable]);
    }

    public function getVariable(string $variable, bool $deserialize = false): mixed
    {
        if (!$this->hasVariable($variable)) {
            return null;
        }
        $value = $this->store[$variable];
        return $deserialize ? unserialize($value) ?: null : $value;
    }

    public function getStore(): Threaded
    {
        return $this->store;
    }
}
