<?php

namespace test;

use Psr\Http\Message\ServerRequestInterface;
use Classes\controller\FeedbackController;
use Psr\Http\Message\ResponseInterface;
use Classes\store\FeedbackRepository;
use Psr\Http\Message\StreamInterface;
use PHPUnit\Framework\TestCase;
use Classes\Database;

class FeedbackControllerTest extends TestCase{

    private function createRequestAndResponseMocks(array $content): array{
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

    public function testFeedbackById(): void{
        $id = 1;

        $database = new Database();
        $rep = new FeedbackRepository($database);
        $feedback = $rep->findById($id);

        $expectedFeedback = json_encode($rep->findById($id));

        list($request, $response) = $this->createRequestAndResponseMocks($feedback);

        $repository = $this->createMock(FeedbackRepository::class);
        $repository->method('findById')->with($id)->willReturn($feedback);

        $feedbackController = new FeedbackController();


        $result = $feedbackController->feedbackById($request, $response, ['id' => $id])->getBody()->getContents();

        $this->assertEquals($expectedFeedback, $result);

    }

    public function testFeedbacksByPage(): void{
        $page = 7;
        $perPage = 20;

        $database = new Database();
        $rep = new FeedbackRepository($database);
        $feedback = $rep->find($page, $perPage);

        $expectedFeedback = json_encode($rep->find($page, $perPage));

        list($request, $response) = $this->createRequestAndResponseMocks($feedback);

        $repository = $this->createMock(FeedbackRepository::class);
        $repository->method('find')->with($page, $perPage)->willReturn($feedback);

        $feedbackController = new FeedbackController();

        $result = $feedbackController->feedbacks($request, $response, ['page' => $page, 'perPage' => $perPage])->getBody()->getContents();

        $this->assertEquals($expectedFeedback, $result);

    }

    public function testDeleteById(): void{
        $id = 16;
        $rep = new FeedbackRepository(new Database());

        $feedbackController = new FeedbackController();

        if(array_key_exists('error', $rep->findById($id))) {
            $expectedFeedback = json_encode(array("error" => "No feedback with ID $id found."));
        }else{
            $expectedFeedback = json_encode(array("success" => "Delete is successful"));
        }

        $feedback = $rep->delete($id);

        list($request, $response) = $this->createRequestAndResponseMocks($feedback);

        $repository = $this->createMock(FeedbackRepository::class);
        $repository->method('delete')->with($id)->willReturn($feedback);


        $result = $feedbackController->deleteById($request, $response, ['id' => $id])->getBody()->getContents();


        $this->assertEquals($expectedFeedback, $result);

    }

    public function testCreateFeedback(): void{
        $data = array('author' => 'Jon Doe', 'content' => 'Hello');

        $repository = new FeedbackRepository(new Database());
        $feedback = json_encode($repository->create($data));

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($feedback);
        $response->method('getBody')->willReturn($stream);

        $response->expects($this->once())
            ->method('withHeader')
            ->willReturnSelf();
        $response->expects($this->once())
            ->method('withStatus')
            ->willReturnSelf();

        $feedbackController = new FeedbackController();

        $result = $feedbackController->createFeedback($request, $response)->getBody()->getContents();

        $this->assertEquals($feedback, $result);

    }

}
