<?php
namespace App\Tests\Services;

use App\Entity\GuestbookPost;
use App\Repository\GuestbookPostRepository;
use App\Services\GuestbookPostRetriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class GuestbookPostRetriverServiceTest extends TestCase
{


    /**
     * @var GuestbookPostRetriver
     */
    private $guestbookPostRetriver;

    /**
     * @var string
     */
    private $currentRepository;

    /**
     * @var GuestbookPost[]
     */
    private $list;


    public function setUp()
    {

        $record1 = new GuestbookPost();
        $record1->setTitle('t');
        $record1->setBody('b');
        $record1->setEnabled(false);
        $record1->setImage('i');

        $record2 = new GuestbookPost();
        $record2->setTitle('t');
        $record2->setBody('b');
        $record2->setEnabled(false);
        $record2->setImage('i');

        $this->list[] = $record1;
        $this->list[] = $record2;

        $entityManager = $this->mockEntityManager();

        $this->guestbookPostRetriver = new GuestbookPostRetriver(
            $entityManager
        );

    }


    public function mockEntityManager()
    {
        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturnCallback([$this, 'getRepositoryCallback']);
        return $entityManager;
    }


    public function testCreate_Post()
    {
        $result = $this->guestbookPostRetriver->getPostList('imagepath');
        $this->assertEquals($this->list, $result);
    }

    public function getRepositoryCallback($repositoryName)
    {
        $repository = $this
            ->getMockBuilder(GuestbookPostRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())
            ->method('getEnabledList')
            ->willReturnCallback([$this, 'getEnabledListCallback']);

        return $repository;
    }

    public function getEnabledListCallback()
    {
        return $this->list;
    }
}
