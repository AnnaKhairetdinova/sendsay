<?php

namespace App;

use Dotenv\Dotenv;

class Config
{
    private static ?Config $instance = null;
    private array $config;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $this->config = [
            'api_key' => $_ENV['SENDSAY_API_KEY'],
            'api_url' => $_ENV['SENDSAY_API_ENDPOINT'],
            'letter_id' => $_ENV['SENDSAY_LETTER_ID'],
        ];
    }

    /**
     * @return Config
     */
    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        return $this->config[$key];
    }
}
