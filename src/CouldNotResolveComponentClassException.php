<?php

namespace ProtoneMedia\SpladeCore;

use Exception;

class CouldNotResolveComponentClassException extends Exception
{
    public function render()
    {
        return abort(403, 'Component not found');
    }
}
