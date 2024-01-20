<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Container\Container;

class ResolveOnce
{
    private bool $resolved = false;

    private $result;

    public function __construct(
        private $callback
    ) {
        //
    }

    public static function make(callable $callback): self
    {
        return new static($callback);
    }

    public function resolveWhen(bool $value)
    {
        if ($value) {
            return $this->resolve();
        }

        return $this;
    }

    private function resolve()
    {
        if ($this->resolved) {
            return $this->result;
        }

        return tap(Container::getInstance()->call($this->callback), function ($result) {
            $this->result = $result;
            $this->resolved = true;
        });
    }

    public function __invoke()
    {
        return $this->resolve();
    }
}
