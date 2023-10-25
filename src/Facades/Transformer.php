<?php

namespace ProtoneMedia\SpladeCore\Facades;

use Illuminate\Support\Facades\Facade;
use ProtoneMedia\SpladeCore\Data\TransformerRepository;

/**
 * @method static self enforce($value = true)
 * @method static self register($class, $transformer = null)
 * @method static mixed handle(mixed $instance)
 *
 * @see \ProtoneMedia\SpladeCore\Data\TransformerRepository
 */
class Transformer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TransformerRepository::class;
    }
}
