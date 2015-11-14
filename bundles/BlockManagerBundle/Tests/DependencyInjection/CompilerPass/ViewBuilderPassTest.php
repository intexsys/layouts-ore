<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\ViewBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ViewBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ViewBuilderPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\ViewBuilderPass::process
     */
    public function testProcess()
    {
        $viewBuilder = new Definition();
        $viewBuilder->addArgument(array());
        $viewBuilder->addArgument(array());
        $this->setDefinition('netgen_block_manager.view.builder', $viewBuilder);

        $viewProvider = new Definition();
        $viewProvider->addTag('netgen_block_manager.view.provider');
        $this->setDefinition('netgen_block_manager.view.provider.test', $viewProvider);

        $templateResolver = new Definition();
        $templateResolver->addTag('netgen_block_manager.view.template_resolver');
        $this->setDefinition('netgen_block_manager.view.template_resolver.test', $templateResolver);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.builder',
            0,
            array(
                new Reference('netgen_block_manager.view.provider.test'),
            )
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.builder',
            1,
            array(
                new Reference('netgen_block_manager.view.template_resolver.test'),
            )
        );
    }
}
