<?php

namespace App;

use Exception;
use Random\RandomException;

class Handler
{
    private SendsayAPI $sendsayAPI;

    public function __construct()
    {
        $this->sendsayAPI = new SendsayAPI();
    }

    /**
     * @param array $data данные из формы $_POST
     * @return bool
     * @throws RandomException
     * @throws Exception
     */
    public function processForm(array $data): bool
    {
        if (empty($data['name'])
            || empty($data['email'])
            || empty($data['pet_category'])
            || empty($data['pet_name'])
        ) {
            throw new Exception('Пустые значения');
        }

        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (!preg_match($pattern, $data['email'])) {
            throw new Exception('Невалидный email');
        }

        $userAdded = $this->sendsayAPI->setMember(
            $data['name'],
            $data['email'],
            $data['pet_category'],
            $data['pet_name'],
        );

        if (!$userAdded) {
            return false;
        }

        $confirmOptions = [
            'issue_name' => 'Подтверждение регистрации питомца',
            //todo:не разобралась как свою ссылку прикладывать в письмо
            'confirm' => $this->generateConfirm(),
        ];

        $result = $this->sendsayAPI->memberSendConfirm(
            $data['email'],
            $confirmOptions
        );

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     * @throws \Random\RandomException
     */
    private function generateConfirm(): string
    {
        return hash('sha256', random_bytes(32));
    }
}
