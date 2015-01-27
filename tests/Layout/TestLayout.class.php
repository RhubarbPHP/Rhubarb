<?php

namespace Rhubarb\Crown\Layout\UnitTesting;

use Rhubarb\Crown\Layout\Layout;

class TestLayout extends Layout
{
	protected function PrintLayout( $content )
	{
		?>Top<?=$content;?>Tail<?php
	}
}