<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Create extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator
     */
    private $createStructValidator;

    public function __construct(LayoutService $layoutService, CreateStructValidator $createStructValidator)
    {
        $this->layoutService = $layoutService;
        $this->createStructValidator = $createStructValidator;
    }

    /**
     * Creates the layout.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout type does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function __invoke(Request $request)
    {
        $requestData = $request->attributes->get('data');

        $this->createStructValidator->validateCreateLayout($requestData);

        try {
            $layoutType = $this->getLayoutType($requestData->get('layout_type'));
        } catch (LayoutTypeException $e) {
            throw new BadStateException('layout_type', 'Layout type does not exist.', $e);
        }

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            $requestData->get('name'),
            $requestData->get('locale')
        );

        $layoutCreateStruct->description = $requestData->get('description');

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        return new View($createdLayout, Version::API_V1, Response::HTTP_CREATED);
    }
}