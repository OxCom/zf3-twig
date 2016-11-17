<?php

namespace ZendTwig\Loader;

use Twig_Error_Loader;
use Twig_Loader_Filesystem;

class StackLoader extends Twig_Loader_Filesystem
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
    public function setSuffix($suffix)
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
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $name
     *
     * @return string
     * @throws Twig_Error_Loader
     */
    protected function findTemplate($name)
    {
        $name = $this->normalizeName((string)$name);

        // Ensure we have the expected file extension
        $defaultSuffix = $this->getSuffix();
        if (pathinfo($name, PATHINFO_EXTENSION) != $defaultSuffix) {
            $name .= '.' . $defaultSuffix;
        }

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $this->validateName($name);

        list($namespace, $name) = $this->parseName($name);

        if (!isset($this->paths[$namespace])) {
            throw new Twig_Error_Loader(sprintf('There are no registered paths for namespace "%s".', $namespace));
        }

        foreach ($this->paths[$namespace] as $path) {
            if (is_file($path . '/' . $name)) {
                return $this->cache[$name] = $path . '/' . $name;
            }
        }

        throw new Twig_Error_Loader(sprintf(
            'Unable to find template "%s" (looked into: %s).',
            $name,
            implode(', ', $this->paths[$namespace])
        ));
    }
}
