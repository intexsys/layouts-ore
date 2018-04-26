<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\View\Fragment\ViewRendererInterface as FragmentViewRendererInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

/**
 * This renderer is used in the frontend to enable rendering some of the entities
 * with ESI fragments.
 */
final class FragmentRenderer implements RendererInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    private $viewBuilder;

    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    private $viewRenderer;

    /**
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler
     */
    private $fragmentHandler;

    /**
     * @var \Netgen\BlockManager\View\Fragment\ViewRendererInterface[]
     */
    private $fragmentViewRenderers;

    /**
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\BlockManager\View\ViewRendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param \Netgen\BlockManager\View\Fragment\ViewRendererInterface[] $fragmentViewRenderers
     */
    public function __construct(
        ViewBuilderInterface $viewBuilder,
        ViewRendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler,
        array $fragmentViewRenderers = []
    ) {
        foreach ($fragmentViewRenderers as $fragmentViewRenderer) {
            if (!$fragmentViewRenderer instanceof FragmentViewRendererInterface) {
                throw new InvalidInterfaceException(
                    'Fragment view renderer',
                    get_class($fragmentViewRenderer),
                    FragmentViewRendererInterface::class
                );
            }
        }

        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
        $this->fragmentHandler = $fragmentHandler;
        $this->fragmentViewRenderers = $fragmentViewRenderers;
    }

    public function renderValue($value, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = [])
    {
        $view = $this->viewBuilder->buildView($value, $context, $parameters);
        if (!$view instanceof CacheableViewInterface || !$view->isCacheable()) {
            return $this->viewRenderer->renderView($view);
        }

        $fragmentViewRenderer = $this->getFragmentViewRenderer($view);
        if (!$fragmentViewRenderer instanceof FragmentViewRendererInterface) {
            return $this->viewRenderer->renderView($view);
        }

        $controller = $fragmentViewRenderer->getController($view);
        if (!$controller instanceof ControllerReference) {
            return $this->viewRenderer->renderView($view);
        }

        return $this->fragmentHandler->render($controller, 'esi');
    }

    /**
     * Returns the fragment view renderer for the provided view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return \Netgen\BlockManager\View\Fragment\ViewRendererInterface|null
     */
    private function getFragmentViewRenderer(ViewInterface $view)
    {
        foreach ($this->fragmentViewRenderers as $fragmentViewRenderer) {
            if (!$fragmentViewRenderer->supportsView($view)) {
                continue;
            }

            return $fragmentViewRenderer;
        }
    }
}
