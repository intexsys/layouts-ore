<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverHandlerTest extends TestCase
{
    use TestCaseTrait;
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    public function setUp(): void
    {
        $this->createDatabase();

        $this->handler = $this->createLayoutResolverHandler();
        $this->layoutHandler = $this->createLayoutHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     */
    public function testLoadRule(): void
    {
        $rule = $this->handler->loadRule(1, Value::STATUS_PUBLISHED);

        $this->assertInstanceOf(Rule::class, $rule);

        $this->assertSame(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'layoutId' => 1,
                'enabled' => true,
                'priority' => 9,
                'comment' => 'My comment',
            ],
            $this->exportObject($rule)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "999999"
     */
    public function testLoadRuleThrowsNotFoundException(): void
    {
        $this->handler->loadRule(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRules(): void
    {
        $rules = $this->handler->loadRules(Value::STATUS_PUBLISHED);

        $this->assertCount(12, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            $this->assertInstanceOf(Rule::class, $rule);

            if ($index > 0) {
                $this->assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRulesData
     */
    public function testLoadRulesWithLayout(): void
    {
        $rules = $this->handler->loadRules(
            Value::STATUS_PUBLISHED,
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(2, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            $this->assertInstanceOf(Rule::class, $rule);

            if ($index > 0) {
                $this->assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCount
     */
    public function testGetRuleCount(): void
    {
        $rules = $this->handler->getRuleCount();

        $this->assertSame(12, $rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRuleCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getRuleCount
     */
    public function testGetRuleCountWithLayout(): void
    {
        $rules = $this->handler->getRuleCount(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );

        $this->assertSame(2, $rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     */
    public function testLoadTarget(): void
    {
        $target = $this->handler->loadTarget(1, Value::STATUS_PUBLISHED);

        $this->assertInstanceOf(Target::class, $target);

        $this->assertSame(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 1,
                'type' => 'route',
                'value' => 'my_cool_route',
            ],
            $this->exportObject($target)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadTargetData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find target with identifier "999999"
     */
    public function testLoadTargetThrowsNotFoundException(): void
    {
        $this->handler->loadTarget(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleTargets
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testLoadRuleTargets(): void
    {
        $targets = $this->handler->loadRuleTargets(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );

        $this->assertNotEmpty($targets);

        foreach ($targets as $target) {
            $this->assertInstanceOf(Target::class, $target);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getTargetCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getTargetCount
     */
    public function testGetTargetCount(): void
    {
        $targets = $this->handler->getTargetCount(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED)
        );

        $this->assertSame(2, $targets);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getConditionSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     */
    public function testLoadCondition(): void
    {
        $condition = $this->handler->loadCondition(1, Value::STATUS_PUBLISHED);

        $this->assertInstanceOf(Condition::class, $condition);

        $this->assertSame(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 2,
                'type' => 'route_parameter',
                'value' => [
                    'parameter_name' => 'some_param',
                    'parameter_values' => [1, 2],
                ],
            ],
            $this->exportObject($condition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadConditionData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find condition with identifier "999999"
     */
    public function testLoadConditionThrowsNotFoundException(): void
    {
        $this->handler->loadCondition(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::loadRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     */
    public function testLoadRuleConditions(): void
    {
        $conditions = $this->handler->loadRuleConditions(
            $this->handler->loadRule(2, Value::STATUS_PUBLISHED)
        );

        $this->assertNotEmpty($conditions);

        foreach ($conditions as $condition) {
            $this->assertInstanceOf(Condition::class, $condition);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleExists(): void
    {
        $this->assertTrue($this->handler->ruleExists(1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExists(): void
    {
        $this->assertFalse($this->handler->ruleExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::ruleExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::ruleExists
     */
    public function testRuleNotExistsInStatus(): void
    {
        $this->assertFalse($this->handler->ruleExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRule(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->layoutId = 3;
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->enabled = true;
        $ruleCreateStruct->comment = 'My rule';
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        $this->assertInstanceOf(Rule::class, $createdRule);

        $this->assertSame(13, $createdRule->id);
        $this->assertSame(3, $createdRule->layoutId);
        $this->assertSame(5, $createdRule->priority);
        $this->assertTrue($createdRule->enabled);
        $this->assertSame('My rule', $createdRule->comment);
        $this->assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRuleWithNoPriority(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        $this->assertInstanceOf(Rule::class, $createdRule);

        $this->assertSame(13, $createdRule->id);
        $this->assertNull($createdRule->layoutId);
        $this->assertSame(-12, $createdRule->priority);
        $this->assertFalse($createdRule->enabled);
        $this->assertSame('', $createdRule->comment);
        $this->assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::getRulePriority
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::getLowestRulePriority
     */
    public function testCreateRuleWithNoPriorityAndNoRules(): void
    {
        // First delete all rules
        $rules = $this->handler->loadRules(Value::STATUS_PUBLISHED);
        foreach ($rules as $rule) {
            $this->handler->deleteRule($rule->id);
        }

        $rules = $this->handler->loadRules(Value::STATUS_DRAFT);
        foreach ($rules as $rule) {
            $this->handler->deleteRule($rule->id);
        }

        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->status = Value::STATUS_DRAFT;

        $createdRule = $this->handler->createRule($ruleCreateStruct);

        $this->assertInstanceOf(Rule::class, $createdRule);

        $this->assertSame(0, $createdRule->priority);
        $this->assertSame(Value::STATUS_DRAFT, $createdRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRule(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 15;
        $ruleUpdateStruct->comment = 'New comment';

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertSame(3, $updatedRule->id);
        $this->assertSame(15, $updatedRule->layoutId);
        $this->assertSame('New comment', $updatedRule->comment);
        $this->assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithStringLayoutId(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = '15';
        $ruleUpdateStruct->comment = 'New comment';

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertSame(3, $updatedRule->id);
        $this->assertSame('15', $updatedRule->layoutId);
        $this->assertSame('New comment', $updatedRule->comment);
        $this->assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithRemovalOfLinkedLayout(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 0;

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertSame(3, $updatedRule->id);
        $this->assertNull($updatedRule->layoutId);
        $this->assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithRemovalOfLinkedLayoutAndStringLayoutId(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = '0';

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertSame(3, $updatedRule->id);
        $this->assertNull($updatedRule->layoutId);
        $this->assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRule
     */
    public function testUpdateRuleWithDefaultValues(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();

        $updatedRule = $this->handler->updateRule(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $ruleUpdateStruct
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);

        $this->assertSame(3, $updatedRule->id);
        $this->assertSame(3, $updatedRule->layoutId);
        $this->assertNull($updatedRule->comment);
        $this->assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleMetadata
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testUpdateRuleMetadata(): void
    {
        $updatedRule = $this->handler->updateRuleMetadata(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED),
            new RuleMetadataUpdateStruct(
                [
                    'enabled' => false,
                    'priority' => 50,
                ]
            )
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertSame(50, $updatedRule->priority);
        $this->assertFalse($updatedRule->enabled);
        $this->assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateRuleMetadata
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateRuleData
     */
    public function testUpdateRuleMetadataWithDefaultValues(): void
    {
        $updatedRule = $this->handler->updateRuleMetadata(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED),
            new RuleMetadataUpdateStruct()
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertSame(5, $updatedRule->priority);
        $this->assertTrue($updatedRule->enabled);
        $this->assertSame(Value::STATUS_PUBLISHED, $updatedRule->status);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::copyRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCopyRule(): void
    {
        $copiedRule = $this->handler->copyRule(
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED)
        );

        $this->assertInstanceOf(Rule::class, $copiedRule);
        $this->assertSame(13, $copiedRule->id);
        $this->assertSame(2, $copiedRule->layoutId);
        $this->assertSame(5, $copiedRule->priority);
        $this->assertTrue($copiedRule->enabled);
        $this->assertNull($copiedRule->comment);
        $this->assertSame(Value::STATUS_PUBLISHED, $copiedRule->status);

        $this->assertSame(
            [
                [
                    'id' => 21,
                    'status' => Value::STATUS_PUBLISHED,
                    'ruleId' => $copiedRule->id,
                    'type' => 'route_prefix',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 22,
                    'status' => Value::STATUS_PUBLISHED,
                    'ruleId' => $copiedRule->id,
                    'type' => 'route_prefix',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($copiedRule)
            )
        );

        $this->assertSame(
            [
                [
                    'id' => 5,
                    'status' => Value::STATUS_PUBLISHED,
                    'ruleId' => $copiedRule->id,
                    'type' => 'my_condition',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($copiedRule)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::createRuleStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::createRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleConditionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::loadRuleTargetsData
     */
    public function testCreateRuleStatus(): void
    {
        $copiedRule = $this->handler->createRuleStatus(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            Value::STATUS_ARCHIVED
        );

        $this->assertInstanceOf(Rule::class, $copiedRule);

        $this->assertSame(3, $copiedRule->id);
        $this->assertSame(3, $copiedRule->layoutId);
        $this->assertSame(7, $copiedRule->priority);
        $this->assertTrue($copiedRule->enabled);
        $this->assertNull($copiedRule->comment);
        $this->assertSame(Value::STATUS_ARCHIVED, $copiedRule->status);

        $this->assertSame(
            [
                [
                    'id' => 5,
                    'status' => Value::STATUS_ARCHIVED,
                    'ruleId' => 3,
                    'type' => 'route',
                    'value' => 'my_fourth_cool_route',
                ],
                [
                    'id' => 6,
                    'status' => Value::STATUS_ARCHIVED,
                    'ruleId' => 3,
                    'type' => 'route',
                    'value' => 'my_fifth_cool_route',
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleTargets($copiedRule)
            )
        );

        $this->assertSame(
            [
                [
                    'id' => 2,
                    'status' => Value::STATUS_ARCHIVED,
                    'ruleId' => 3,
                    'type' => 'route_parameter',
                    'value' => [
                        'parameter_name' => 'some_param',
                        'parameter_values' => [3, 4],
                    ],
                ],
                [
                    'id' => 3,
                    'status' => Value::STATUS_ARCHIVED,
                    'ruleId' => 3,
                    'type' => 'route_parameter',
                    'value' => [
                        'parameter_name' => 'some_other_param',
                        'parameter_values' => [5, 6],
                    ],
                ],
            ],
            $this->exportObjectList(
                $this->handler->loadRuleConditions($copiedRule)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "3"
     */
    public function testDeleteRule(): void
    {
        $this->handler->deleteRule(3);

        $this->handler->loadRule(3, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRule
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleConditions
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteRuleTargets
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "5"
     */
    public function testDeleteRuleInOneStatus(): void
    {
        $this->handler->deleteRule(5, Value::STATUS_DRAFT);

        // First, verify that NOT all rule statuses are deleted
        try {
            $this->handler->loadRule(5, Value::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the rule in draft status deleted other/all statuses.');
        }

        $this->handler->loadRule(5, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::addTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addTarget
     */
    public function testAddTarget(): void
    {
        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->type = 'target';
        $targetCreateStruct->value = '42';

        $target = $this->handler->addTarget(
            $this->handler->loadRule(1, Value::STATUS_PUBLISHED),
            $targetCreateStruct
        );

        $this->assertInstanceOf(Target::class, $target);

        $this->assertSame(
            [
                'id' => 21,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 1,
                'type' => 'target',
                'value' => '42',
            ],
            $this->exportObject($target)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateTarget
     */
    public function testUpdateTarget(): void
    {
        $targetUpdateStruct = new TargetUpdateStruct();
        $targetUpdateStruct->value = 'my_new_route';

        $target = $this->handler->updateTarget(
            $this->handler->loadTarget(1, Value::STATUS_PUBLISHED),
            $targetUpdateStruct
        );

        $this->assertInstanceOf(Target::class, $target);

        $this->assertSame(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 1,
                'type' => 'route',
                'value' => 'my_new_route',
            ],
            $this->exportObject($target)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteTarget
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteTarget
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find target with identifier "2"
     */
    public function testDeleteTarget(): void
    {
        $target = $this->handler->loadTarget(2, Value::STATUS_PUBLISHED);

        $this->handler->deleteTarget($target);

        $this->handler->loadTarget(2, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::addCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::addCondition
     */
    public function testAddCondition(): void
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'condition';
        $conditionCreateStruct->value = ['param' => 'value'];

        $condition = $this->handler->addCondition(
            $this->handler->loadRule(3, Value::STATUS_PUBLISHED),
            $conditionCreateStruct
        );

        $this->assertInstanceOf(Condition::class, $condition);

        $this->assertSame(
            [
                'id' => 5,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 3,
                'type' => 'condition',
                'value' => ['param' => 'value'],
            ],
            $this->exportObject($condition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::updateCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::updateCondition
     */
    public function testUpdateCondition(): void
    {
        $conditionUpdateStruct = new ConditionUpdateStruct();
        $conditionUpdateStruct->value = ['new_param' => 'new_value'];

        $condition = $this->handler->updateCondition(
            $this->handler->loadCondition(1, Value::STATUS_PUBLISHED),
            $conditionUpdateStruct
        );

        $this->assertInstanceOf(Condition::class, $condition);

        $this->assertSame(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 2,
                'type' => 'route_parameter',
                'value' => ['new_param' => 'new_value'],
            ],
            $this->exportObject($condition)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::deleteCondition
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::deleteCondition
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find condition with identifier "2"
     */
    public function testDeleteCondition(): void
    {
        $this->handler->deleteCondition(
            $this->handler->loadCondition(2, Value::STATUS_PUBLISHED)
        );

        $this->handler->loadCondition(2, Value::STATUS_PUBLISHED);
    }
}
