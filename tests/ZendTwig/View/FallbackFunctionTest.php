<?php

namespace ZendTwig\Test\View;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use ZendTwig\Test\Bootstrap;
use ZendTwig\View\TwigStrategy;

class FallbackFunctionTest extends TestCase
{
    public function testBindCallback()
    {
        /**
         * @var Environment $env
         */
        $sm  = Bootstrap::getInstance()->getServiceManager();
        $env = $sm->get(Environment::class);

        /**
         * @var \Twig\Error\LoaderError $extensionSet
         */
        $refEnv = new \ReflectionClass($env);
        $prop   = $refEnv->getProperty('extensionSet');
        $prop->setAccessible(true);
        $extensionSet = $prop->getValue($env);

        $refSet = new \ReflectionClass($extensionSet);
        $prop   = $refSet->getProperty('functionCallbacks');
        $prop->setAccessible(true);
        $functions = $prop->getValue($extensionSet);

        $this->assertNotEmpty($functions);
    }

    public function testNoCallback()
    {
        /**
         * @var Environment $env
         */
        $config = include(__DIR__ . '/../../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../../Fixture/config/fallback/{{,*.}global}.php',
        ];
        $sm  = Bootstrap::getInstance($config)->getServiceManager();
        $env = $sm->get(Environment::class);

        /**
         * @var \Twig\Error\LoaderError $extensionSet
         */
        $refEnv = new \ReflectionClass($env);
        $prop   = $refEnv->getProperty('extensionSet');
        $prop->setAccessible(true);
        $extensionSet = $prop->getValue($env);

        $refSet = new \ReflectionClass($extensionSet);
        $prop   = $refSet->getProperty('functionCallbacks');
        $prop->setAccessible(true);
        $functions = $prop->getValue($extensionSet);

        $this->assertEmpty($functions);
    }

    public function testRenderWithFallback()
    {
        $model    = new \Laminas\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate('View/testRenderWithFallback');

        /**
         * @var \Laminas\View\View $view
         */
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $strategyTwig = $sm->get(TwigStrategy::class);
        $view         = $sm->get('View');
        $request      = $sm->get('Request');
        $response     = $sm->get('Response');

        $e = $view->getEventManager();
        $strategyTwig->attach($e, 100);

        $view->setEventManager($e)
            ->setRequest($request)
            ->setResponse($response)
            ->render($model);

        $result = $view->getResponse()
            ->getContent();

        $this->assertEquals(0, strpos($result, '<!DOCTYPE html>'), "Fallback for ZF Helpers was not injected");
    }

    /**
     * @dataProvider generatorFallbackToZendHelpers
     * @param $template
     * @param $expected
     */
    public function testFallbackToZendHelpers($template, $expected)
    {
        $model    = new \Laminas\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate($template);

        /**
         * @var \Laminas\View\View $view
         */
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $strategyTwig = $sm->get(TwigStrategy::class);
        $view         = $sm->get('View');
        $request      = $sm->get('Request');
        $response     = $sm->get('Response');

        $e = $view->getEventManager();
        $strategyTwig->attach($e, 100);

        $view->setEventManager($e)
            ->setRequest($request)
            ->setResponse($response)
            ->render($model);

        $result = $view->getResponse()
            ->getContent();

        $this->assertEquals($expected, html_entity_decode($result));
    }

    /**
     * @return array
     */
    public static function generatorFallbackToZendHelpers()
    {
        return [
            [
                'Helpers/basePath',
                "/css/app.css\n"
            ],
            [
                'Helpers/headMeta',
                "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n"
                . "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n"
            ],
            [
                'Helpers/headTitle',
                "<title>Test passed</title>\n"
            ],
            [
                'Helpers/docType',
                "<!DOCTYPE html>\n"
            ],
        ];
    }

    public function testFallbackToNotExistsZendHelpers()
    {
        $model    = new \Laminas\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate('Helpers/NotExists');

        /**
         * @var \Laminas\View\View $view
         */
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $strategyTwig = $sm->get(TwigStrategy::class);
        $view         = $sm->get('View');
        $request      = $sm->get('Request');
        $response     = $sm->get('Response');

        $e = $view->getEventManager();
        $strategyTwig->attach($e, 100);

        $this->expectException(\Twig\Error\SyntaxError::class);

        $view->setEventManager($e)
            ->setRequest($request)
            ->setResponse($response)
            ->render($model);
    }
}
