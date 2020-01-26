<?php

namespace ZendTwig\Loader;

use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class MapLoader implements LoaderInterface
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
     * @throws \Twig\Error\LoaderError
     * @return MapLoader
     */
    public function add($name, $path) : MapLoader
    {
        if ($this->exists($name)) {
            throw new LoaderError(sprintf(
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
    public function exists($name) : bool
    {
        return array_key_exists($name, $this->map);
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheKey($name) : string
    {
        return $name;
    }

    /**
     * {@inheritDoc}
     */
    public function isFresh($name, $time) : bool
    {
        if (!$this->exists($name)) {
            throw new LoaderError(sprintf(
                'Unable to find template "%s" from template map',
                $name
            ));
        }

        if (!file_exists($this->map[$name])) {
            throw new LoaderError(sprintf(
                'Unable to open file "%s" from template map',
                $this->map[$name]
            ));
        }

        return filemtime($this->map[$name]) <= $time;
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @param string $name The template logical name
     *
     * @return Source
     *
     * @throws LoaderError When $name is not found
     */
    public function getSourceContext($name) : Source
    {
        if (!$this->exists($name)) {
            throw new LoaderError(sprintf(
                'Unable to find template "%s" from template map',
                $name
            ));
        }

        if (!file_exists($this->map[$name])) {
            throw new LoaderError(sprintf(
                'Unable to open file "%s" from template map',
                $this->map[$name]
            ));
        }

        $content = file_get_contents($this->map[$name]);
        $source = new Source($content, $name, $this->map[$name]);

        return $source;
    }
}
