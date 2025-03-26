<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class SendsayAPI
{
    private string $apiKey;
    private string $apiUrl;
    private string $letterId;
    private ?string $session = null;
    private Client $client;

    public function __construct()
    {
        $config = Config::getInstance();
        $this->apiKey = $config->get('api_key');
        $this->apiUrl = $config->get('api_url');
        $this->letterId = $config->get('letter_id');

        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => 15,
            'http_errors' => false,
        ]);
    }

    /**
     * @return bool
     */
    public function auth(): bool
    {
        $response = $this->makeRequest([
            'action' => 'login',
            'apikey' => $this->apiKey
        ]);

        if (isset($response['session'])) {
            $this->session = $response['session'];
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $petCategory
     * @param string $petName
     * @return bool
     * @throws Exception
     */
    public function setMember(string $name, string $email, string $petCategory, string $petName): bool
    {
        if (!$this->auth()) {
            throw new Exception('Авторизация прошла не успешно');
        }

        $userData = [
            'action' => 'member.set',
            'email' => $email,
            'session' => $this->session,
            'newbie.confirm' => 1,
            'source' => $_SERVER['REMOTE_ADDR'],
            'datakey' => [
                ['base.firstName', 'set', $name],
                ['custom.q197', 'set', $petCategory],
                ['custom.q39', 'set', $petName],
            ]
        ];

        $response = $this->makeRequest($userData);

        if (isset($response['errors'])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function memberSendConfirm(string $email, array $options = []): bool
    {
        if (!$this->auth()) {
            throw new Exception('Авторизация прошла не успешно');
        }

        $requestData = [
            'action' => 'member.sendconfirm',
            'session' => $this->session,
            'email' => $email,
            'confirm' => 1,
            'letter' => $this->letterId,
        ];

        if (!empty($options['issue_name'])) {
            $requestData['issue_name'] = $options['issue_name'];
        }

        $response = $this->makeRequest($requestData);

        if (isset($response['errors'])) {
            return false;
        }

        return true;
    }

    /**
     * @param array $data данные для отправки
     * @return array
     */
    private function makeRequest(array $data): array
    {
        try {
            if ($this->session && !isset($data['session']) && $data['action'] !== 'login') {
                $data['session'] = $this->session;
            }

            $response = $this->client->post('', [
                RequestOptions::JSON => $data,
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    //todo:с заголовком возникает ошибка too_many_auth_parameters
                    //'Authorization' => 'sendsay apikey=' . urlencode($this->apiKey)
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true) ?: [];

        } catch (GuzzleException $e) {
            return ['errors' => $e->getMessage()];
        }
    }
}
