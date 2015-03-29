<?php

namespace AI\Tester\Util;

class Randomizer
{
    /**
     * @var array
     */
    protected $variants = [];

    public function reset()
    {
        $this->variants = [];
    }

    /**
     * @param mixed $item
     * @param int $priority
     */
    public function addVariant($item, $priority)
    {
        $array = array_fill(0, $priority, $item);
        $this->variants = array_merge($this->variants, $array);
    }

    /**
     * @param array $variants
     * @param string $itemName
     * @param string $priorityName
     */
    public function addVariants(array $variants, $itemName = 'item', $priorityName = 'priority')
    {
        foreach ($variants as $variant) {
            $this->addVariant($variant[$itemName], $variant[$priorityName]);
        }
    }

    /**
     * @return mixed
     */
    public function getRandomVariant()
    {
        return $this->variants[array_rand($this->variants)];
    }
}
