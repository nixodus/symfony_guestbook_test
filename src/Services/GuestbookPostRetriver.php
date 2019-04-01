<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GuestbookPost;


class GuestbookPostRetriver
{


    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $pathGuestbookpostImages
     *     *
     * @return array
     * @throws \RuntimeException
     */
    public function getPostList($pathGuestbookpostImages) {


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
