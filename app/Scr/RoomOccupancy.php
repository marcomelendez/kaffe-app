<?php

namespace App\Scr;

class RoomOccupancy
{
    protected $adults = [];

    protected array $children = [];

    /**
     * @param array $adults
     * @param array $child
     */
    public function __construct(array $adults = [34,34], array $child = [])
    {
        $this->adults = $adults;
        $this->children = $child;
    }

    public function setAdults(array $values)
    {
        $this->adults = $values;
    }

    public function getAdults()
    {
        return $this->adults;
    }

    public function setChildren(array $values)
    {
        $this->children = $values;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getTotalAdults(): int
    {
        return count($this->getAdults());
    }

    public function getTotalChildren(): int
    {
        return count($this->getChildren());
    }

    public function getTotalPersons(): int
    {
        return $this->getTotalAdults() + $this->getTotalChildren();
    }
}
