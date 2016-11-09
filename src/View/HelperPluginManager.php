<?php

namespace ZendTwig\View;

class HelperPluginManager extends \Zend\View\HelperPluginManager
{
    /**
     * Default aliases
     *
     * @var string[]
     */
    protected $aliases = [
        'flashmessenger'   => \Zend\Mvc\Plugin\FlashMessenger\FlashMessenger::class,
        'doctype'          => \Zend\View\Helper\Doctype::class,
        'declarevars'      => \Zend\View\Helper\DeclareVars::class,
        'htmlflash'        => \Zend\View\Helper\HtmlFlash::class,
        'htmllist'         => \Zend\View\Helper\HtmlList::class,
        'htmlobject'       => \Zend\View\Helper\HtmlObject::class,
        'htmlpage'         => \Zend\View\Helper\HtmlPage::class,
        'htmlquicktime'    => \Zend\View\Helper\HtmlQuicktime::class,
        'layout'           => \Zend\View\Helper\Layout::class,
        'renderchildmodel' => \Zend\View\Helper\RenderChildModel::class,
    ];
    /**
     * Default factories
     *
     * @var string[]
     */
    protected $factories = [
        \Zend\Mvc\Plugin\FlashMessenger\FlashMessenger::class => \Zend\View\Helper\Service\FlashMessengerFactory::class,
        \Zend\View\Helper\Doctype::class                      => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\DeclareVars::class                  => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\HtmlFlash::class                    => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\HtmlList::class                     => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\HtmlObject::class                   => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\HtmlPage::class                     => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\HtmlQuicktime::class                => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\Layout::class                       => \Zend\ServiceManager\Factory\InvokableFactory::class,
        \Zend\View\Helper\RenderChildModel::class             => \Zend\ServiceManager\Factory\InvokableFactory::class,
    ];
}
