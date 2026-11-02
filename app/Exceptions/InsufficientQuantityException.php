<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientQuantityException extends RuntimeException
{
    public function __construct(public readonly int $availableQuantity)
    {
        parent::__construct("Nepietiekams produkta daudzums. Pieejams: {$availableQuantity}.");
    }
}
