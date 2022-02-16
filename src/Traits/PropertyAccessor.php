<?php

declare(strict_types=1);

namespace Base\Vote\Traits;

trait PropertyAccessor
{
    /**
     * @var array
     */
    protected $props = [];

    /**
     * @param array $props
     * @return void
     */
    protected function initProps(array $props)
    {
        $this->props = $props;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getProp(string $key)
    {
        return $this->props[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setProp(string $key, $value)
    {
        $this->props[$key] = $value;
    }

    /**
     * @return array
     */
    protected function getProps(): array
    {
        return $this->props;
    }
}
