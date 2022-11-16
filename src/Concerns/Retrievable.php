<?php

declare(strict_types=1);

namespace Pest\Concerns;

use Pest\Support\Arr;

/**
 * @internal
 */
trait Retrievable
{
    /**
     * @template TRetrievableValue
     *
     * Safely retrieve the value at the given key from an object or array.
     *
     * @param  array<string, TRetrievableValue>|object  $value
     * @param  TRetrievableValue|null  $default
     * @return TRetrievableValue|null
     */
    private function retrieve(string $key, mixed $value, mixed $default = null): mixed
    {
        foreach (explode('.', $key) as $segment) {
            if (is_array($value)) {
                $value = Arr::get($value, $segment, $default);
            } elseif (is_object($value)) {
                // @phpstan-ignore-next-line
                $value = $value->$segment ?? $default;
            } else {
                return $default;
            }
        }

        return $value;
    }
}
