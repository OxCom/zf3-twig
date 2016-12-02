<?php

namespace ZendTwig\Renderer;

use Twig_Environment;
use Twig_Loader_Chain;

use ZendTwig\Resolver\TwigResolver;
use ZendTwig\View\HelperPluginManager as TwigHelperPluginManager;

use Zend\View\Exception\DomainException;
use Zend\View\Exception\InvalidArgumentException;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager as ZendHelperPluginManager;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\RendererInterface;
use Zend\View\Renderer\TreeRendererInterface;
use Zend\View\Resolver\ResolverInterface;
use Zend\View\View;

class TwigRenderer implements RendererInterface, TreeRendererInterface
{
    /**
     * @var bool
     */
    protected $canRenderTrees = true;

    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * @var Twig_Loader_Chain
     */
    protected $loader;

    /**
     * @var \Zend\View\View
     */
    protected $view;

    /**
     * @var TwigResolver
     */
    protected $resolver;

    /**
     * Helper plugin manager
     *
     * @var TwigHelperPluginManager
     */
    private $twigHelpers;

    /**
     * Helper plugin manager
     *
     * @var ZendHelperPluginManager
     */
    private $zendHelpers;

    /**
     * Force \Zend\View\Model\ViewModel::$terminate to true
     * @var bool
     */
    protected $forceStandalone = false;

    /**
     * @param \Zend\View\View   $view
     * @param Twig_Environment  $env
     * @param ResolverInterface $resolver
     */
    public function __construct(View $view, Twig_Environment $env = null, ResolverInterface $resolver = null)
    {
        $this->setView($view)
             ->setEnvironment($env)
             ->setLoader($env->getLoader())
             ->setResolver($resolver);
    }

    /**
     * Overloading: proxy to helpers
     *
     * Proxies to the attached plugin manager to retrieve, return, and potentially
     * execute helpers.
     *
     * * If the helper does not define __invoke, it will be returned
     * * If the helper does define __invoke, it will be called as a function
     *
     * @param  string $method
     * @param  array  $argv
     *
     * @return mixed
     */
    public function __call($method, $argv)
    {
        $plugin = $this->plugin($method);

        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $argv);
        }

        // @codeCoverageIgnoreStart
        return $plugin;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name    Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     *
     * @return AbstractHelper
     */
    public function plugin($name, array $options = null)
    {
        $helper = $this->getTwigHelpers()->setRenderer($this);

        if ($helper->has($name)) {
            return $helper->get($name, $options);
        }

        return $this->getZendHelpers()->get($name, $options);
    }

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return mixed
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param  string|ModelInterface   $nameOrModel The script/resource process, or a view model
     * @param  null|array|\ArrayAccess $values      Values to use during rendering
     *
     * @return string The script output.
     */
    public function render($nameOrModel, $values = null)
    {
        $model = $nameOrModel;
        if ($model instanceof ModelInterface) {
            $nameOrModel = $model->getTemplate();

            if (empty($nameOrModel)) {
                throw new DomainException(sprintf(
                    '%s: received View Model argument, but template is empty',
                    __METHOD__
                ));
            }

            $options = $model->getOptions();
            $options = empty($options) ? [] : $options;
            foreach ($options as $setting => $value) {
                $method = 'set' . $setting;
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
                unset($method, $setting, $value);
            }

            // Give view model awareness via ViewModel helper
            $helper = $this->plugin('view_model');
            $helper->setCurrent($model);

            $values = $model->getVariables();
        }

        if (!$this->canRender($nameOrModel)) {
            throw new \Zend\View\Exception\RuntimeException(sprintf(
                '%s: Unable to render template "%s"; resolver could not resolve to a file',
                __METHOD__,
                $nameOrModel
            ));
        }

        if ($model instanceof ModelInterface && $model->hasChildren() && $this->canRenderTrees()) {
            if (!isset($values[$model->captureTo()])) {
                $values[$model->captureTo()] = '';
            }

            foreach ($model->getChildren() as $child) {
                /**
                 * @var \Zend\View\Model\ViewModel $child
                 * @var \Twig_Template             $template
                 */
                $result = $this->render($child, $values);

                if ($this->isForceStandalone() || $child->terminate()) {
                    return $result;
                }

                $child->setOption('has_parent', true);

                if ($child->isAppend()) {
                    $values[$child->captureTo()] .= $result;
                } else {
                    $values[$child->captureTo()] = $result;
                }
            }
        }

        /** @var \Twig_Template $template */
        $template = $this->getResolver()->resolve($nameOrModel, $this);

        return $template->render((array)$values);
    }

    /**
     * Can the template be rendered?
     *
     * @param string $name
     *
     * @return bool
     */
    public function canRender($name)
    {
        return $this->getLoader()
                    ->exists($name);
    }

    /**
     * Indicate whether the renderer is capable of rendering trees of view models
     *
     * @return bool
     */
    public function canRenderTrees()
    {
        return $this->canRenderTrees;
    }

    /**
     * @param boolean $canRenderTrees
     *
     * @return TwigRenderer
     */
    public function setCanRenderTrees($canRenderTrees)
    {
        $this->canRenderTrees = !!$canRenderTrees;

        return $this;
    }

    /**
     * @return \Zend\View\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param \Zend\View\View $view
     *
     * @return TwigRenderer
     */
    public function setView(View $view)
    {
        $this->view = $view;

        $view->getEventManager();

        return $this;
    }

    /**
     * @return Twig_Environment
     */
    public function getEnvironment()
    {
        if (!$this->environment instanceof Twig_Environment) {
            throw new InvalidArgumentException(sprintf(
                'Twig environment must be Twig_Environment; got type "%s" instead',
                (is_object($this->loader) ? get_class($this->loader) : gettype($this->loader))
            ));
        }

        return $this->environment;
    }

    /**
     * @param Twig_Environment $environment
     *
     * @return TwigRenderer
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return Twig_Loader_Chain
     */
    public function getLoader()
    {
        if (!$this->loader instanceof Twig_Loader_Chain) {
            throw new InvalidArgumentException(sprintf(
                'Twig loader must implement Twig_Loader_Chain; got type "%s" instead',
                (is_object($this->loader) ? get_class($this->loader) : gettype($this->loader))
            ));
        }

        return $this->loader;
    }

    /**
     * @param Twig_Loader_Chain $loader
     *
     * @return TwigRenderer
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @return TwigResolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * @param ResolverInterface|TwigResolver $resolver
     *
     * @return TwigRenderer
     */
    public function setResolver(ResolverInterface $resolver = null)
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * @return TwigHelperPluginManager
     */
    public function getTwigHelpers()
    {
        return $this->twigHelpers;
    }

    /**
     * @param TwigHelperPluginManager $twigHelpers
     *
     * @return TwigRenderer
     */
    public function setTwigHelpers($twigHelpers)
    {
        $this->twigHelpers = $twigHelpers;

        return $this;
    }

    /**
     * @return ZendHelperPluginManager
     */
    public function getZendHelpers()
    {
        return $this->zendHelpers;
    }

    /**
     * Set helper plugin manager instance
     *
     * @param ZendHelperPluginManager $helpers
     *
     * @return TwigRenderer
     */
    public function setZendHelpers($helpers)
    {
        $this->zendHelpers = $helpers;

        return $this;
    }

    /**
     * @param boolean $forceStandalone
     * @return TwigRenderer
     */
    public function setForceStandalone($forceStandalone)
    {
        $this->forceStandalone = !!$forceStandalone;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isForceStandalone()
    {
        return $this->forceStandalone;
    }
}
