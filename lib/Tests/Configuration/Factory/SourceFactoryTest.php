<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\Factory\SourceFactory;
use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class SourceFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\Factory\SourceFactory::buildSource
     */
    public function testBuildSource()
    {
        $source = SourceFactory::buildSource(
            'dynamic',
            array(
                'name' => 'Dynamic source',
                'enabled' => true,
                'queries' => array(
                    'default' => array(
                        'query_type' => 'ezcontent_search',
                        'default_parameters' => array(
                            'parent_location_id' => 2,
                            'content_types' => array('news', 'article'),
                        ),
                    ),
                    'other' => array(
                        'query_type' => 'other_type',
                        'default_parameters' => array(),
                    ),
                ),
            ),
            array(
                'default' => new QueryType('ezcontent_search'),
                'other' => new QueryType('other_type'),
            )
        );

        $this->assertEquals(
            new Source(
                'dynamic',
                true,
                'Dynamic source',
                array(
                    'default' => new Query(
                        'default',
                        new QueryType('ezcontent_search'),
                        array(
                            'parent_location_id' => 2,
                            'content_types' => array('news', 'article'),
                        )
                    ),
                    'other' => new Query(
                        'other',
                        new QueryType('other_type'),
                        array()
                    ),
                )
            ),
            $source
        );
    }
}
