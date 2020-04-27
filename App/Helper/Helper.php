<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Helper
 *
 * @author Administrator
 */

namespace App\Helper;

class Helper
{

    /**
     * 返回用户错误信息
     * @param type $message
     * @param type $code
     * @param type $Throwable
     * @throws type
     */
    static public function useThrowable($message, $code, $Throwable = 'paramThrowable')
    {
        $namespace = '\App\Throwables\\' . $Throwable;
        throw new $namespace($message, $code);
    }

    /**
     * 用户密码加密方法，可以考虑盐值包含时间（例如注册时间），
     * @param string $pass 原始密码
     * @return string 多重加密后的32位小写MD5码
     */
   static public function encrypt_pass($pass)
    {
        if ('' == $pass) {
            return '';
        }
        $configInstance = \EasySwoole\EasySwoole\Config::getInstance();
        $salt = $configInstance->getConf('SYSTEM.pass_salt');
        return md5(sha1($pass) . $salt);
    }

    /**
     * 定义返回值格式
     * @param bool $status 返回状态
     * @param string $message 错误时返回错误内容
     * @param type $data 返回格式数据 array 或字符串
     * @return array
     */
    static public function msgStatus(bool $status, string $message = '', $data = null): array
    {
        return ['status' => $status, 'msg' => $message, 'data' => $data];
    }

    /**
     * 系统加密方法
     * @param string $data 要加密的字符串
     * @param string $key 加密密钥
     * @param int $expire 过期时间 单位 秒
     * @return string
     */
    static public function think_encrypt($data, $key = '', $expire = 0)
    {
        $configInstance = \EasySwoole\EasySwoole\Config::getInstance();
        $key = md5(empty($key) ? $configInstance->getConf('SYSTEM.pass_salt') : $key);
        $data = base64_encode($data);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l)
                $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        $str = sprintf('%010d', $expire ? $expire + time() : 0);

        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }

        $str = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
        return strtoupper(md5($str)) . $str;
    }

    /**
     * 系统解密方法
     * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
     * @param  string $key 加密密钥
     * @return string
     */
    static public function think_decrypt($data, $key = '')
    {
        $configInstance = \EasySwoole\EasySwoole\Config::getInstance();
        $key = md5(empty($key) ? $configInstance->getConf('SYSTEM.pass_salt') : $key);
        $data = substr($data, 32);
        $data = str_replace(array('-', '_'), array('+', '/'), $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);
        $expire = substr($data, 0, 10);
        $data = substr($data, 10);

        if ($expire > 0 && $expire < time()) {
            return '';
        }
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = $str = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l)
                $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }

}
