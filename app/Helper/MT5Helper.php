<?php

namespace App\Helper;

use App\Models\Admin;
use App\Models\MT5Connect;
use GuzzleHttp\Client;
use App\Models\LiveAccount;
use App\Models\User;
use App\Repositories\LiveAccountRepository;

class MT5Helper
{
    protected static $mt5Url = 'http://79.143.176.19:17014/ManagerAPIFOREX/';

    protected static $session = 'sfkja3eipso3';
    protected static $managerIndex = '101';

    /**
     * @var LiveAccountRepository
     */
    private $liveAccountRepository;
    /**
     * MT4Connect constructor.
     */

    private function __construct(LiveAccountRepository  $liveAccountRepository)
    {
        $this->liveAccountRepository = $liveAccountRepository;
    }

    public static function openAccount($data)
    {
        $endpoint = self::$mt5Url . 'ADD_MT_USER';
        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'debug' => true
            ]
        ]);
        $mt5 = self::getMT5Connect();
        $data['Session'] = $mt5->session;
        $data['ManagerIndex'] = $mt5->manager_index;
        $body = json_encode($data);
        $response = $client->request('POST', $endpoint, ['body' => $body]);
        $result = json_decode($response->getBody(), true);
        return $result;
    }

    public static function updateAccount($type, $login, $data)
    {
        $mt5 = self::getMT5Connect();
        $endpoint = self::$mt5Url . $type . '?Session=' .$mt5->session . '&ManagerIndex=' . $mt5->manager_index . '&Account=' . $login;
        foreach ($data as $key => $value) {
            $endpoint = $endpoint . '&' . $key . '=' . $value;
        }
        $client = new Client();
        $response = $client->request('GET', $endpoint);
        $result = json_decode($response->getBody());
        return $result;
    }

    public static function getGroups()
    {
        $mt5 = self::getMT5Connect();
        $endpoint = self::$mt5Url . 'GET_GROUPS?Session=' . $mt5->session . '&ManagerIndex=' . $mt5->manager_index;
        $client = new Client();
        $response = $client->request('GET', $endpoint);
        $result = json_decode($response->getBody());
        return $result->lstGroups;
    }

    private static function connectMT5()
    {
        // $endpoint = self::$mt5Url . 'LOGIN_SESSION?Email=startingmt5broker@gmail.com&Password=rasa8r&Source=1';
        // $client = new Client();
        // $response = $client->request('GET', $endpoint);
        // $result = json_decode($response->getBody());
        // $mt5->session =  $result->Session;

        // $endpoint = self::$mt5Url . 'INITIAL_ADD_MANAGER';
        // $client = new Client([
        //     'headers' => [
        //         'Content-Type' => 'application/json',
        //         'debug' => true
        //     ]
        // ]);
        // $data = [
        //     "ManagerID" => 1480,
        //     "ManagerIndex" => 0,
        //     "MT4_MT5" => 1,
        //     "CreatedBy" => 1,
        //     "oStatus" => 1,
        //     "ServerConfig" => "174.142.252.29:443",
        //     "ServerCode" => "Live",
        //     "Password" => "G9istgg_",
        //     "oDemo" => 1,
        //     "Session" => $mt5->session
        // ];
        // $body = json_encode($data);
        // $response = $client->request('POST', $endpoint, ['body' => $body]);
        // $result = json_decode($response->getBody(), true);
        // $mt5->manager_index =  $result['Result'];
    }

    public static function getAccountInfo($login){
        $mt5 = self::getMT5Connect();
        $endpoint = self::$mt5Url . 'GET_USER_INFO?Session=' . $mt5->session. '&ManagerIndex=' .$mt5->manager_index . '&Account=' . $login;
        $client = new Client();
        $response = $client->request('GET', $endpoint);
        $result = json_decode($response->getBody());
        return $result;
    }

    public static function makeDeposit($data, $isReturn = true)
    {
        $mt5 = self::getMT5Connect();
        $endpoint = self::$mt5Url . 'MAKE_DEPOIST_BALANCE?Session=' . $mt5->session . '&ManagerIndex=' . $mt5->manager_index;
        foreach ($data as $key => $value) {
            $endpoint = $endpoint . '&' . $key . '=' . $value;
        }
        $client = new Client();
        $response = $client->request('GET', $endpoint);
        $result = json_decode($response->getBody());
        if($isReturn){
            return $result;
        }
    }

    public static function makeWithdrawal($data)
    {
        $mt5 = self::getMT5Connect();
        $endpoint = self::$mt5Url . 'MAKE_WITHDRAW_BALANCE?Session=' . $mt5->session . '&ManagerIndex=' . $mt5->manager_index;
        foreach ($data as $key => $value) {
            $endpoint = $endpoint . '&' . $key . '=' . $value;
        }
        $client = new Client();
        $response = $client->request('GET', $endpoint);
        $result = json_decode($response->getBody());
        return $result;
    }

    public static function getOpenedTrades($logins, $data)
    {
        $lots = 0;
        $commission = 0;
        $deposit = 0;
        $withdrawal = 0;
        $profit = 0;
        $trades = [];
        foreach ($logins as $login => $commissionValue) {
            $data['Account'] = $login;
            $result = self::getClosedAll($data);
            $tradeByLogin = $result->lstCLOSE;
            $deposit += $result->Depoist;
            $withdrawal += $result->Withdraw;
            $trades = array_merge($trades, $tradeByLogin);
            foreach ($tradeByLogin as $key => $trade) {
                if (strtotime($trade->Close_Time) - strtotime($trade->Open_Time) > 180) {
                    $lots += $trade->Lot;
                    $profit += $trade->Profit;
                    $symbol = $trade->Symbol;
                    if (in_array($symbol, config('trader_type.USStocks'))) {
                        $commission += round($trade->Lot * $commissionValue[0], 2);
                    } elseif (in_array($symbol, config('trader_type.Forex'))) {
                        $commission += round($trade->Lot * $commissionValue[1], 2);
                    } else {
                        $commission += round($trade->Lot * $commissionValue[2], 2);
                    }
                }
            }
        }
        return [$trades, $lots, $commission, $profit, $withdrawal, $deposit];
    }

    public static function getClosedAll($data)
    {
        $mt5 = self::getMT5Connect();
        $endpoint = self::$mt5Url . 'GET_CLOSED_ALL?Session=' . $mt5->session . '&ManagerIndex=' . $mt5->manager_index;
        foreach ($data as $key => $value) {
            $endpoint = $endpoint . '&' . $key . '=' . $value;
        }
        $client = new Client();
        $response = $client->request('GET', $endpoint);
        $result = json_decode($response->getBody());
        return $result->lstCLOSE;
    }

    public static function getMT5Connect(){
        return MT5Connect::first();
    }

    public static function changeInvestorPassword($data)
    {
        $mt5 = self::getMT5Connect();
        $endpoint = self::$mt5Url . 'CHANGE_INVESTOR_PASSWORD?Session=' . $mt5->session .  '&ManagerIndex=' . $mt5->manager_index .'&Account=' . $data['login'] . '&Password=' . $data['password'];
        $client = new Client();
        $response = $client->request('GET', $endpoint);
        $result = json_decode($response->getBody());
        return $result;
    }
}
