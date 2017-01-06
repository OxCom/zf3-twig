<?php

namespace ZendTwig\Loader;

use Twig_Error_Loader;
use Twig_LoaderInterface;
use Twig_Source;

class MapLoader implements Twig_LoaderInterface
{
    /**
     * Array of templates to filenames.
     * @var array
     */
    protected $map = [];

    /**
     * Add to the map.
     *
     * @param string $name
     * @param string $path
     *
     * @throws \Twig_Error_Loader
     * @return MapLoader
     */
    public function add($name, $path)
    {
        if ($this->exists($name)) {
            throw new Twig_Error_Loader(sprintf(
                'Name "%s" already exists in map',
                $name
            ));
        }
        $this->map[$name] = $path;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->map);
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheKey($name)
    {
        return $name;
    }

    /**
     * {@inheritDoc}
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->map[$name]) <= $time;
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @param string $name The template logical name
     *
     * @return Twig_Source
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSourceContext($name)
    {
        if (!$this->exists($name)) {
            throw new Twig_Error_Loader(sprintf(
                'Unable to find template "%s" from template map',
                $name
            ));
        }
        if (!file_exists($this->map[$name])) {
            throw new Twig_Error_Loader(sprintf(
                'Unable to open file "%s" from template map',
                $this->map[$name]
            ));
        }

        $source = new \Twig_Source(
            file_get_contents($this->map[$name]),
            $name,
            $this->map[$name]
        );

        return $source;
    }
}
