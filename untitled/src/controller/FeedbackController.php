<?php

namespace Classes\controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Classes\store;


class FeedbackController{
    private store\FeedbackRepository $repository;
    public function __construct(){
        $this->repository = new store\FeedbackRepository(new \Classes\Database());
    }

    public function feedbackById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{
        $id = $args['id'];

        $feedback = $this->repository->findById($id);

        return $this->responseTreatment($feedback, $response);

    }

    public function feedbacks(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{

        $page = $args["page"];
        $perPage = 20;

        $feedback = $this->repository->find($page, $perPage);

        return $this->responseTreatment($feedback, $response);

    }

    public function checkAdminCredentials($request): bool{
        $config = include("config.php");
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

    public function deleteById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{
        $id = $args["id"];

        $isAdmin = $this->checkAdminCredentials($request);
        if(!$isAdmin){
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $feedback = $this->repository->delete($id);

        return $this->responseTreatment($feedback, $response);
    }

    public function createFeedback(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{
        $data = json_decode($request->getBody()->getContents(), true);

        $feedback = $this->repository->create($data);

        if(isset($feedback['error'])){
            $response = $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($feedback));
            return $response;
        }

        $response = $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($feedback));
        return $response;
    }

    public function responseTreatment(array $feedback, ResponseInterface $response): ResponseInterface{
        if (array_key_exists('error', $feedback)) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($feedback));
            return $response;
        }

        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($feedback));
        return $response;
    }

}