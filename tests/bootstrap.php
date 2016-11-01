<?php
namespace ZendTwig\Test;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\Util\ModuleLoader;

include __DIR__ . "/../vendor/autoload.php";

class Bootstrap extends ModuleLoader
{
    /**
     * @var Bootstrap
     */
    protected static $bootstrap;

    /**
     * @return Bootstrap
     */
    public static function getInstance()
    {
        if (empty(static::$bootstrap)) {
            $config = include(__DIR__ . '/Fixture/config/application.config.php');

            static::$bootstrap = new self($config);
        }

        return static::$bootstrap;
    }

    /**
     * Init bootstrap
     */
    public static function init()
    {
        static::getInstance();
    }
}

Bootstrap::init();
