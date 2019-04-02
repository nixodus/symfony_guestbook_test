<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\GuestbookPost;


class GuestbookPostRetriver
{

    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * @var EntityManager
     */
    private $entityManager;



    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * @param string $pathGuestbookpostImages
     *     *
     * @return array
     * @throws \RuntimeException
     */
    public function getPostList() {

        $pathGuestbookpostImages = $this->container->getParameter('app.path.guestbookpost_images');
        $repository = $this->entityManager->getRepository(GuestbookPost::class);
        /** @var GuestbookPost[] $posts */

        $posts = $repository->getEnabledList();


        foreach ($posts as $post) {
            $image = $post->getImage();
            if($image){
                $post->setImage(
                    $pathGuestbookpostImages .'/'. $image);
            }
        }

        return $posts;
    }
}
