<?php

namespace App\Helper;

use App\Models\Admin;
use App\Models\LiveAccount;
use App\Models\User;
use App\Repositories\LiveAccountRepository;
use Exception;
use DateTime;
use DateTimeZone;

class MT4Connect
{
//    /**
//     * @var LiveAccountRepository
//     */
//    private $liveAccountRepository;
//
//    /**
//     * MT4Connect constructor.
//     */
//    public function __construct(LiveAccountRepository  $liveAccountRepository)
//    {
//        $this->liveAccountRepository = $liveAccountRepository;
//    }
//
//    public static function connect()
//    {
//        return fsockopen(config('mt4.vps_ip'), config('mt4.vps_port'), $errno, $errstr, 6);
//    }
//
//    public static function deleteMultiLiveAccount($logins)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $message = '';
//            foreach ($logins as $login) {
//                $cmd = 'action=deleteaccount&login=' . $login;
//                fwrite($fp, $cmd);
//                stream_set_timeout($fp, 1);
//                $result = '';
//                $info = stream_get_meta_data($fp);
//                while (!$info['timed_out'] && !feof($fp)) {
//                    $str = @fgets($fp, 1024);
//                    if (strpos($str, 'login')) {
//                        $result .= $str;
//                        $info = stream_get_meta_data($fp);
//                    }
//                }
//                $result = explode('&', $result);
//                $result =  explode('=', $result[0])[1];
//                if ($result != 1) {
//                    $message =  "Xóa tài khoản thất bại";
//                    break;
//                }
//            }
//            fclose($fp);
//            return $message;
//        } catch (Exception $e) {
//            return "Xóa tài khoản thất bại";
//        }
//    }
//
//    public function deleteLiveAccount($login)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $message = '';
//            $cmd = 'action=deleteaccount&login=' . $login;
//            fwrite($fp, $cmd);
//            stream_set_timeout($fp, 1);
//            $result = '';
//            $info = stream_get_meta_data($fp);
//            while (!$info['timed_out'] && !feof($fp)) {
//                $str = @fgets($fp, 1024);
//                if (strpos($str, 'login')) {
//                    $result .= $str;
//                    $info = stream_get_meta_data($fp);
//                }
//            }
//            $result = explode('&', $result);
//            $result =  explode('=', $result[0])[1];
//            if ($result != 1) {
//                $message =  "Xóa tài khoản thất bại";
//            }
//            fclose($fp);
//            return $message;
//        } catch (Exception $e) {
//            return "Xóa tài khoản thất bại";
//        }
//    }
//
//    public static function openLiveAccount($data)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $cmd = 'action=createaccount&login=next';
//            foreach ($data as $key => $value) {
//                $cmd = $cmd . '&' . $key . '=' . $value;
//            }
//            fwrite($fp, $cmd);
//            stream_set_timeout($fp, 1);
//            $result = '';
//            $info = stream_get_meta_data($fp);
//            while (!$info['timed_out'] && !feof($fp)) {
//                $str = @fgets($fp, 1024);
//                if (strpos($str, 'login')) {
//                    $result .= $str;
//                    $info = stream_get_meta_data($fp);
//                }
//            }
//            fclose($fp);
//            $result = explode('&', $result);
//            $data['login'] = explode('=', $result[1])[1];
//            return $data['login'];
//        } catch (Exception $e) {
//            return "Xóa tài khoản thất bại";
//        }
//    }
//
//    public static function updateLiveAccount($data)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $cmd = 'action=modifyaccount';
//            foreach ($data as $key => $value) {
//                $cmd = $cmd . '&' . $key . '=' . $value;
//            }
//            fwrite($fp, $cmd);
//            stream_set_timeout($fp, 1);
//            $result = '';
//            $info = stream_get_meta_data($fp);
//            while (!$info['timed_out'] && !feof($fp)) {
//                $str = @fgets($fp, 1024);
//                if (strpos($str, 'login')) {
//                    $result .= $str;
//                    $info = stream_get_meta_data($fp);
//                }
//            }
//            fclose($fp);
//        } catch (Exception $e) {
//            return "Cập nhật tài khoản thất bại";
//        }
//    }
//
//    public static function getOpenedTrades($loginArray, $from, $to)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $logins = array_keys($loginArray);
//            $loginString = '';
//            foreach ($logins as $login) {
//                $loginString .= $login . ';';
//            }
//            $logins = (rtrim($loginString, ';'));
//            $cmd = 'action=gethistory&from=' . $from . '&to=' . $to  . '&login=' . $logins;
//            fwrite($fp, $cmd);
//            stream_set_timeout($fp, 1);
//            $result = '';
//            $info = stream_get_meta_data($fp);
//            while (!$info['timed_out'] && !feof($fp)) {
//                $str = @fgets($fp, 1024);
//                if (strpos($str, 'size')) {
//                    $size = explode('size', $str);
//                    $userAmount = $size[1];
//                    if ($userAmount == "=0\n") {
//                        break;
//                    }
//                }
//                if (strpos($str, ';')) {
//                    $result .= $str;
//                    $info = stream_get_meta_data($fp);
//                }
//            }
//            $array = array();
//            $lots = 0;
//            $commission = 0;
//            if (!empty($result)) {
//                $result = explode('&', $result);
//                fclose($fp);
//                $lines = explode("\n", $result[0]);
//                foreach ($lines as $line) {
//                    $line = $array[] = explode(";", $line);
//                    if ($line[8] - $line[7] > 180) {
//                        $lots += round($line[6] / 100, 2);
//                        $commission += round($line[6] / 100 * $loginArray[$line[0]], 2);
//                    }
//                }
//            }
//            return [$array, $lots, $commission];
//        } catch (Exception $e) {
//            return "Hệ thống đang bị lỗi. Vui lòng thử lại sau ";
//        }
//    }
//
//    public function transferCommission()
//    {
//        $admins = Admin::where('role', config('role.staff'))->get();
//        $to = strtotime('now');
//        $from = strtotime('-1 week');
//        foreach ($admins as $key => $admin) {
//            $logins = $this->liveAccountRepository->getLoginsByAdmin($admin);
//            $result = self::getOpenedTrades($logins, $from, $to);
//            $commission = $result[2];
//            if ($commission) {
//                $userId = User::where('email', $admin->email)->pluck('id');
//                $account = LiveAccount::where('user_id', $userId[0])->pluck('login');
//                if (count($account)) {
//                    self::changeBalance($account[0], $commission, 'transfer commission', false);
//                }
//            }
//        }
//    }
//
//    public static function reportByLogin($logins, $from, $to)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $logins = implode(";", $logins);
//            $cmd = 'action=gethistory&from=' . $from . '&to=' . $to . '&login=' . $logins;
//            fwrite($fp, $cmd);
//            stream_set_timeout($fp, 1);
//            $result = '';
//            $info = stream_get_meta_data($fp);
//            while (!$info['timed_out'] && !feof($fp)) {
//                $str = @fgets($fp, 1024);
//                if (strpos($str, 'size')) {
//                    $size = explode('size', $str);
//                    $userAmount = $size[1];
//                    if ($userAmount == "=0\n") {
//                        break;
//                    }
//                }
//                if (strpos($str, ';')) {
//                    $result .= $str;
//                    $info = stream_get_meta_data($fp);
//                }
//            }
//            $array = [];
//            if (!empty($result)) {
//                $result = explode('&', $result);
//                fclose($fp);
//                $lines = explode("\n", $result[0]);
//                foreach ($lines as $line) {
//                    $array[] = explode(";", $line);
//                }
//            }
//            return $array;
//        } catch (Exception $e) {
//            return "Hệ thống đang bị lỗi. Vui lòng thử lại sau ";
//        }
//    }
//
//
//    public function changeBalance($login, $value, $comment, $isReturn = true)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $cmd = 'action=changebalance&login=' . $login . '&value=' . $value . '&comment=' . $comment;
//            fwrite($fp, $cmd);
//            stream_set_timeout($fp, 1);
//            $result = '';
//            $info = stream_get_meta_data($fp);
//            while (!$info['timed_out'] && !feof($fp)) {
//                $str = @fgets($fp, 1024);
//                if (strpos($str, 'login')) {
//                    $result .= $str;
//                    $info = stream_get_meta_data($fp);
//                }
//            }
//            fclose($fp);
//            if ($isReturn) {
//                return $result;
//            }
//        } catch (Exception $e) {
//            return "Lỗi hệ thống";
//        }
//    }
//
//    public function getEquityBalanceByLogin($login)
//    {
//        try {
//            $fp = self::connect();
//            if (!$fp) {
//                return 'Không thể kết nối tới MT4';
//            }
//            $cmd = 'action=getaccountinfoex&login=' . $login;
//            fwrite($fp, $cmd);
//            stream_set_timeout($fp, 1);
//            $result = '';
//            $info = stream_get_meta_data($fp);
//            while (!$info['timed_out'] && !feof($fp)) {
//                $str = @fgets($fp, 1024);
//                if (strpos($str, 'login')) {
//                    $result .= $str;
//                    $info = stream_get_meta_data($fp);
//                }
//            }
//            $result = explode('&', $result);
//            $equity =  (explode('=', $result[28])[1]);
//            fclose($fp);
//            return $equity;
//        } catch (Exception $e) {
//            return "Lỗi hệ thống";
//        }
//    }
}
