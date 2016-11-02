<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;

class LayoutResolverMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper $layoutMapper
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface $conditionTypeRegistry
     */
    public function __construct(
        Handler $persistenceHandler,
        LayoutMapper $layoutMapper,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        parent::__construct($persistenceHandler);

        $this->layoutMapper = $layoutMapper;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Builds the API rule value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function mapRule(PersistenceRule $rule)
    {
        $handler = $this->persistenceHandler->getLayoutResolverHandler();

        $layout = null;
        try {
            // Layouts used by rule are always in published status
            $layout = $this->persistenceHandler->getLayoutHandler()->loadLayout(
                $rule->layoutId,
                Value::STATUS_PUBLISHED
            );

            $layout = $this->layoutMapper->mapLayout($layout);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $persistenceTargets = $handler->loadRuleTargets($rule);

        $targets = array();
        foreach ($persistenceTargets as $persistenceTarget) {
            $targets[] = $this->mapTarget($persistenceTarget);
        }

        $persistenceConditions = $handler->loadRuleConditions($rule);

        $conditions = array();
        foreach ($persistenceConditions as $persistenceCondition) {
            $conditions[] = $this->mapCondition($persistenceCondition);
        }

        $ruleData = array(
            'id' => $rule->id,
            'status' => $rule->status,
            'layout' => $layout,
            'enabled' => $rule->enabled,
            'priority' => $rule->priority,
            'comment' => $rule->comment,
            'targets' => $targets,
            'conditions' => $conditions,
        );

        return new Rule($ruleData);
    }

    /**
     * Builds the API target value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function mapTarget(PersistenceTarget $target)
    {
        $targetData = array(
            'id' => $target->id,
            'status' => $target->status,
            'ruleId' => $target->ruleId,
            'targetType' => $this->targetTypeRegistry->getTargetType(
                $target->type
            ),
            'value' => $target->value,
        );

        return new Target($targetData);
    }

    /**
     * Builds the API condition value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function mapCondition(PersistenceCondition $condition)
    {
        $conditionData = array(
            'id' => $condition->id,
            'status' => $condition->status,
            'ruleId' => $condition->ruleId,
            'conditionType' => $this->conditionTypeRegistry->getConditionType(
                $condition->type
            ),
            'value' => $condition->value,
        );

        return new Condition($conditionData);
    }
}
