<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'vendor/autoload.php';
require 'config.php';




$app = AppFactory::create();

$setup = new \Classes\Setup();

//1 getById
$app->get('/api/feedback/{id}', function (Request $request, Response $response, $args) use ($setup){
    $id = $args["id"];

    $feedback = $setup->getFeedbackById($id);

    if(!$feedback){
        $response->getBody()->write(json_encode(['error' => 'Feedback not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($feedback));
    return $response->withHeader('Content-Type', 'application/json');
});

//2 getByPage

$app->get('/api/feedbacks/{page}', function (Request $request, Response $response, $args) use ($setup){

    $page = $args["page"];
    $perPage = 20;

    $feedback = $setup->getFeedbacks($page, $perPage);

    $totalFeedbacks = $setup->getFeedbacksLength();

    $totalPages = ceil($totalFeedbacks/$perPage);

    if(!$feedback){
        $response->getBody()->write(json_encode(['error' => 'Feedback not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode(['feedbacks' => $feedback, 'totalPages' => $totalPages]));
    return $response->withHeader('Content-Type', 'application/json');

});

//5 length
$app->get('/api/feedbacksLength', function (Request $request, Response $response) use ($setup){
    $feedbacks = $setup->getFeedbackLength();
    if(!$feedbacks){
        $response->getBody()->write(json_encode(['error' => 'Feedbacks not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($feedbacks));
    return $response->withHeader('Content-Type', 'application/json');
});

//3 delete
$app->get('/api/delete/{id}', function (Request $request, Response $response, $args) use ($setup){

    $id = $args["id"];

    $isAdmin = checkAdminCredentials($request);
    if(!$isAdmin){
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    $feedback = $setup->delete($id);

    if(!$feedback){
        $response->getBody()->write(json_encode(['error' => 'Delete failed']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode("Delete is successful"));
    return $response->withHeader('Content-Type', 'application/json');

});

//4 create

$app->post('/api/create', function (Request $request, Response $response) use ($setup){
    $data = json_decode($request->getBody()->getContents(), true);

    $result = $setup->create($data);

    if(isset($result['error'])){
        $response->getBody()->write(json_encode(['error' => $result['error']]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(['success' => $result['success']]));
    return $response->withHeader('Content-Type', 'application/json');
});


function checkAdminCredentials($request){
    $config = include('config.php');
    $auth = $request->getHeaderLine('Authorization');
    if($auth){
        list($type, $credentials) = explode(' ', $auth);
        if($type === 'Basic'){
            $decodedCredentials = base64_decode($credentials);
            list($username, $password) = explode(':', $decodedCredentials);
            return ($username == $config['admin_username'] && $password == $config['admin_password']);
        }
    }
    return false;
}



$app->addErrorMiddleware(true, true, true);

//0 hello
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Hello World!");
    return $response;
});



$app->run();




