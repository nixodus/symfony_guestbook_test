<?php

namespace App\Tests;


use App\Entity\User;
use App\Entity\GuestbookPost;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseTestCase extends KernelTestCase
{

    const TEST_ADMIN_USER = "adminuser";
    const TEST_ADMIN_USER_PASSWORD = "admin123";
    const TEST_ADMIN_USER_EMAIL = "rest@nixus.eu";

    const TEST_BASE_URL = "http://localhost:8000";
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var User
     */
    protected $testUser;

    /**
     * @string accessToken
     */
    protected $accessToken;


    public function setUp()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => self::TEST_BASE_URL,
            'exceptions' => false
        ]);

        $container = $this->getPrivateContainer();

        $this->em = $container
            ->get('doctrine')
            ->getManager();


        $this->createGuestbookPost();
        $this->testUser = $this->createTestUser();

        $this->accessToken = $this->getValidToken();

    }

    private function truncateTables()
    {
        $em = $this->em;

        $query = $em->createQuery('DELETE App:RefreshToken u WHERE 1 = 1');
        $query->execute();


        $query = $em->createQuery('DELETE App:AccessToken u WHERE 1 = 1');
        $query->execute();


        $query = $em->createQuery('DELETE App:Client u WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:User u WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:GuestbookPost u WHERE 1 = 1');
        $query->execute();


        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    protected function createTestUser($username = self::TEST_ADMIN_USER, $email = self::TEST_ADMIN_USER_EMAIL, $password = self::TEST_ADMIN_USER_PASSWORD)
    {
        $container = $this->getPrivateContainer();
        $userManager = $container
            ->get('fos_user.user_manager');


        $user = $userManager->createUser();
        $user->setUsername($username );
        $user->setEmail($email);
        $user->setPlainPassword($password);

        $user->setEnabled(true);
        $user->setRoles(array('ROLE_ADMIN'));

        // Update the user
        return $userManager->updateUser($user, true);

    }

    /**
     * @param string $name
     * @return GuestbookPost|string
     */
    protected function createGuestbookPost($title = "Test title 1", $body = "Body")
    {
        $data['title'] = $title;
        $data['body'] = $body;
        $data['enabled'] = true;
        $fileData = '';
        $container = $this->getPrivateContainer();
        $imagePatch = $container->getParameter('app.path.guestbookpost_images');
        $projectDir = self::$kernel->getProjectDir();

        $creatorService = $container
            ->get('App\Services\GuestbookPostCreator');

        return $creatorService->createPost(
            $data,
            $fileData,
            $imagePatch,
            $projectDir
        );
    }

    protected function getValidToken(User $user = null)
    {
        if (!$user) {
            $user = $this->testUser;
        }

        $data['grant-type'] = 'password';
        $data['redirect-uri'] = self::TEST_BASE_URL;

        $json = json_encode($data);


        $response = $this->client->post(
            '/createClient',
            array(
                'form_params' => array(
                    'data' => $json
                )
            )
        );


        if($response->getStatusCode() == JsonResponse::HTTP_OK){
            $resultJSON = $response->getBody()->getContents();
            $result = json_decode($resultJSON,1);



            $responseOAuth = $this->client->post(
                '/oauth/v2/token',
                array(
                    'form_params' => array(
                        'grant_type' => 'password',
                        'client_id' => $result['client_id'],
                        'client_secret' => $result['client_secret'],
                        'username' => self::TEST_ADMIN_USER,
                        'password' => self::TEST_ADMIN_USER_PASSWORD,
                    )
                )
            );

            if($response->getStatusCode() == JsonResponse::HTTP_OK){
                $resultJSONOAuth = $responseOAuth->getBody()->getContents();
                $resultOAuth = json_decode($resultJSONOAuth,1);
                return $resultOAuth['access_token'];

            }
        }

        return false;

    }

    private function getPrivateContainer()
    {
        self::bootKernel();

        // returns the real and unchanged service container

        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;

        return $container;
    }

    protected function tearDown()
    {
        $this->truncateTables();
    }
}