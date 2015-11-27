<?php

namespace Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

class PathInfoPrefix extends RoutePrefix
{
    /**
     * Returns the target identifier this handler handles.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'path_info_prefix';
    }
}
