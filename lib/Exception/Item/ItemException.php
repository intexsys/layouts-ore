<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Item;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;
use function sprintf;

final class ItemException extends InvalidArgumentException implements Exception
{
    public static function noValueType(string $valueType): self
    {
        return new self(
            sprintf(
                'Value type "%s" does not exist.',
                $valueType
            )
        );
    }

    public static function noValueLoader(string $valueType): self
    {
        return new self(
            sprintf(
                'Value loader for "%s" value type does not exist.',
                $valueType
            )
        );
    }

    public static function noValueUrlGenerator(string $valueType): self
    {
        return new self(
            sprintf(
                'Value URL generator for "%s" value type does not exist.',
                $valueType
            )
        );
    }

    /**
     * @param int|string $value
     *
     * @return \Netgen\Layouts\Exception\Item\ItemException
     */
    public static function noValue($value): self
    {
        return new self(
            sprintf(
                'Value with (remote) ID %s does not exist.',
                $value
            )
        );
    }

    /**
     * @param int|string $value
     *
     * @return \Netgen\Layouts\Exception\Item\ItemException
     */
    public static function invalidValue($value): self
    {
        return new self(
            sprintf(
                'Item "%s" is not valid.',
                $value
            )
        );
    }

    public static function canNotLoadItem(): self
    {
        return new self('Item could not be loaded.');
    }
}
