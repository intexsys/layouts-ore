<?php

namespace Netgen\BlockManager\View\TemplateResolver;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class LayoutViewTemplateResolver extends TemplateResolver
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Matcher\MatcherInterface[] $matchers
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(array $matchers = array(), ConfigurationInterface $configuration)
    {
        parent::__construct($matchers, $configuration->getParameter('layout_view'));
    }

    /**
     * Returns if this template resolver supports the provided view
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return bool
     */
    public function supports(ViewInterface $view)
    {
        return $view instanceof LayoutViewInterface;
    }
}
