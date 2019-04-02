<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\GuestbookPost;
use App\Entity\User;
use FOS\UserBundle\Model\UserManager;

class GuestbookPostCreator
{


    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * @param string $pathGuestbookpostImages
     *     *
     * @return boolean
     * @throws \RuntimeException
     */
    public function createPost($data, $fileimage) {

        $post = new GuestbookPost();

        if (!isset($data['title'])) {
            throw new \RuntimeException('Title is requred.');
        }

        if (!isset($data['body'])) {
            throw new \RuntimeException('Body is requred.');
        }


        $post->setEnabled($data['enabled']);
        $post->setTitle($data['title']);
        $post->setBody($data['body']);

        $pathGuestbookpostImages = $this->container->getParameter('app.path.guestbookpost_images');
        $projectDir = $this->container->get('kernel')->getProjectDir();



        try{
            if($fileimage){
                $this->base64_to_jpeg($fileimage, $projectDir . '/public' .
                    $pathGuestbookpostImages .'/'. $data['image']);
                $post->setImage($data['image']);
            }

            $this->entityManager->persist($post);
            $this->entityManager->flush();
        }catch (\Exception $e) {
            throw $e;
        }


        return true;
    }

    private function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }



}
