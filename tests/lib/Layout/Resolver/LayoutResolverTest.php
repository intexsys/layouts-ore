<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\LayoutResolver;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LayoutResolverTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface
     */
    private $layoutResolver;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->targetTypeRegistry = new TargetTypeRegistry();

        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('/'));

        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            $this->targetTypeRegistry,
            $this->requestStack
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRules()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', 84));

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule3 = new Rule(
            [
                'layout' => new Layout(['id' => 14]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule4 = new Rule(
            [
                'layout' => new Layout(['id' => 15]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->layoutResolverServiceMock
            ->expects($this->at(1))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule3, $rule4]));

        $resolvedRules = $this->layoutResolver->resolveRules();

        $this->assertCount(4, $resolvedRules);

        // We can't be sure in what order two rules with same priority will be returned,
        // so just assert the first one and the last one
        $this->assertEquals($rule3, $resolvedRules[0]);
        $this->assertEquals($rule1, $resolvedRules[3]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithInvalidRule()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertEquals([$rule1], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoValidRules()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));

        $rule1 = new Rule(
            [
                'layout' => null,
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertEquals([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValue()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', null));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', 84));

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertEquals([$rule2, $rule1], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValues()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', null));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', null));

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertEquals([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoRequest()
    {
        $this->requestStack->pop();

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertEquals([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     *
     * @param array $matches
     * @param int $layoutId
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsProvider
     */
    public function testResolveRulesWithConditionsAndPartialConditionMatching(array $matches, $layoutId)
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertEquals(
            $layoutId !== null ? [$rule] : [],
            $this->layoutResolver->resolveRules(null, ['condition2'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     *
     * @param array $matches
     * @param int $layoutId
     *
     * @dataProvider resolveRulesWithRuleConditionsProvider
     */
    public function testResolveRulesWithConditions(array $matches, $layoutId)
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertEquals(
            $layoutId !== null ? [$rule] : [],
            $this->layoutResolver->resolveRules()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRule()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', 84));

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule3 = new Rule(
            [
                'layout' => new Layout(['id' => 14]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule4 = new Rule(
            [
                'layout' => new Layout(['id' => 15]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->layoutResolverServiceMock
            ->expects($this->at(1))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule3, $rule4]));

        $this->assertEquals($rule3, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithInvalidRule()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertEquals($rule1, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoValidRules()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));

        $rule1 = new Rule(
            [
                'layout' => null,
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoTargetValue()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', null));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', 84));

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertEquals($rule2, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoTargetValues()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', null));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', null));

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoRequest()
    {
        $this->requestStack->pop();

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @param array $matches
     * @param int $layoutId
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsProvider
     */
    public function testResolveRuleWithConditionsAndPartialConditionMatching(array $matches, $layoutId)
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertEquals($layoutId !== null ? $rule : null, $this->layoutResolver->resolveRule(null, ['condition2']));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @param array $matches
     * @param int $layoutId
     *
     * @dataProvider resolveRulesWithRuleConditionsProvider
     */
    public function testResolveRuleWithConditions(array $matches, $layoutId)
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertEquals($layoutId !== null ? $rule : null, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     *
     * @param array $matches
     * @param bool $isMatch
     *
     * @dataProvider matchesProvider
     */
    public function testMatches(array $matches, $isMatch)
    {
        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => 42]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->assertEquals($isMatch, $this->layoutResolver->matches($rule, Request::create('/')));
    }

    public function resolveRulesWithRuleConditionsProvider()
    {
        return [
            [[], 42],
            [['condition' => true], 42],
            [['condition' => false], null],
            [['condition1' => true, 'condition2' => false], null],
            [['condition1' => false, 'condition2' => true], null],
            [['condition1' => false, 'condition2' => false], null],
            [['condition1' => true, 'condition2' => true], 42],
        ];
    }

    public function resolveRulesWithPartialRuleConditionsProvider()
    {
        return [
            [[], 42],
            [['condition' => true], 42],
            [['condition' => false], 42],
            [['condition1' => true, 'condition2' => false], null],
            [['condition1' => false, 'condition2' => true], 42],
            [['condition1' => false, 'condition2' => false], null],
            [['condition1' => true, 'condition2' => true], 42],
        ];
    }

    public function matchesProvider()
    {
        return [
            [[], true],
            [['condition' => true], true],
            [['condition' => false], false],
            [['condition1' => true, 'condition2' => false], false],
            [['condition1' => false, 'condition2' => true], false],
            [['condition1' => false, 'condition2' => false], false],
            [['condition1' => true, 'condition2' => true], true],
        ];
    }
}
