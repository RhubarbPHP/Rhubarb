<?php

namespace Gcd\Core\Layout\UnitTesting;

use Gcd\Core\Layout\Layout;

class TestLayout extends Layout
{
	protected function PrintLayout( $content )
	{
		?>Top<?=$content;?>Tail<?php
	}
}