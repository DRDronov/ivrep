<?php

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Json;
use Classes\Setup;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

class FirstTest extends TestCase{
    protected $app;

    protected function setUp(): void{
        $this->app = AppFactory::create();
    }

    public function testGetFeedbackById(){


        $data = [
            "id" => 1,
            "author" => "author1",
            "content" => "content1"
        ];
    }

}

