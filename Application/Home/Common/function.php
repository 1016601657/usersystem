<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/14
 * Time: 23:44
 * 公用方法
 */

/**
 * 判断一个手机号码是否是合法的手机号
 * @param $mobileNumber
 * @return bool
 */
function checkMobileWithRegex($mobileNumber)
{
    if (preg_match('/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])[0-9]{8}$/', $mobileNumber)) {
        return true;
    }
    return false;
}

/**
 * COOKIE
 * @param $var
 * @param $value
 * @param int $life_time
 * @param int $prefix
 * @return bool
 */
function usetcookie($var, $value, $life_time = 0, $prefix = 1)
{
    $cookiepre = C('COOKIE_PREFIX');
    $cookiepath = C('COOKIE_PATH');
    $cookiedomain = C('COOKIE_DOMAIN');
    $time_stamp = C('COOKIE_EXPIRE');
    return setcookie(($prefix ? $cookiepre : '') . $var, $value,
        $life_time ? $time_stamp + $life_time : 0, $cookiepath,
        $cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

/**
 * COOKIE加密
 * @param $string
 * @param string $operation
 * @param string $key
 * @param int $expire
 * @return string
 */
function authcode($string, $operation = "ENCODE", $key = '', $expire = 0)
{
    $auth_key = C('COOKIE_AUTH_KEY');
    $ckey_length = 4;
    $key = md5($key ? $key : $auth_key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expire ? $expire + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
/**
 * 根据用户COOKIE判断登录状态
 * @return bool
 */
function checkUserLoginStatusByCookie()
{
    //判断cookie[AUTH]是否为空
    if (empty(cookie("AUTH"))) {
        return false;
    }

    //解密COOKIE
    $auth = json_decode(authcode(cookie("AUTH"), 'DECODE', C('COOKIE_AUTH_KEY')));

    //判断cookie是否合法
    if (intval($auth->uuid)) {
        //查询用户信息
        $Users = D('Users');
        $userInfo = $Users->getUserInfoByID($auth->uuid);
        $md5hash = md5($auth->uuid . $userInfo['tel_num']);

        //验证cookie是否合法
        if (strcmp($auth->ukey, $md5hash) == 0) {
            session('uid', intval($auth->uuid));
            session('uname', $userInfo['uname']);
            session('tel_num', $userInfo['tel_num']);
            return true;
        }
        return false;
    }
    return false;
}