<?php

namespace Netgen\BlockManager\Exception;

use Exception as BaseException;

class BadStateException extends BaseException implements Exception
{
    /**
     * Creates a new bad state exception.
     *
     * @param string $argument
     * @param string $whatIsWrong
     * @param \Exception $previousException
     */
    public function __construct($argument, $whatIsWrong, BaseException $previousException = null)
    {
        parent::__construct(
            'Argument "' . $argument . '" has an invalid state. ' . $whatIsWrong,
            0,
            $previousException
        );
    }
}
