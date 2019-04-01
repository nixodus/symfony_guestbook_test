<?php

namespace App\Tests\Controllers;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityControllerTest extends BaseTestCase
{

    public function test_getToken()
    {

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

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        if($response->getStatusCode() == JsonResponse::HTTP_OK){
            $resultJSON = $response->getBody()->getContents();
            $result = json_decode($resultJSON,1);

            $this->assertArrayHasKey('client_id', $result);
            $this->assertArrayHasKey('client_secret', $result);

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

            $this->assertEquals(JsonResponse::HTTP_OK, $responseOAuth->getStatusCode());

            if($response->getStatusCode() == JsonResponse::HTTP_OK){
                $resultJSONOAuth = $responseOAuth->getBody()->getContents();
                $resultOAuth = json_decode($resultJSONOAuth,1);

                $this->assertArrayHasKey('access_token', $resultOAuth);
                $this->assertArrayHasKey('expires_in', $resultOAuth);
                $this->assertArrayHasKey('token_type', $resultOAuth);
                $this->assertArrayHasKey('scope', $resultOAuth);
                $this->assertArrayHasKey('refresh_token', $resultOAuth);

            }
        }

    }

    public function test_getToken_bad_client_secret()
    {

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

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        if($response->getStatusCode() == JsonResponse::HTTP_OK){
            $resultJSON = $response->getBody()->getContents();
            $result = json_decode($resultJSON,1);

            $this->assertArrayHasKey('client_id', $result);
            $this->assertArrayHasKey('client_secret', $result);

            $responseOAuth = $this->client->post(
                '/oauth/v2/token',
                array(
                    'form_params' => array(
                        'grant_type' => 'password',
                        'client_id' => $result['client_id'],
                        'client_secret' => $result['client_secret'] . '1',
                        'username' => self::TEST_ADMIN_USER,
                        'password' => self::TEST_ADMIN_USER_PASSWORD,
                    )
                )
            );

            $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $responseOAuth->getStatusCode());


        }





    }



    public function test_getToken_bad_grand_type()
    {

        $data['grant_type'] = 'password';
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

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

    }

}
