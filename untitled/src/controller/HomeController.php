<?php

namespace Classes\controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController{
    public function hello(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{
        $response->getBody()->write("Hello World!");
        return $response;
    }

}