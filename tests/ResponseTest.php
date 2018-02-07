<?php

namespace Tests;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    public function testResponse()
    {
        $json = ['code' => 200, 'msg' => 'SUCCESS', 'data' => []];

        $this->assertJson(
            json_encode($json)
        );
    }
}