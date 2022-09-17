<?php

declare(strict_types=1);

namespace F9Web\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Thrown when a file doesn't exist.
 */
class FileNotFoundException extends NotFoundHttpException
{
    public function __construct(
        string     $message = 'File not found in the given disk.',
        Throwable $previous = null,
        int        $code = 0,
        array      $headers = []
    )
    {
        parent::__construct($message, $previous, $code, $headers);
    }
}
