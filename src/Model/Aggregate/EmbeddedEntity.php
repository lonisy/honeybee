<?php

namespace Honeybee\Model\Aggregate;

use Trellis\Common\ObjectInterface;
use Honeybee\Model\Event\EmbeddedEntityEventInterface;
use Honeybee\Common\Error\RuntimeError;
use Honeybee\Model\Event\EmbeddedEntityEventList;
use Honeybee\Model\Command\EmbeddedEntityTypeCommandInterface;
use Honeybee\Model\Task\ModifyAggregateRoot\ModifyEmbeddedEntity\ModifyEmbeddedEntityCommand;
use Honeybee\Model\Task\ModifyAggregateRoot\ModifyEmbeddedEntity\EmbeddedEntityModifiedEvent;
use Honeybee\Model\Task\ModifyAggregateRoot\AddEmbeddedEntity\AddEmbeddedEntityCommand;
use Honeybee\Model\Task\ModifyAggregateRoot\AddEmbeddedEntity\EmbeddedEntityAddedEvent;
use Honeybee\Model\Task\ModifyAggregateRoot\RemoveEmbeddedEntity\RemoveEmbeddedEntityCommand;
use Honeybee\Model\Task\ModifyAggregateRoot\RemoveEmbeddedEntity\EmbeddedEntityRemovedEvent;
use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;

abstract class EmbeddedEntity extends Entity
{
    public function applyEvent(EmbeddedEntityEventInterface $event, $auto_commit = true)
    {
        $class_parts = explode('\\', get_class($event));
        $class_name = array_pop($class_parts);
        $callback_func = 'on' . preg_replace('~Event$~', '', $class_name);

        if (is_callable([$this, $callback_func])) {
            $source_event = $this->$callback_func($event);
            $this->markClean();
            return $source_event;
        } else {
            throw new RuntimeError(
                sprintf(
                    'Unsupported domain event-type given. Supported default event-types are: %s.',
                    implode(
                        ', ',
                        [ EmbeddedEntityAddedEvent::CLASS, EmbeddedEntityModifiedEvent::CLASS, EmbeddedEntityRemovedEvent::CLASS ]
                    )
                )
            );
        }
    }

    protected function onEmbeddedEntityAdded(EmbeddedEntityAddedEvent $event)
    {
        return $this->applyValues($event);
    }

    protected function onEmbeddedEntityModified(EmbeddedEntityModifiedEvent $event)
    {
        return $this->applyValues($event);
    }

    protected function onEmbeddedEntityRemoved(EmbeddedEntityRemovedEvent $event)
    {
        return $event;
    }

    protected function applyValues(EmbeddedEntityEventInterface $event)
    {
        if (!is_callable([$event, 'getData'])) {
            throw new RuntimeError('Event instance is not support due to lack of a getData method.');
        }
        $this->setValues($event->getData());
        if (!$this->isValid()) {
            throw new RuntimeError('Corrupt event data given.');
        }

        $embedded_entity_events = new EmbeddedEntityEventList();
        foreach ($event->getEmbeddedEntityEvents() as $embedded_event) {
            $embedded_entity_events->push($this->applyEmbeddedEntityEvent($embedded_event));
        }

        $source_event = $event;
        $recorded_changes = $this->getRecordedChanges();
        if (!empty($recorded_changes)) {
            $source_event = $event->createCopyWith(
                [ 'data' => $recorded_changes, 'embedded_entity_events' => $embedded_entity_events ]
            );
        }

        return $source_event;
    }
}
