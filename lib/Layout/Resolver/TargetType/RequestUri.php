<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class RequestUri implements TargetTypeInterface
{
    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType()
    {
        return 'request_uri';
    }

    /**
     * Returns the constraints that will be used to validate the target value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'string')),
        );
    }

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function provideValue(Request $request)
    {
        return $request->getRequestUri();
    }
}
