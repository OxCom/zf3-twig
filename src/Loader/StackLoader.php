<?php

namespace ZendTwig\Loader;

class StackLoader extends \Twig_Loader_Filesystem
{
    /**
     * Default suffix to use
     *
     * Appends this suffix if the template requested does not use it.
     *
     * @var string
     */
    protected $suffix;

    /**
     * Set default file suffix
     *
     * @param string $suffix
     *
     * @return StackLoader
     */
    public function setSuffix($suffix) : StackLoader
    {
        $this->suffix = (string)$suffix;
        $this->suffix = ltrim($this->suffix, '.');

        return $this;
    }

    /**
     * Get default file suffix
     *
     * @return string
     */
    public function getSuffix() : string
    {
        return $this->suffix;
    }

    /**
     * @{@inheritdoc}
     */
    protected function findTemplate($name, $throw = true)
    {
        // Ensure we have the expected file extension
        $defaultSuffix = $this->getSuffix();
        if (pathinfo($name, PATHINFO_EXTENSION) != $defaultSuffix) {
            $name .= '.' . $defaultSuffix;
        }

        return parent::findTemplate($name, $throw);
    }
}
