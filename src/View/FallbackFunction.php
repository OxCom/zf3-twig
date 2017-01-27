<?php

namespace ZendTwig\View;

use Twig_Function;
use ZendTwig\Extension\Extension;

class FallbackFunction extends Twig_Function
{
    /**
     * @param       $name
     * @param       $callable
     * @param array $options
     */
    public function __construct($name, $callable = null, array $options = [])
    {
        $options = [
            'needs_environment' => true,
            'is_safe'           => ['all'],
        ];

        /**
         * Create callback function for injection of Zend View Helpers
         *
         * @param \Twig_Environment $env
         * @param array             ...$args
         *
         * @return mixed
         */
        $callable = function ($env, ... $args) {
            $plugin = $env->getExtension(Extension::class)
                          ->getRenderer()
                          ->plugin($this->getName());

            $args = empty($args) ? [] : $args;

            return call_user_func_array($plugin, $args);
        };

        parent::__construct($name, $callable, $options);
    }
}
