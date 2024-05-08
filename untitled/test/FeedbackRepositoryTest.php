<?php
namespace test;

use Classes\store\FeedbackRepository;
use PHPUnit\Framework\TestCase;
use Classes\Database;
use PDOStatement;
use PDO;

class FeedbackRepositoryTest extends TestCase{

    public function testFindById(): void{

        $databaseMock = $this->createMock(Database::class);
        $PDOStatementMock = $this->createMock(PDOStatement::class);
        $feedbackRepository = new FeedbackRepository($databaseMock);

        $id = 9;
        $expectedResult = ['id' => 1, 'author' => 'author1', 'content' => 'content1'];

        $databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("SELECT * FROM feedbacks WHERE id = :id"))
            ->willReturn($PDOStatementMock);

        $PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with(['id' => $id]);

        $PDOStatementMock->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedResult);

        $result = $feedbackRepository->findById($id);

        $this->assertSame($expectedResult['id'], $result['id']);
        $this->assertSame($expectedResult['author'], $result['author']);
        $this->assertSame($expectedResult['content'], $result['content']);
    }

    public function testFind(): void{

        $databaseMock = $this->createMock(Database::class);
        $PDOStatementMock = $this->createMock(PDOStatement::class);
        $PDOStatementMockCount = $this->createMock(PDOStatement::class);
        $feedbackRepository = new FeedbackRepository($databaseMock);

        $page = 1;
        $perPage = 10;
        $expectedResult = [
            ['id' => 5, 'author' => 'author5', 'content' => 'content5'],
            ['id' => 4, 'author' => 'author4', 'content' => 'content4']
        ];

        $databaseMock->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnMap([
                ["SELECT * FROM feedbacks ORDER BY id DESC LIMIT :offset, :perPage ", $PDOStatementMock],
                ["SELECT COUNT(*) FROM feedbacks", $PDOStatementMockCount]
            ]);

        $PDOStatementMockCount->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $PDOStatementMockCount->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(100);

        $PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with([':offset' => ($page - 1) * $perPage, ':perPage' => $perPage])
            ->willReturn(true);
        $PDOStatementMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedResult);

        $result = $feedbackRepository->find($page, $perPage);

        $this->assertSame($expectedResult, $result['feedbacks']);
    }

    public function testCount(): void{

        $databaseMock = $this->createMock(Database::class);
        $PDOStatementMock = $this->createMock(PDOStatement::class);
        $feedbackRepository = new FeedbackRepository($databaseMock);

        $expectedCount = 5;

        $databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("SELECT COUNT(*) FROM feedbacks"))
            ->willReturn($PDOStatementMock);

        $PDOStatementMock->expects($this->once())
            ->method('execute');

        $PDOStatementMock->expects($this->once())
            ->method('fetchColumn')
            ->willReturn($expectedCount);

        $result = $feedbackRepository->count();

        $this->assertSame($expectedCount, $result);
    }


    public function testDelete(): void{

        $databaseMock = $this->createMock(Database::class);
        $PDOStatementMock = $this->createMock(PDOStatement::class);
        $feedbackRepository = new FeedbackRepository($databaseMock);

        $id = 999;

        $expectedResult = array("success" => "Delete is successful");

        $databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("DELETE FROM feedbacks WHERE id = :id"))
            ->willReturn($PDOStatementMock);

        $PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with(['id' => $id]);

        $PDOStatementMock->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $feedbackRepository->delete($id);

        $this->assertSame($expectedResult,$result);
    }


    public function testGetFieldsForRead(): void{

        $databaseMock = $this->createMock(Database::class);
        $feedbackRepository = new FeedbackRepository($databaseMock);

        $expectedResult = [
            'id' => 'int',
            'author' => 'string',
            'content' => 'string'
        ];

        $result = $feedbackRepository->getFieldsForRead();

        $this->assertSame($expectedResult, $result);
    }

    public function testGetFieldsForCreate(): void{

        $databaseMock = $this->createMock(Database::class);
        $feedbackRepository = new FeedbackRepository($databaseMock);

        $expectedResult = [
            'author' => 'string',
            'content' => 'string'
        ];

        $result = $feedbackRepository->getFieldsForCreate();

        $this->assertSame($expectedResult, $result);

    }

    public function testCreate(): void{

        $databaseMock = $this->createMock(Database::class);
        $PDOStatementMock = $this->createMock(PDOStatement::class);
        $feedbackRepository = new FeedbackRepository($databaseMock);

        $feedbackData = ['author' => 'John', 'content' => 'Test feedback'];

        $databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("INSERT INTO feedbacks (author, content) VALUES (:author, :content)"))
            ->willReturn($PDOStatementMock);

        $PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with($feedbackData);

        $result = $feedbackRepository->create($feedbackData);

        $this->assertSame(['success' => 'Feedback created successfully'], $result);

    }

}
