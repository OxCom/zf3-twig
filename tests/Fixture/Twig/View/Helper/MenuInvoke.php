<?php

namespace ZendTwig\Test\Fixture\View\Helper;

class MenuInvoke extends Menu
{
    public function __invoke()
    {
        return $this;
    }
}
