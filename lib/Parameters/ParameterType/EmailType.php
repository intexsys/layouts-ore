<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Validator\Constraints;

class EmailType extends ParameterType
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'email';
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(array('type' => 'string')),
            new Constraints\Email(),
        );
    }
}
