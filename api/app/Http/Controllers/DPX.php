<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use Tron\Address;
use Carbon\Carbon;

class DPX extends Controller
{
    const URI = 'https://nile.trongrid.io';
    public static function GenerateTRXAddress()
    {
        try {
            $api = new \Tron\Api(new Client(['base_uri' => self::URI]));
            $trxWallet = new \Tron\TRX($api);
            $addressData = $trxWallet->generateAddress();

            $config = [
                'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t', // USDT TRC20
                'decimals' => 6,
            ];

            $trc20Wallet = new \Tron\TRC20($api, $config);
            $addressData = $trc20Wallet->generateAddress();

        } catch (\Exception $e) {
            return null;
        }
        return ['addressData' => $addressData];
    }

    public static function ValidateTRXAddress(string $address, string $privateKey, string $hexAddress)
    {
        $wallet = Wallet::where('wallet', $address)->first();

        if ($wallet) {

            try {
                $api = new \Tron\Api(new Client(['base_uri' => self::URI]));
                $trxWallet = new \Tron\TRX($api);
                $tronAddress = new \Tron\Address($address, $privateKey, $hexAddress);
                $addressData = $trxWallet->validateAddress(
                    $tronAddress
                );

            } catch (\Exception $e) {
                return ['addressData' => false];
            }

            if ($addressData) {
                return ['addressData' => $addressData];
            }
        }

        return ['addressData' => false];
    }


    public static function CreateWallet(null|string $wallet = null)
    {

        if (!$wallet) {
            $wallet = DPX::GenerateTRXAddress();
        }

        // Access the 'addressData' object within the array
        if (isset($wallet['addressData']) && is_object($wallet['addressData'])) {

            $addressData = $wallet['addressData'];
            // Access the properties directly
            $address = $addressData->address;
            $privateKey = $addressData->privateKey;

            // Insert the wallet into the Wallet table
            Wallet::insert([
                'wallet' => $address,
                'secret' => Hash::make($privateKey),
                'hexAddress' => $addressData->hexAddress
            ]);

            // Return the address and private key
            return [
                'wallet' => $address,
                'secret' => $privateKey,
                'hexAddress' => $addressData->hexAddress
            ];
        } else {
            // Handle the case where addressData is not set or not an object
            throw new \Exception('Address data is missing or not an object.');
        }
    }


    public static function Transfer(string $departure, string $destination, float $amount, string $secret, float $fee = null)
    {
        $wallet = Wallet::where('wallet', $departure)->first();

        if (!$wallet) {

            return API::Error('invalid-wallet', 'Wallet address does not exist on the Miniapp.');

        } else {

            try {
                $api = new \Tron\Api(new Client(['base_uri' => self::URI]));
                $trxWallet = new \Tron\TRX($api);

                $from = $trxWallet->privateKeyToAddress($secret);
                $to = new Address(
                    $destination,
                    '',
                    $trxWallet->tron->address2HexString($destination)
                );
                $transferData = $trxWallet->transfer($from, $to, $amount);

                $responseData = [
                    "transaction" => $transferData->txID,
                    "departure" => $departure,
                    "destination" => $destination,
                    "amount" => $amount,
                    "fee" => $fee ?? "0.2", // Set a default fee if not provided
                    "timestamp" => Carbon::createFromTimestampMs($transferData->raw_data['timestamp'])->toDateTimeString(), // Convert to seconds
                ];

                Transaction::insert($responseData);

            } catch (\Exception $e) {
                return API::Error('error', $e->getMessage());
            }
        }

        return API::Respond($responseData ?? []);
    }

    public static function Verify(string $wallet, string $secret, string $hexAddress)
    {

        $validatedAddress = DPX::ValidateTRXAddress($wallet, $secret, $hexAddress);

        if ($validatedAddress['addressData'] === true) {

            return true;
        }

        return false;
    }


    public static function GetBalance(string $wallet)
    {

            $api = new \Tron\Api(new Client(['base_uri' => self::URI, 'timeout' => 100000]));
            $trxWallet = new \Tron\TRX($api);

            $address = new Address(
                $wallet,
                '',
                $trxWallet->tron->address2HexString($wallet)
            );

            $balanceData = (string) $trxWallet->balance($address);



            return API::Respond($balanceData);

    }

    public static function GetTransaction(string $transaction)
    {

        $transactionInfo = Transaction::where(['transaction' => $transaction])->first(['transaction', 'departure', 'destination', 'amount', 'fee', 'timestamp']);

        return $transactionInfo ? API::Respond($transactionInfo) : API::Error('invalid-transaction', 'Transaction is invalid');
    }

    public static function GetTransactions(int $offset = 0, string|null $departure = null, string|null $destination = null)
    {

        if ($departure && $destination) {

            if ($departure === $destination) {

                $transactions = Transaction::where(['departure' => $departure])
                    ->orWhere(['destination' => $destination])
                    ->orderby('id', 'DESC')
                    ->limit(env('TRANSACTIONS_PER_FETCH', 250))
                    ->offset($offset)
                    ->get(['transaction', 'departure', 'destination', 'amount', 'fee', 'timestamp']);
            } else {

                $transactions = Transaction::where(['departure' => $departure, 'destination' => $destination])
                    ->orderby('id', 'DESC')
                    ->limit(env('TRANSACTIONS_PER_FETCH', 250))
                    ->offset($offset)
                    ->get(['transaction', 'departure', 'destination', 'amount', 'fee', 'timestamp']);
            }
        } else if ($departure) {

            $transactions = Transaction::where(['departure' => $departure])
                ->orderby('id', 'DESC')
                ->limit(env('TRANSACTIONS_PER_FETCH', 250))
                ->offset($offset)
                ->get(['transaction', 'departure', 'destination', 'amount', 'fee', 'timestamp']);
        } else if ($destination) {

            $transactions = Transaction::where(['destination' => $destination])
                ->orderby('id', 'DESC')
                ->limit(env('TRANSACTIONS_PER_FETCH', 250))
                ->offset($offset)
                ->get(['transaction', 'departure', 'destination', 'amount', 'fee', 'timestamp']);
        } else {

            $transactions = Transaction::orderby('id', 'DESC')
                ->limit(env('TRANSACTIONS_PER_FETCH', 250))
                ->offset($offset)
                ->get(['transaction', 'departure', 'destination', 'amount', 'fee', 'timestamp']);
        }

        $transactions = json_decode(json_encode($transactions), true);

        return API::Respond($transactions, 'success');
    }

}
