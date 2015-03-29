<?php

namespace AI\Tester\Strategy;

class Randomizer
{
    /**
     * @var array
     */
    protected $variants = [];

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
     */
    public function addVariants(array $variants)
    {
        foreach ($variants as $variant) {
            $this->addVariant($variant['item'], $variant['priority']);
        }
    }

    /**
     * @return mixed
     */
    public function getRandomVariant()
    {
        return array_rand($this->variants);
    }
}
