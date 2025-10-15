<?php

namespace ZendTwig\Renderer;

use Twig\Environment;

use Twig\Loader\ChainLoader;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\ViewModel;
use ZendTwig\Resolver\TwigResolver;
use ZendTwig\View\HelperPluginManager as TwigHelperPluginManager;

use Laminas\View\Exception\DomainException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\HelperPluginManager as ZendHelperPluginManager;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Renderer\RendererInterface;
use Laminas\View\Renderer\TreeRendererInterface;
use Laminas\View\Resolver\ResolverInterface;
use Laminas\View\View;
use ZendTwig\View\TwigModel;

class TwigRenderer implements RendererInterface, TreeRendererInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var ChainLoader
     */
    protected $loader;

    /**
     * @var View
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
     * Force \Laminas\View\Model\ViewModel::$terminate to true
     * @var bool
     */
    protected $forceStandalone = false;

    /**
     * @param View              $view
     * @param Environment       $env
     * @param ResolverInterface $resolver
     */
    public function __construct(View $view, ?Environment $env = null, ?ResolverInterface $resolver = null)
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
     * @return AbstractHelper|callable
     */
    public function plugin($name, ?array $options = null)
    {
        $helper = $this->getTwigHelpers()->setRenderer($this);

        if ($helper->has($name)) {
            return $helper->get($name, $options);
        }

        return $this->getHelperPluginManager()->get($name, $options);
    }

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return TwigRenderer
     */
    public function getEngine() : TwigRenderer
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
    public function render($nameOrModel, $values = null) : string
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

            /**
             * Give view model awareness via ViewModel helper
             * @var \Laminas\View\Helper\ViewModel $helper
             */
            $helper = $this->plugin(ViewModel::class);
            $helper->setCurrent($model);

            $values = $model->getVariables();
        }

        if (!$this->canRender($nameOrModel)) {
            throw new RuntimeException(sprintf(
                '%s: Unable to render template "%s"; resolver could not resolve to a file',
                __METHOD__,
                $nameOrModel
            ));
        }

        if ($model instanceof ModelInterface && $model->hasChildren() && $this->canRenderTrees($model)) {
            if (!isset($values[$model->captureTo()])) {
                $values[$model->captureTo()] = '';
            }

            foreach ($model->getChildren() as $child) {
                /**
                 * @var \Laminas\View\Model\ViewModel $child
                 * @var \Twig\Template             $template
                 */
                try {
                    $result = $this->render($child, $values);
                } catch (RuntimeException $e) {
                    continue;
                }

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

        /** @var \Twig\Template $template */
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
     * @param mixed $model
     *
     * @return bool
     */
    public function canRenderTrees($model = null)
    {
        if (!empty($model) && $model instanceof TwigModel) {
            return true;
        }

        return false;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     *
     * @return TwigRenderer
     */
    public function setView(View $view) : TwigRenderer
    {
        $this->view = $view;

        $view->getEventManager();

        return $this;
    }

    /**
     * @return Environment
     */
    public function getEnvironment() : Environment
    {
        if (!$this->environment instanceof Environment) {
            throw new InvalidArgumentException(sprintf(
                'Twig environment must be \Twig\Environment; got type "%s" instead',
                (is_object($this->loader) ? get_class($this->loader) : gettype($this->loader))
            ));
        }

        return $this->environment;
    }

    /**
     * @param Environment $environment
     *
     * @return TwigRenderer
     */
    public function setEnvironment($environment) : TwigRenderer
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return ChainLoader
     */
    public function getLoader() : ChainLoader
    {
        if (!$this->loader instanceof ChainLoader) {
            throw new InvalidArgumentException(sprintf(
                'Twig loader must implement \Twig\Loader\ChainLoader; got type "%s" instead',
                (is_object($this->loader) ? get_class($this->loader) : gettype($this->loader))
            ));
        }

        return $this->loader;
    }

    /**
     * @param \Twig\Loader\LoaderInterface $loader
     *
     * @return TwigRenderer
     */
    public function setLoader($loader) : TwigRenderer
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @return TwigResolver
     */
    public function getResolver() : TwigResolver
    {
        return $this->resolver;
    }

    /**
     * @param ResolverInterface|TwigResolver $resolver
     *
     * @return TwigRenderer
     */
    public function setResolver(?ResolverInterface $resolver = null) : TwigRenderer
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * @return TwigHelperPluginManager
     */
    public function getTwigHelpers() : TwigHelperPluginManager
    {
        return $this->twigHelpers;
    }

    /**
     * @param TwigHelperPluginManager $twigHelpers
     *
     * @return TwigRenderer
     */
    public function setTwigHelpers($twigHelpers) : TwigRenderer
    {
        $this->twigHelpers = $twigHelpers;

        return $this;
    }

    /**
     * @return \Laminas\View\HelperPluginManager
     */
    public function getHelperPluginManager() : ZendHelperPluginManager
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
    public function setZendHelpers(ZendHelperPluginManager $helpers) : TwigRenderer
    {
        $this->zendHelpers = $helpers;

        return $this;
    }

    /**
     * @param boolean $forceStandalone
     * @return TwigRenderer
     */
    public function setForceStandalone($forceStandalone) : TwigRenderer
    {
        $this->forceStandalone = !!$forceStandalone;
        return $this;
    }

    /**
     * @return bool
     */
    public function isForceStandalone() : bool
    {
        return $this->forceStandalone;
    }
}
