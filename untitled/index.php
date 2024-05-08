<?php

use Slim\Factory\AppFactory;

require 'vendor/autoload.php';


$app = AppFactory::create();
//1 getById
$app->get('/api/feedback/{id}', \Classes\controller\FeedbackController::class . ':feedbackById');
//2 getByPage
$app->get('/api/feedbacks/{page}', \Classes\controller\FeedbackController::class . ':feedbacks');
//3 delete
$app->delete('/api/delete/{id}', \Classes\controller\FeedbackController::class . ':deleteById');
//4 create
$app->post('/api/create', \Classes\controller\FeedbackController::class . ':createFeedback');
//0 hello
$app->get('/', \Classes\controller\HomeController::class . ':hello');

$app->addErrorMiddleware(true, true, true);

$app->run();





