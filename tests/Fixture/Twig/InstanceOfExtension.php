<?php

namespace ZendTwig\Test\Fixture\Extension;

use ZendTwig\Extension\AbstractExtension;

class InstanceOfExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            'isInstanceOf' => new \Twig_Function_Method($this, 'isInstanceOf'),
        ];
    }

    /**
     *  Is instance of ...
     *
     * @param string $class
     * @param string $className
     *
     * @return bool
     */
    public function isInstanceOf($class = '', $className = '')
    {
        $reflectionClass = new \ReflectionClass($class);

        return $reflectionClass->isInstance(new $className());
    }
}
