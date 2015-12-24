<?php

namespace Rhubarb\Crown\Tests\Fixtures\Layout;

use Rhubarb\Crown\Layout\Layout;

class TestLayout extends Layout
{
    protected function printLayout($content)
    {
        ?>Top<?= $content; ?>Tail<?php
    }
}