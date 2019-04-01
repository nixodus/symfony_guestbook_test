<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GuestbookPost;

class GuestbookPostCreator
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
     * @return boolean
     * @throws \RuntimeException
     */
    public function createPost($data, $fileimage, $pathGuestbookpostImages, $projectDir) {

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
