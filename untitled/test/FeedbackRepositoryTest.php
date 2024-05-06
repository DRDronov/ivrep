<?php
namespace test;

use PHPUnit\Framework\TestCase;
use Classes\FeedbackRepository;
use Classes\Database;
use PDOStatement;
use PDO;

class FeedbackRepositoryTest extends TestCase{
    protected Database $databaseMock;
    protected PDOStatement $PDOStatementMock;
    protected FeedbackRepository $feedbackRepository;

    protected function setUp(): void{
        $this->databaseMock = $this->createMock(Database::class);
        $this->PDOStatementMock = $this->createMock(PDOStatement::class);
        $this->feedbackRepository = new FeedbackRepository($this->databaseMock);
    }

    public function testFindById(): void{
        $id = 9;
        $expectedResult = ['id' => 1, 'author' => 'author1', 'content' => 'content1'];

        $this->databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("SELECT * FROM feedbacks WHERE id = :id"))
            ->willReturn($this->PDOStatementMock);

        $this->PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with(['id' => $id]);

        $this->PDOStatementMock->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedResult);

        $result = $this->feedbackRepository->findById($id);

        $this->assertSame($expectedResult['id'], $result['id']);
        $this->assertSame($expectedResult['author'], $result['author']);
        $this->assertSame($expectedResult['content'], $result['content']);
    }

    public function testFind(): void{
        $page = 2;
        $perPage = 10;
        $expectedResult = [
            ['id' => 5, 'author' => 'author5', 'content' => 'content5'],
            ['id' => 4, 'author' => 'author4', 'content' => 'content4']
        ];

        $this->databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("SELECT * FROM feedbacks ORDER BY id DESC LIMIT :offset, :perPage "))
            ->willReturn($this->PDOStatementMock);

        $this->PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with([':offset' => ($page - 1) * $perPage, ':perPage' => $perPage]);

        $this->PDOStatementMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedResult);

        $result = $this->feedbackRepository->find($page, $perPage);

        $this->assertSame($expectedResult, $result);
    }

    public function testCount(): void{
        $expectedCount = 5;

        $this->databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("SELECT COUNT(*) FROM feedbacks"))
            ->willReturn($this->PDOStatementMock);

        $this->PDOStatementMock->expects($this->once())
            ->method('execute');

        $this->PDOStatementMock->expects($this->once())
            ->method('fetchColumn')
            ->willReturn($expectedCount);

        $result = $this->feedbackRepository->count();

        $this->assertSame($expectedCount, $result);
    }


    public function testDelete(): void{
        $id = 3;

        $this->databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("DELETE FROM feedbacks WHERE id = :id"))
            ->willReturn($this->PDOStatementMock);

        $this->PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with(['id' => $id]);

        $this->PDOStatementMock->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $this->feedbackRepository->delete($id);

        $this->assertTrue($result);
    }


    public function testGetFieldsForRead(): void{
        $expectedResult = [
            'id' => 'int',
            'author' => 'string',
            'content' => 'string'
        ];

        $result = $this->feedbackRepository->getFieldsForRead();

        $this->assertSame($expectedResult, $result);
    }

    public function testGetFieldsForCreate(): void{
        $expectedResult = [
            'author' => 'string',
            'content' => 'string'
        ];

        $result = $this->feedbackRepository->getFieldsForCreate();

        $this->assertSame($expectedResult, $result);

    }

    public function testCreate(): void{
        $feedbackData = ['author' => 'John', 'content' => 'Test feedback'];

        $this->databaseMock->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("INSERT INTO feedbacks (author, content) VALUES (:author, :content)"))
            ->willReturn($this->PDOStatementMock);

        $this->PDOStatementMock->expects($this->once())
            ->method('execute')
            ->with($feedbackData);

        $result = $this->feedbackRepository->create($feedbackData);

        $this->assertSame(['success' => 'Feedback created successfully'], $result);
    }

}
