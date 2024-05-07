<?php

namespace Classes;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController{
    private $container;
    private $repository;
    private $database;
    public function __construct($container){
        $this->container = $container;
        $this->database = new \Classes\Database();
        $this->repository = new \Classes\FeedbackRepository($this->database);
    }

    public function hello(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $response->getBody()->write("Hello World!");
        return $response;
    }

    public function feedbackById(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $id = $args['id'];

        $feedback = $this->repository->findById($id);

        if (!$feedback) {
            $response->getBody()->write(json_encode(['error' => 'Feedback not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($feedback));
        return $response->withHeader('Content-Type', 'application/json');

    }

    public function feedbacks(ServerRequestInterface $request, ResponseInterface $response, array $args){

        $page = $args["page"];
        $perPage = 20;

        $feedback = $this->repository->find($page, $perPage);

        $totalFeedbacks = $this->repository->count();

        $totalPages = ceil($totalFeedbacks / $perPage);

        if (!$feedback) {
            $response->getBody()->write(json_encode(['error' => 'Feedback not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['feedbacks' => $feedback, 'totalPages' => $totalPages]));
        return $response->withHeader('Content-Type', 'application/json');

    }

    public function checkAdminCredentials($request): bool{
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

    public function deleteById(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $id = $args["id"];

        $isAdmin = $this->checkAdminCredentials($request);
        if(!$isAdmin){
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $feedback = $this->repository->delete($id);

        if(!$feedback){
            $response->getBody()->write(json_encode(['error' => 'Delete failed']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode("Delete is successful"));
        return $response->withHeader('Content-Type', 'application/json');

    }

    public function createFeedback(ServerRequestInterface $request, ResponseInterface $response){
        $data = json_decode($request->getBody()->getContents(), true);

        $result = $this->repository->create($data);

        if(isset($result['error'])){
            $response->getBody()->write(json_encode(['error' => $result['error']]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['success' => $result['success']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

}