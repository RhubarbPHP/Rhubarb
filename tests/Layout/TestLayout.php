<?php

namespace Rhubarb\Crown\Tests\Layout;

use Rhubarb\Crown\Layout\Layout;

class TestLayout extends Layout
{
	protected function printLayout( $content )
	{
		?>Top<?=$content;?>Tail<?php
	}
}