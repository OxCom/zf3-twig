<?php

namespace ZendTwig\View;

use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\View\Helper\DeclareVars;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\HtmlFlash;
use Laminas\View\Helper\HtmlList;
use Laminas\View\Helper\HtmlObject;
use Laminas\View\Helper\HtmlPage;
use Laminas\View\Helper\HtmlQuicktime;
use Laminas\View\Helper\Layout;
use Laminas\View\Helper\RenderChildModel;
use Laminas\View\Helper\Service\FlashMessengerFactory;

class HelperPluginManager extends \Laminas\View\HelperPluginManager
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
