<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Services\GuestbookPostRetriver;
use App\Services\GuestbookPostCreator;

/**
 * GuestbookPost controller.
 * @Route("/api", name="api_")
 */
class GuestbookPostController extends FOSRestController
{

    private $postRetriver;

    private $postCreator;

    public function __construct(GuestbookPostRetriver $postRetriver, GuestbookPostCreator $postCreator)
    {
        $this->postRetriver = $postRetriver;
        $this->postCreator = $postCreator;
    }


    /**
     * Lists all GuestbookPosts.
     * @Rest\Get("/guestbookposts")
     *
     * @return Response
     */
    public function getGuestbookPostsAction(Request $request)
    {


        $posts = $this->postRetriver->getPostList($this->container->getParameter('app.path.guestbookpost_images'));

        return $this->handleView($this->view($posts));
    }

    /**
     * Create GuestbookPost.
     * @Rest\Post("/guestbookpost")
     *
     * @return Response
     */
    public function postGuestbookPostsAction(Request $request)
    {

        $data = json_decode($request->get('data'), 1);
        $data['enabled'] = false;
        $fileimage = $request->get('fileimage');


        try {
            $this->postCreator->createPost($data,
                $fileimage, $this->container->getParameter('app.path.guestbookpost_images'),
                $this->get('kernel')->getProjectDir()
            );

        } catch (\Exception $e) {
            return $this->handleView($this->view(['error' => 'error post created:' . $e->getMessage()], Response::HTTP_BAD_REQUEST));
        }
        return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));

    }
}
