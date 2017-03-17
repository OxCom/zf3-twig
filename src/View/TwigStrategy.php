<?php

namespace ZendTwig\View;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\View\Renderer\RendererInterface;
use Zend\View\ViewEvent;

class TwigStrategy implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var bool
     */
    protected $forceRender = false;

    /**
     * TwigStrategy constructor.
     *
     * @param \Zend\View\Renderer\RendererInterface $renderer
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
     * @param \Zend\View\ViewEvent $e
     *
     * @return \Zend\View\Renderer\RendererInterface|null
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

        return null;
    }

    /**
     * @param \Zend\View\ViewEvent $e
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
