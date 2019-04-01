<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Response;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;



class SecurityController extends FOSRestController
{
    private $client_manager;
    public function __construct(ClientManagerInterface $client_manager)
    {
        $this->client_manager = $client_manager;
    }
    /**
     * Create Client.
     * @FOSRest\Post("/createClient")
     *
     * @return Response
     */
    public function AuthenticationAction(Request $request)
    {

        $data = json_decode($request->get('data'),1);

        if (empty($data['redirect-uri']) || empty($data['grant-type'])) {
            return $this->handleView($this->view(['error' => 'redirect-uri or grant-type not present'], Response::HTTP_BAD_REQUEST));
        }

        $clientManager = $this->client_manager;
        $client = $clientManager->createClient();
        $client->setRedirectUris([$data['redirect-uri']]);
        $client->setAllowedGrantTypes([$data['grant-type']]);
        $clientManager->updateClient($client);
        $rows = [
            'client_id' => $client->getPublicId(), 'client_secret' => $client->getSecret()
        ];


        return $this->handleView($this->view($rows));
    }
}
