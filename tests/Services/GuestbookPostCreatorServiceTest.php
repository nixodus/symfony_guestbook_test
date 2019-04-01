<?php
namespace PersonalGoalsBundle\Tests\Services;

use App\Entity\GuestbookPost;
use App\Services\GuestbookPostCreator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

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

        $this->guestbookPostCreator = new GuestbookPostCreator(
            $entityManager
        );

    }


    public function mockEntityManager()
    {
        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $entityManager;
    }


    public function testCreate_Post()
    {

        $data['title'] = 't';
        $data['body'] = 'b';
        $data['enabled'] = false;

        $result = $this->guestbookPostCreator->createPost($data, false, 'imagePath', 'dir');

        $this->assertTrue($result);
    }

}
