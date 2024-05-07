<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'vendor/autoload.php';
require 'config.php';

$app = AppFactory::create();
//1 getById
$app->get('/api/feedback/{id}', Classes\HomeController::class . ':feedbackById');
//2 getByPage
$app->get('/api/feedbacks/{page}', Classes\HomeController::class . ':feedbacks');
//3 delete
$app->get('/api/delete/{id}', Classes\HomeController::class . ':deleteById');
//4 create
$app->post('/api/create', Classes\HomeController::class . ':createFeedback');
//0 hello
$app->get('/', Classes\HomeController::class . ':hello');

$app->addErrorMiddleware(true, true, true);

$app->run();





