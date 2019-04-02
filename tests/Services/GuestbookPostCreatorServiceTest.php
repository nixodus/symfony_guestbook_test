<?php
namespace PersonalGoalsBundle\Tests\Services;

use App\Entity\GuestbookPost;
use App\Services\GuestbookPostCreator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;


class GuestbookPostCreatorServiceTest extends TestCase
{
    /**
     * @var string
     */
    private $currentRepository;

    /**
     * @var GuestbookPostCreator
     */
    private $guestbookPostCreator;


    public function setUp()
    {
        $entityManager = $this->mockEntityManager();
        $container = $this->mockContainer();

        $this->guestbookPostCreator = new GuestbookPostCreator(
            $entityManager,
            $container
        );

    }


    public function mockEntityManager()
    {
        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $entityManager;
    }


    public function mockContainer()
    {
        $Container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();
        $Container->expects($this->any())
            ->method('get')
            ->willReturnCallback([$this, 'getKernelCallback']);
        return $Container;
    }

    public function getKernelCallback()
    {
        $Kernel = $this->getMockBuilder(Kernel::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $Kernel;
    }


    public function testCreate_Post()
    {

        $data['title'] = 't';
        $data['body'] = 'b';
        $data['enabled'] = false;

        $result = $this->guestbookPostCreator->createPost($data, false);

        $this->assertTrue($result);
    }

}
