<?php

namespace ZendTwig\View;

use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\View\Helper\DeclareVars;
use Zend\View\Helper\Doctype;
use Zend\View\Helper\HtmlFlash;
use Zend\View\Helper\HtmlList;
use Zend\View\Helper\HtmlObject;
use Zend\View\Helper\HtmlPage;
use Zend\View\Helper\HtmlQuicktime;
use Zend\View\Helper\Layout;
use Zend\View\Helper\RenderChildModel;
use Zend\View\Helper\Service\FlashMessengerFactory;

class HelperPluginManager extends \Zend\View\HelperPluginManager
{
    /**
     * Default aliases
     *
     * @var string[]
     */
    protected $aliases = [
        'flashmessenger'   => FlashMessenger::class,
        'doctype'          => Doctype::class,
        'declarevars'      => DeclareVars::class,
        'htmlflash'        => HtmlFlash::class,
        'htmllist'         => HtmlList::class,
        'htmlobject'       => HtmlObject::class,
        'htmlpage'         => HtmlPage::class,
        'htmlquicktime'    => HtmlQuicktime::class,
        'layout'           => Layout::class,
        'renderchildmodel' => RenderChildModel::class,
    ];
    /**
     * Default factories
     *
     * @var string[]
     */
    protected $factories = [
        FlashMessenger::class   => FlashMessengerFactory::class,
        Doctype::class          => InvokableFactory::class,
        DeclareVars::class      => InvokableFactory::class,
        HtmlFlash::class        => InvokableFactory::class,
        HtmlList::class         => InvokableFactory::class,
        HtmlObject::class       => InvokableFactory::class,
        HtmlPage::class         => InvokableFactory::class,
        HtmlQuicktime::class    => InvokableFactory::class,
        Layout::class           => InvokableFactory::class,
        RenderChildModel::class => InvokableFactory::class,
    ];
}
