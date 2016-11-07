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
     * @throws \Twig_Error_Loader
     */
    protected function findTemplate($name)
    {
        $name = (string)$name;

        // normalize name
        $name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        // Ensure we have the expected file extension
        $defaultSuffix = $this->getSuffix();
        if (pathinfo($name, PATHINFO_EXTENSION) != $defaultSuffix) {
            ;
            $name .= '.' . $defaultSuffix;
        }

        $this->validateName($name);

        $namespace = '__main__';
        if (isset($name[0]) && '@' == $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                throw new Twig_Error_Loader(sprintf(
                    'Malformed namespaced template name "%s" (expecting "@namespace/template_name").',
                    $name
                ));
            }

            $namespace = substr($name, 1, $pos - 1);

            $name = substr($name, $pos + 1);
        }

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
