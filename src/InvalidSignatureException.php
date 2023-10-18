<?php

namespace ProtoneMedia\SpladeCore;

use Exception;

class InvalidSignatureException extends Exception
{
    public function render()
    {
        return abort(403, 'Malicious request');
    }
}
