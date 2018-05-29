<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\Block;

use Exception;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ViewBlock extends Controller
{
    /**
     * @var \Netgen\BlockManager\Error\ErrorHandlerInterface
     */
    private $errorHandler;

    public function __construct(ErrorHandlerInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * Renders the provided block. Used by ESI rendering strategy, so if rendering fails,
     * we log an error and just return an empty response in order not to crash the page.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $viewContext
     *
     * @throws \Exception If rendering fails
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, $viewContext = ViewInterface::CONTEXT_DEFAULT)
    {
        try {
            return $this->buildView($block, $viewContext);
        } catch (Throwable $t) {
            $message = sprintf('Error rendering a block with ID %d', $block->getId());

            $this->errorHandler->handleError($t, $message);
        } catch (Exception $e) {
            $message = sprintf('Error rendering a block with ID %d', $block->getId());

            $this->errorHandler->handleError($e, $message);
        }

        return new Response();
    }

    protected function checkPermissions()
    {
    }
}