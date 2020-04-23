<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Design;

use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function array_filter;
use function array_map;
use function array_merge;
use function array_reverse;
use function array_unique;
use function array_unshift;
use function array_values;
use function is_dir;

final class ThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('twig.loader.native_filesystem')) {
            return;
        }

        $twigLoader = $container->getDefinition('twig.loader.native_filesystem');

        $designList = $container->getParameter('netgen_layouts.design_list');
        $themeList = array_unique(array_merge(...array_values($designList)));
        $themeList[] = 'standard';

        $themeDirs = $this->getThemeDirs($container, $themeList);

        foreach ($designList as $designName => $designThemes) {
            $designThemes[] = 'standard';

            foreach ($designThemes as $themeName) {
                foreach ($themeDirs[$themeName] as $themeDir) {
                    $container->addResource(new FileExistenceResource($themeDir));

                    if (is_dir($themeDir)) {
                        $twigLoader->addMethodCall('addPath', [$themeDir, 'nglayouts_' . $designName]);
                    }
                }
            }
        }
    }

    /**
     * Returns an array with all found paths for provided theme list.
     *
     * @param string[] $themeList
     *
     * @return array<string, string[]>
     */
    private function getThemeDirs(ContainerBuilder $container, array $themeList): array
    {
        $paths = array_map(
            static function (array $bundleMetadata): array {
                return [
                    $bundleMetadata['path'] . '/Resources/views/nglayouts/themes',
                    $bundleMetadata['path'] . '/templates/nglayouts/themes',
                ];
            },
            // Reversing the list of bundles so bundles added at end have higher priority
            // when searching for a template
            array_reverse($container->getParameter('kernel.bundles_metadata'))
        );

        $paths = array_merge(...array_values($paths));
        $paths = array_filter($paths, 'is_dir');

        $defaultTwigDir = $container->getParameterBag()->resolveValue($container->getParameter('twig.default_path')) . '/nglayouts/themes';
        if (is_dir($defaultTwigDir)) {
            array_unshift($paths, $defaultTwigDir);
        }

        if ($container->hasParameter('kernel.name')) {
            $appDir = $this->getAppDir($container) . '/Resources/views/nglayouts/themes';
            if (is_dir($appDir)) {
                array_unshift($paths, $appDir);
            }
        }

        $themeDirs = [];
        foreach ($paths as $path) {
            foreach ($themeList as $themeName) {
                $themeDirs[$themeName][] = $path . '/' . $themeName;
            }
        }

        return $themeDirs;
    }

    /**
     * Returns the current app dir.
     */
    private function getAppDir(ContainerBuilder $container): string
    {
        return (string) $container->getParameter('kernel.project_dir') . '/' . (string) $container->getParameter('kernel.name');
    }
}
