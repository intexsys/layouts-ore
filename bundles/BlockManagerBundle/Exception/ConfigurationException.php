<?php

namespace Netgen\Bundle\BlockManagerBundle\Exception;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class ConfigurationException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $parameterName
     *
     * @return \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException
     */
    public static function noParameter($parameterName)
    {
        return new self(
            sprintf(
                'Parameter "%s" does not exist in configuration.',
                $parameterName
            )
        );
    }
}