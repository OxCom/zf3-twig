<?php
namespace ZendTwig\Test;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\Util\ModuleLoader;

include __DIR__ . "/../vendor/autoload.php";

class Bootstrap extends ModuleLoader
{
    /**
     * @var Bootstrap[]
     */
    protected static $instances;

    /**
     * @param array $config
     *
     * @return Bootstrap
     */
    public static function getInstance($config = [])
    {
        if (empty($config)) {
            $config = include(__DIR__ . '/Fixture/config/application.config.php');
        }

        $key = md5(serialize($config));
        if (empty(static::$instances[$key])) {
            static::$instances[$key] = new self($config);
        }

        return static::$instances[$key];
    }

    /**
     * Init bootstrap
     *
     * @param array $config
     *
     * @return Bootstrap
     */
    public static function init($config = [])
    {
        return static::getInstance($config);
    }
}

Bootstrap::init();
