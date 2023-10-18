<?php

namespace ProtoneMedia\SpladeCore;

use Closure;
use Illuminate\Http\Request;

class SpladeCoreRequest
{
    public function __construct(
        protected Closure $requestResolver
    ) {
    }

    /**
     * Returns the current request.
     */
    protected function getRequest(): Request
    {
        return ($this->requestResolver)();
    }

    /**
     * Indicates if the request is meant to refresh a component.
     */
    public function isRefreshingComponent(): bool
    {
        return $this->getRequest()->hasHeader('X-Splade-Component-Refresh');
    }

    /**
     * Returns the ID of the component that should be refreshed.
     */
    public function getComponentRefreshId(): string
    {
        return $this->getRequest()->header('X-Splade-Component-Refresh', '');
    }
}
