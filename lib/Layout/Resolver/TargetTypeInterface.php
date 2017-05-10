<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Symfony\Component\HttpFoundation\Request;

interface TargetTypeInterface
{
    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the constraints that will be used to validate the target value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints();

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function provideValue(Request $request);
}
