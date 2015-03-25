<?php

namespace AI\Tester\Client;

use AI\Tester\Model\User;
use DI\Annotation\Inject;
use Doctrine\ODM\MongoDB\DocumentManager;
use GuzzleHttp\Client;

class API
{
    /**
     * @Inject(name="http.client")
     * @var Client
     */
    protected $client;

    /**
     * @Inject(name="doctrine.documentManager")
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var string
     */
    protected $accessToken;

    public function register(User $user)
    {
        if (!$user->registered) {
            $requestBody = [
                'username' => $user->username,
                'email' => $user->email,
                'password' => $user->password,
            ];

            $response = $this->client->post(
                '/users/signup',
                ['json' => $requestBody]
            );

            if ($response->getStatusCode() == 201) {
                $user->registered = true;
                $this->documentManager->persist($user);
                $this->documentManager->flush();

                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function login(User $user)
    {
        $requestBody = [
            'grant_type' => 'password',
            'username' => $user->email,
            'password' => $user->password,
            'client_id' => 'tester_tester',
            'client_secret' => 'secret'
        ];

        $response = $this->client->post(
            '/oauth/v2/token',
            ['json' => $requestBody]
        );

        if ($response->getStatusCode() == 200) {
            $accessToken = $response->json()['access_token'];
            $this->accessToken = $accessToken;

            return true;
        } else {
            return false;
        }
    }

    public function getBuys()
    {
    }

    public function rateUpBuy($buy)
    {
    }

    public function rateDownBuy($buy)
    {
    }

    public function createBuy($buyData)
    {
    }

    public function deleteBuy($buy)
    {
    }

    public function editBuy($buy, $editedData)
    {
    }

    public function createParam($paramData)
    {
    }

    public function deleteParam($param)
    {
    }

    public function editParam($param, $editedData)
    {
    }
}
