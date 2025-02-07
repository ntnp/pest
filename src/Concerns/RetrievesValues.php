<?php

declare(strict_types=1);

namespace Pest\Concerns;

use Pest\Support\Arr;

/**
 * @internal
 */
trait RetrievesValues
{
    /**
     * @template TRetrievableValue
     *
     * Safely retrieve the value at the given key from an object or array.
     *
     * @param array<string, TRetrievableValue>|object $value
     * @param TRetrievableValue|null                  $default
     *
     * @return TRetrievableValue|null
     */
    private function retrieve(string $key, $value, $default = null)
    {
        if (is_array($value)) {
            /**
             * @var TRetrievableValue|null
             *
             * @phpstan-ignore-next-line
             */
            return Arr::get($value, $key, $default);
        }

        // @phpstan-ignore-next-line
        return $value->$key ?? $default;
    }
}
