<?php

namespace Honeybee\Ui\ViewTemplate\Part;

use Honeybee\Common\Error\RuntimeError;
use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueCollectionInterface;

class NamedItemList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return NamedItemInterface::CLASS;
    }

    public function getByName($name)
    {
        foreach ($this->items as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }

        throw new RuntimeError('Item with name not found: ' . $name);
    }
}
