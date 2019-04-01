<?php

namespace App\Tests\Controllers;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class GuestbookPostControllerTest extends BaseTestCase
{

    public function test_getPostList_without_credentials()
    {

        $response = $this->client->get(
            '/api/guestbookposts',
            array(
                'form_params' => array(
                )
            )
        );

        $resultJSON = $response->getBody()->getContents();
        $result = json_decode($resultJSON,1);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThanOrEqual(1, count($result));
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('body', $result[0]);

    }


    public function test_createPost_with_credentials()
    {

        $data['title'] = 'title 1';
        $data['body']  = 'body';

        $json = json_encode($data);

        $response = $this->client->post(
            '/api/guestbookpost',
            array(
                'form_params' => array(
                    'access_token' => $this->accessToken,
                    'data' => $json
                )
            )
        );


        $resultJSON = $response->getBody()->getContents();
        $result = json_decode($resultJSON,1);


        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($result['status'], 'ok');

    }

    public function test_createPost_without_credentials()
    {

        $data['title'] = 'title 1';
        $data['body']  = 'body';

        $json = json_encode($data);

        $response = $this->client->post(
            '/api/guestbookpost',
            array(
                'form_params' => array(
                    'data' => $json
                )
            )
        );

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());

    }


    public function test_createPost_without_title()
    {

        $data['body']  = 'body';

        $json = json_encode($data);

        $response = $this->client->post(
            '/api/guestbookpost',
            array(
                'form_params' => array(
                    'access_token' => $this->accessToken,
                    'data' => $json
                )
            )
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());


    }

    public function test_createPost_without_body()
    {

        $data['title'] = 'title 1';


        $json = json_encode($data);

        $response = $this->client->post(
            '/api/guestbookpost',
            array(
                'form_params' => array(
                    'access_token' => $this->accessToken,
                    'data' => $json
                )
            )
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

    }

}
