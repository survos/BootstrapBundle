<?php

namespace Survos\BootstrapBundle\Helper;

class ContextHelper extends \ArrayObject
{
    public function getOptions(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setOption(string $name, $value): ContextHelper
    {
        $this->offsetSet($name, $value);

        return $this;
    }


    public function hasOption(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /**
     * @param mixed $default
     * @return mixed|null
     */
    public function getOption(string $name, $default = null)
    {
        return $this->offsetExists($name) ? $this->offsetGet($name) : $default;
    }
}
