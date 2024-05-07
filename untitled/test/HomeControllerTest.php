<?php

namespace test;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use PHPUnit\Framework\TestCase;
use Classes\FeedbackRepository;
use Classes\HomeController;
use Classes\Database;

require 'config.php';

class HomeControllerTest extends TestCase
{

    private function createRequestAndResponseMocks(array $content): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $stream->method('getContents')->willReturn(json_encode($content));
        $response->method('getBody')->willReturn($stream);

        $response->expects($this->once())
            ->method('withHeader')
            ->willReturnSelf();
        $response->expects($this->once())
            ->method('withStatus')
            ->willReturnSelf();

        return [$request, $response];
    }

    public function testFeedbackById()
    {
        $id = 1;

        $database = new Database();
        $rep = new FeedbackRepository($database);
        $feedback = $rep->findById($id);

        $expectedFeedback = json_encode($rep->findById($id));

        list($request, $response) = $this->createRequestAndResponseMocks($feedback);

        $repository = $this->createMock(FeedbackRepository::class);
        $repository->method('findById')->with($id)->willReturn($feedback);

        $homeController = new HomeController();
        $homeController->setRepository($rep);

        $result = $homeController->feedbackById($request, $response, ['id' => $id])->getBody()->getContents();

        $this->assertEquals($expectedFeedback, $result);

    }

    public function testFeedbacksByPage()
    {
        $page = 7;
        $perPage = 20;

        $database = new Database();
        $rep = new FeedbackRepository($database);
        $feedback = $rep->find($page, $perPage);

        $expectedFeedback = json_encode($rep->find($page, $perPage));

        list($request, $response) = $this->createRequestAndResponseMocks($feedback);

        $repository = $this->createMock(FeedbackRepository::class);
        $repository->method('find')->with($page, $perPage)->willReturn($feedback);

        $homeController = new HomeController();
        $homeController->setRepository($rep);

        $result = $homeController->feedbacks($request, $response, ['page' => $page, 'perPage' => $perPage])->getBody()->getContents();

        $this->assertEquals($expectedFeedback, $result);

    }

//    public function testCreateFeedback()
//    {
//        $data = array('author' => 'Jhon Doe', 'content' => 'Hello');
//
//        $rep = new FeedbackRepository(new Database());
//        $feedback = json_encode($rep->create($data));
//
//        $request = $this->createMock(ServerRequestInterface::class);
//        $response = $this->createMock(ResponseInterface::class);
//        $stream = $this->createMock(StreamInterface::class);
//
//        $stream->method('getContents')->willReturn(json_encode($data));
//        $response->method('getBody')->willReturn($stream);
//
//        $request->method('getBody')->willReturn($stream);
//
//
//        $homeController = new HomeController();
//        $homeController->setRepository($rep);
//
//        $result = $homeController->createFeedback($request, $response)->getBody()->getContents();
//
//        $this->assertEquals($feedback, $result);
//
//    }


}
