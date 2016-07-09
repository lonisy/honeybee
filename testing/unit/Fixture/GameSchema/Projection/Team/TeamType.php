<?php

namespace Honeybee\Tests\Fixture\GameSchema\Projection\Team;

use Honeybee\Tests\Fixture\GameSchema\Projection\ProjectionType;
use Trellis\EntityType\Attribute\Text\TextAttribute;
use Workflux\StateMachine\StateMachineInterface;

class TeamType extends ProjectionType
{
    public function __construct(StateMachineInterface $state_machine)
    {
        $this->workflow_state_machine = $state_machine;
        parent::__construct(
            'Team',
            [
                new TextAttribute('name', $this, [ 'mandatory' => true ])
            ],
            [ 'is_hierarchical' => true ]
        );
    }

    public function getEntityImplementor()
    {
        return Team::CLASS;
    }
}
