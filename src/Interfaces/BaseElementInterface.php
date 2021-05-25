<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

use Bx\Model\Interfaces\CollectionItemInterface;

interface BaseElementInterface extends CollectionItemInterface
{
    /**
     * @return array
     */
    public function toArray(): array;
    /**
     * @param string $key
     * @return mixed
     */
    public function getProp(string $key);
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setProp(string $key, $value);
}
