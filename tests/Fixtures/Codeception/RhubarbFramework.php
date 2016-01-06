<?php

namespace Rhubarb\Crown\Tests\Fixtures\Codeception;

use Codeception\Lib\Framework;
use Codeception\TestCase;
use PHPUnit_Framework_Assert;

class RhubarbFramework extends Framework
{
    public function _initialize()
    {
        $this->client = new RhubarbConnector();
    }

    public function seeHeader($header, $headerValue=null)
    {
        $response = $this->client->getInternalResponse();
        PHPUnit_Framework_Assert::assertContains($header,$response->getHeaders());

        if ( $headerValue!=null){
            PHPUnit_Framework_Assert::assertEquals($headerValue,$response->getHeader($header));
        }
    }
}