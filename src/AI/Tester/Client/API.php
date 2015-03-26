<?php

namespace AI\Tester\Client;

use AI\Tester\Model\Buy;
use AI\Tester\Model\Param;
use AI\Tester\Model\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use GuzzleHttp\Client;
use Monolog\Logger;

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
     * @Inject("logger.apiClient")
     * @var Logger
     */
    protected $logger;

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
            } else {
                $this->logger->addError("Register failed", [
                    $response->getStatusCode(),
                    $response->getHeaders(),
                    $response->getBody()->getContents(),
                ]);
            }
        } else {
            $this->logger->addWarning("User already registered", [$user]);
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
            $this->logger->addError("Login failed", [
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);

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
            $this->logger->addError("Get buys failed", [
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);

            return false;
        }

        $json = $response->json();

        return Buy::parseAllFromJson($json);
    }

    /**
     * @param Buy $buy
     * @return Buy
     */
    public function rateUpBuy(Buy $buy)
    {
        $this->changeBuyRate($buy, 'Up');

        return $buy;
    }

    /**
     * @param Buy $buy
     * @return Buy
     */
    public function rateDownBuy(Buy $buy)
    {
        $this->changeBuyRate($buy, 'Down');

        return $buy;
    }

    /**
     * @param Buy $buy
     * @return bool
     */
    public function purchaseBuy(Buy $buy)
    {
        $url = "/buys/{$buy->id}/purchased";
        $response = $this->client->post($url);

        if (200 != $response->getStatusCode()) {
            $this->logger->addError("Purchase buy failed", [
                $buy,
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);

            return false;
        }

        return true;
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
            $this->logger->addError("Create buy failed", [
                $buyData,
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);

            return false;
        }

        $json = $response->json();
        return Buy::parseFromJson($json);
    }

    public function deleteBuy(Buy $buy)
    {
    }

    public function editBuy(Buy $buy, $editedData)
    {
    }

    /**
     * @param Buy $buy
     * @param array $paramData
     * @return Param|bool
     */
    public function createParam(Buy $buy, array $paramData)
    {
        $uri = "/buys/{$buy->id}/params";
        $response = $this->client->post($uri, ['json' => $paramData]);

        if (201 != $response->getStatusCode()) {
            $this->logger->addError("Create param failed", [
                $buy,
                $paramData,
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);
            return false;
        }

        $json = $response->json();
        return Param::parseFromJson($json, $buy);
    }

    /**
     * @param Buy $buy
     * @return Buy[]|bool
     */
    public function getParams(Buy $buy)
    {
        $uri = "/buys/{$buy->id}/params";
        $response = $this->client->get($uri);

        if (200 != $response->getStatusCode()) {
            $this->logger->addError("Get buys failed", [
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);

            return false;
        }

        $json = $response->json();

        return Param::parseAllFromJson($json, $buy);
    }

    /**
     * @param Param $param
     * @return bool
     */
    public function deleteParam(Param $param)
    {
        $uri = "/buys/{$param->buy->id}/params/{$param->id}";
        $response = $this->client->delete($uri);
        if (204 != $response->getStatusCode()) {
            $this->logger->addError("Login failed", [
                $param,
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);

            return false;
        }

        return true;
    }

    public function editParam(Param $param, $editedData)
    {
    }

    /**
     * @param Buy $buy
     * @param string $action
     */
    protected function changeBuyRate(Buy $buy, $action)
    {
        $response = $this->client->post('/buys/' . $buy->id . '/rate' . $action);

        if (200 != $response->getStatusCode()) {
            $this->logger->addError("Rate{$action} failed", [
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ]);
        }

        $buy->rating = $response->json()['rating'];
    }
}
