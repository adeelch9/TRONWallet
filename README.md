# TRONWallet
TRONWallet is a Feature-Rich demonstration of a TRX Wallet app built in Telegram. Wallet is currently set to testnet. Head over to the api/app/Http/Controllers/DPX.php file to change the network to mainnet. You can use this app to generate a new wallet, check your balance, send and receive TRX tokens, and view your transaction history.

## Laravel Backend API Depoloyment
- clone the repository to your server and navigate to the api folder
  
  ```
  git clone https://github.com/adeelch9/TRONWallet.git
  cd TRONWallet/api
  ```

- install dependencies
  
  ```
  composer install
  ```

- copy the example env file and make the required configuration changes in the .env file
  
  ```
  cp .env.example .env
  ```

- create SQLite database and set permissions
  
  ```
  touch database/database.sqlite
  chmod 666 database/database.sqlite
  chmod 775 database
  ```

- run migrations
  
  ```
  php artisan migrate
  ```

- generate a new application key
  
  ```
  php artisan key:generate
  ```

- run the server
  
  ```
  php artisan serve
  ```

## Testing Backend API

  - Generate Wallet:
  
  ```
  curl --location --request POST 'http://127.0.0.1:8000/api/generate'
  ```

  - Get Wallet Balance:
  
  ```
  curl --location 'http://127.0.0.1:8000/api/balance/<WALLET_ADDRESS>'
  ```

  - Send Transaction:
  
  ```
  curl --location 'http://127.0.0.1:8000/api/transfer' \
--form 'departure="<SENDERS_WALLET_ADDRESS>"' \
--form 'secret="<SENDERS_PRIVATE_KEY>"' \
--form 'destination="<RECIEVERS_WALLET_ADDRESS>"' \
--form 'amount="<TRX_TOKENS_AMOUNT>"'
  ```

  - Get Transaction History:
  
  ```
  curl --location 'http://127.0.0.1:8000/api/transaction/<TRANSACTION_HASH>'
  ```

  - Get Transactions History of a Wallet:
  
  ```
  curl --location 'http://127.0.0.1:8000/api/transactions' \
--form 'departure="<SENDERS_WALLET_ADDRESS>"' \
--form 'destination="<RECIEVERS_WALLET_ADDRESS>"'
  ```


## FrontEnd Deployment

  - clone the repository to your server and navigate to the frontend folder
  
  ```
  git clone https://github.com/adeelch9/TRONWallet.git
  cd TRONWallet/webApp
  ```

  - install dependencies
  
  ```
  npm install
  ```

  - run the server
  
  ```
  npm run dev
  ```

  - deploy frontend to Vercel, also add .env file with the variables set to the values of the variables in the .env.example file in /api folder of the project.
  
  - Set APP_URL in .env variable to the URL of the backend API.
  
  
## Telegram Bot Deployment

  - Create a new bot on Telegram using the `@BotFather` bot.
  - Use the command `/newapp` to create a new mini app
  - Select your bot and provide a name and description for the app
  - Upload an icon image for your app
  - Enter the URL where your webApp is hosted (Ensure the URL provided to BotFather is correct and accessible)
  
## Test your Mini App

  - Open the Telegram app and search for your bot
  - Select your Mini App to open it
  - You should see the Mini App running in the Telegram app

## Docker Deployment

  Clone the repository:

  ```
  git clone https://github.com/adeelch9/TRONWallet.git
  ```

  Navigate to the project directory:

  ```
  cd TRONWallet
  ```

  Navigate to the api folder:

  ```
  cd api
  ```

  Build the Docker image:

  ```
  docker build -t demo/laravel:0.1 .
  ```

  Run the Docker container:

  ```
  docker run -p 8080:80 demo/laravel:0.1
  ```

## Testing Docker Deployment

  - Generate Wallet
  
  ```
  curl --location --request POST 'http://localhost:8080/api/generate'
  ```

  - Get Wallet Balance
  
  ```
  curl --location 'http://localhost:8080/api/balance/<WALLET_ADDRESS>'
  ```

  - Send Transaction
  
  ```
  curl --location 'http://localhost:8080/api/transfer' \
  --form 'departure="<SENDERS_WALLET_ADDRESS>"' \
  --form 'secret="<SENDERS_PRIVATE_KEY>"' \
  --form 'destination="<RECIEVERS_WALLET_ADDRESS>"' \
  --form 'amount="<TRX_TOKENS_AMOUNT>"'
  ```

  - Get Transaction History
  
  ```
  curl --location 'http://localhost:8080/api/transaction/<TRANSACTION_HASH>'
  ```

  - Get Transactions History of a Wallet
  
  ```
  curl --location 'http://localhost:8080/api/transactions' \
  --form 'departure="<SENDERS_WALLET_ADDRESS>"' \
  --form 'destination="<RECIEVERS_WALLET_ADDRESS>"'
  ```

  ## License

  This project is licensed under the [MIT](https://github.com/adeelch9/TRONWallet/blob/master/LICENSE) License.
