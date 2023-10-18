<?php

namespace ProtoneMedia\SpladeCore\Facades;

use Illuminate\Support\Facades\Facade;
use ProtoneMedia\SpladeCore\SpladeCoreRequest;

/**
 * @method static bool isRefreshingComponent()
 *
 * @see \ProtoneMedia\SpladeCore\SpladeCoreRequest
 */
class SpladeCore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SpladeCoreRequest::class;
    }
}
