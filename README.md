# sendsay
 integration with Sendsay

# как запустить
1. скопировать .env.example в .env 
2. заполнить переменные:
    ```
    SENDSAY_ACCOUNT=идентификатор аккаунта из админки sendsay
    SENDSAY_API_KEY=апи ключ из sendsay
    SENDSAY_API_ENDPOINT=https://api.sendsay.ru/general/api/v100/json/${SENDSAY_ACCOUNT}
    SENDSAY_LETTER_ID=идентификатор письма для подтверждения подписки
3. запустить composer:
    ```
    composer install
4. запустить docker-compose:
    ```
    docker compose up -d
