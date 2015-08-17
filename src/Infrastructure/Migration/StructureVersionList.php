<?php

namespace Honeybee\Infrastructure\Migration;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueCollectionInterface;

class StructureVersionList extends TypedList implements UniqueCollectionInterface
{
    private $identifier;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    protected function getItemImplementor()
    {
        return StructureVersionInterface::CLASS;
    }
}
