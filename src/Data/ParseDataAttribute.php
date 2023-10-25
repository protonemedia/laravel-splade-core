<?php

namespace ProtoneMedia\SpladeCore\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ProtoneMedia\SpladeCore\Facades\Transformer;

class ParseDataAttribute
{
    /**
     * Tries to parse the given $data into an array.
     *
     * @param  mixed  $data
     * @return mixed
     */
    public static function handle($data = null)
    {
        if ($data === null) {
            return;
        }

        if (is_array($data) || is_object($data)) {
            $data = Transformer::handle($data);
        }

        if ($data instanceof Jsonable) {
            return json_decode($data->toJson(), true);
        }

        if ($data instanceof JsonSerializable) {
            return json_decode(json_encode($data), true);
        }

        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            return json_decode(json_encode($data), true);
        }

        if (is_string($data)) {
            if ($decoded = rescue(fn () => json_decode($data, true), null, false)) {
                return $decoded;
            }
        }

        return null;
    }
}
