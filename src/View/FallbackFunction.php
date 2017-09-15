<?php

namespace ZendTwig\View;

use Twig_Function;
use ZendTwig\Extension\Extension;

class FallbackFunction
{
    /**
     * @param       $name
     *
     * @return \Twig_Function
     */
    public static function build($name)
    {
        /**
         * Create callback function for injection of Zend View Helpers
         *
         * @param \Twig_Environment $env
         * @param array             ...$args
         *
         * @return mixed
         */
        $callable = function ($env, ... $args) use ($name) {
            /**
             * @var \ZendTwig\Extension\Extension $extension
             */
            $extension = $env->getExtension(Extension::class);
            $plugin = $extension->getRenderer()
                                ->plugin($name);

            if (is_callable($plugin)) {
                // helper should implement __invoke() function
                $args = empty($args) ? [] : $args;
                return call_user_func_array($plugin, $args);
            } else {
                return $plugin;
            }
        };

        $options = [
            'needs_environment' => true,
            'is_safe'           => ['all'],
        ];

        return new Twig_Function($name, $callable, $options);
    }
}
