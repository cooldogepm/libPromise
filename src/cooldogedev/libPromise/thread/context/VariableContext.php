<?php

declare(strict_types=1);

namespace cooldogedev\libPromise\thread\context;

/**
 * TODO: Implement this in @link ThreadedPromise
 */
final class VariableContext
{
    protected array $variables;

    public function __construct()
    {
        $this->variables = [];
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariable(string $variable, mixed $value, bool $override): bool
    {
        if ($this->hasVariable($variable) && !$override) {
            return false;
        }
        $this->variables[$variable] = serialize($value);
        return true;
    }

    public function getVariable(string $variable): mixed
    {
        return $this->hasVariable($variable) ? unserialize($this->variables[$variable]) : null;
    }

    public function hasVariable(string $variable): bool
    {
        return isset($this->variables[$variable]);
    }

    public function removeVariable(string $variable): bool
    {
        if (!$this->hasVariable($variable)) {
            return false;
        }
        unset($this->variables[$variable]);
        return true;
    }
}
