<?php

namespace AI\Tester\Client;

use AI\Tester\Model\Buy;
use AI\Tester\Model\Param;
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
            $this->client->setDefaultOption('headers/Authorization', 'Bearer ' . $this->accessToken);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Buy[]|bool
     */
    public function getBuys()
    {
        $response = $this->client->get('/buys');

        if (200 != $response->getStatusCode()) {
            return false;
        }

        $json = $response->json();

        return Buy::parseAllFromJson($json);
    }

    public function rateUpBuy(Buy $buy)
    {
    }

    public function rateDownBuy(Buy $buy)
    {
    }

    /**
     * @param array $buyData
     * @return Buy|bool
     */
    public function createBuy($buyData)
    {
        $response = $this->client->post(
            '/buys',
            ['json' => $buyData]
        );

        if (201 != $response->getStatusCode()) {
            return false;
        }

        $json = $response->json();
        return Buy::parseFromJson($json);
    }

    public function deleteBuy($buy)
    {
    }

    public function editBuy(Buy $buy, $editedData)
    {
    }

    public function createParam($paramData)
    {
    }

    public function deleteParam(Param $param)
    {
    }

    public function editParam(Param $param, $editedData)
    {
    }
}
