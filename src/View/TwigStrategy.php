<?php

namespace ZendTwig\View;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\View\Renderer\RendererInterface;
use Laminas\View\ViewEvent;
use ZendTwig\Renderer\TwigRenderer;

class TwigStrategy implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var \Laminas\View\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var bool
     */
    protected $forceRender = false;

    /**
     * TwigStrategy constructor.
     *
     * @param \Laminas\View\Renderer\RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, [$this, 'selectRender'], $priority);
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, [$this, 'injectResponse'], $priority);
    }

    /**
     * @param \Laminas\View\ViewEvent $e
     *
     * @return \Laminas\View\Renderer\RendererInterface|null
     */
    public function selectRender(ViewEvent $e)
    {
        if ($this->isForceRender()) {
            return $this->renderer;
        }

        $model = $e->getModel();
        if ($model instanceof TwigModel) {
            return $this->renderer;
        }

        if ($this->renderer instanceof TwigRenderer) {
            try {
                $tpl = $this->renderer->getResolver()->resolve($model->getTemplate());
                if ($tpl instanceof \Twig\Template) {
                    return $this->renderer;
                }
            } catch (\Throwable $e) {
            }
        }

        return null;
    }

    /**
     * @param \Laminas\View\ViewEvent $e
     */
    public function injectResponse(ViewEvent $e)
    {
        if ($this->renderer !== $e->getRenderer()) {
            return;
        }

        $result   = $e->getResult();
        $response = $e->getResponse();

        $response->setContent($result);
    }

    /**
     * @param bool $forceRender
     *
     * @return TwigStrategy
     */
    public function setForceRender(bool $forceRender) : TwigStrategy
    {
        $this->forceRender = $forceRender;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForceRender() : bool
    {
        return $this->forceRender;
    }
}
