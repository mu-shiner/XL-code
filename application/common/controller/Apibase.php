<?php
/**
 * @author yunqi
 * @copyright 云旗工作室
 * @qq 157053220
 * Date 2018-05-09
 */

namespace app\common\controller;


use think\Cache;

class Apibase extends Base
{
    public  $module;
    public  $controller;
    public  $action;
    public  $check_token = true; //是否进行token验证  true => 是 false => 否
    public  $params;
    private $public_key="MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAn7jswOzNvLpen2c0jhkFct+skDonPawmgManYY+CZuK9\/hZkK65Ja3H8ExrzBS00n\/erbCNyAsh\/ltM\/N6Xju53jn7mCgESKU13QPtAk6HwQN83zDJMz7Pu1PL8oxN0XpIdAHQn3BAe\/mp4GMMcYEJ89+NxREKuACWmVC76q75CBsRRUDrSLo446S33yRZ0bgGYL\/0YxarLGIdc\/IF+Y44z+loVLH1j1+JYQKGJlRFcHk+M3jZNF0hLpCZhanFYXAHtzBTQly6dW+anqTcrTG7diZRo\/Zdy5Zmj3PGS3aMQ6UtMjvOyBNZNPL3aASW7jCMJ1XE43xSXcquqrMcNBHG6qCYtSIanZ32M\/IJTgd3Yldi7rdtPAs4L0pWghRYEX8ttZ+u3L\/x8K\/K\/6wLu62+D6OjX+KejVOCMMZNEqsNJGIO5nO44jmxPfsxLiudJbV+wUi6zN4pV6FWAJcA9vcWch9k0ipcR3Hga70RE7ElB5P82lJtHvRObV3x3ERIx62BMel5l\/osrETBtj+8W30n\/T46VN8y8q57\/leN9sKztEiX7DYarfAsv2Ay2Sw1tLEH9UJS2ArmOJs4GUIYoLARuWN199is9397pEF+tj8nsradN\/GNjVhXl8G+3TIU3rytCVgi+IllVxAWDVn2wdH4Zgs6gSBikvdTWA5TLGDt0CAwEAAQ==";
    private $private_key="MIIJQwIBADANBgkqhkiG9w0BAQEFAASCCS0wggkpAgEAAoICAQCfuOzA7M28ul6fZzSOGQVy36yQOic9rCaAxqdhj4Jm4r3+FmQrrklrcfwTGvMFLTSf96tsI3ICyH+W0z83peO7neOfuYKARIpTXdA+0CTofBA3zfMMkzPs+7U8vyjE3Rekh0AdCfcEB7+angYwxxgQnz343FEQq4AJaZULvqrvkIGxFFQOtIujjjpLffJFnRuAZgv\/RjFqssYh1z8gX5jjjP6WhUsfWPX4lhAoYmVEVweT4zeNk0XSEukJmFqcVhcAe3MFNCXLp1b5qepNytMbt2JlGj9l3LlmaPc8ZLdoxDpS0yO87IE1k08vdoBJbuMIwnVcTjfFJdyq6qsxw0EcbqoJi1IhqdnfYz8glOB3diV2Lut208CzgvSlaCFFgRfy21n67cv\/Hwr8r\/rAu7rb4Po6Nf4p6NU4Iwxk0Sqw0kYg7mc7jiObE9+zEuK50ltX7BSLrM3ilXoVYAlwD29xZyH2TSKlxHceBrvRETsSUHk\/zaUm0e9E5tXfHcREjHrYEx6XmX+iysRMG2P7xbfSf9PjpU3zLyrnv+V432wrO0SJfsNhqt8Cy\/YDLZLDW0sQf1QlLYCuY4mzgZQhigsBG5Y3X32Kz3f3ukQX62Pyeytp038Y2NWFeXwb7dMhTevK0JWCL4iWVXEBYNWfbB0fhmCzqBIGKS91NYDlMsYO3QIDAQABAoICAQCL51JR5tqLTn\/lhmQNd7NDHNMtfhKzPaB4OgmRNkAV2NRsxLY3YLFk0PHo1jk6No+a8zkPPrj14SOJPD5qgv9IbpNcbAT4T5EOVuU6r900Wr9l8hrL0ACyuwPUUujmD3dPIT0ycnEQ8ayORY7MmQfmP2XsHydOv7omBHqOUBOwRdakAldijhimScWcLJTTDztwq3IkxxowCKnuKHld5P6piu3RcK4NpkHF3cQHa7CiPM0hZ+xYMUD+Mpw3UGFGAU6imH92dmgIy+ouQ\/w86cUiyO4B+wy\/L6iiIJ03JE1XElK4C8OJD2xaPk8lFtpl+CAboyFjmgvVv0Vb3LPZodrcJ3v6OwgbClwJrM1hN5\/uIgrIHL9tp1r1kIYDDvHB9tud7Wnf92Nzlsk1hD2RhaYNbtFYHfhsymf20w0Ll190zeyuw+7f+U32UzXgiJiuJYtKgP5n+AP70yWQSNMSakroftE6CXzIMO1kQKYhFQqJrIuqokJf5b4IvjjYaD3qVmzibyLjvjCJBNbU1XZH1iZCbbUURs9gUoQaAQ+mR2k893QITmn0A6r1yPe0p6HF4\/IkCusH0eM9OrIb0rK3oFuPcbtT14nc\/i4CIA0smIuUdAlQJbVhOZmJT04dr4xqpRm\/FiQhRwP8wzhIBjQUoJz83pPYpuIHA13bsMRHYV0kbQKCAQEAzD2\/OPVGi+jjlxI9CsXP4PiZh\/mLad7QHi+ECX2ukHS342tkjkWhx5tgkaY0QaGUlCIoMsCaOzcjTXxg4lua5Hk4vcaUwMqyGb3Zq2exihzgPpzS7qmJQe7HwBj\/aNbvCnjHYigoaBoN\/D9GZynSV+OMDqFVJuc8OLfPA7mM0JZmGnTXZ1BK4RHg5hJGcRPqVa6FvViJCMc1ml4jjirLNt3nClJG\/Z2Bic+WfXOCW7cOB5kOYr\/RNmxWEfoGuk1vcCBtfmB3DQgNIBEVtDWMUNhTxQZrdQVozbJGa8uQVxhGyR1bmDdk9f5iY6KkuY1U9m9mFyMyOVVdIVxSezk0RwKCAQEAyDL+0ldVrlGTvURQkPrmOLhsL0DA6kY5TfdAxP7IfwKm8Vm\/nSLSwp4U1PGURXE\/QFO71utMEV174FeDumMFcLZqGR5SZmmWAIfgMs20WSFeZ3QIpZ1eRFXbr6qbZNMxewAYjxu290tZOxSWI1HOt1TMEsK3yfH6s+qrF7wewuABDj7ukOU72zH3tHAabTCKWXIxAOJzu+cRNJlW4ecbmIdwpzVExIMzbAAuZCEsczWgwSFNistbU\/plOSF2uOlJa1+Ab417Ziovh1xeNnL4EL7j6vUstJgZvej75IgNwNLq1eMesnGA09wDJxUrAlOm9ZA9c1hlBZssk4+frrapuwKCAQAT2ThZL7UAacycZOBbyKeQJHi\/NyguTMIK\/PEm\/vjU\/xLT3h7ymJ\/FRztWTEGt0yceUkd3zzHt6UjcAedkeCSQaZtzDfZs2gX+7G1X9AbT9kRwsUrqeL1nE+6Do6pGpQSsDtrJlj9\/BnEvf3K3GeBgPDPjRBiDZFt845gRs5mUt9kKXyqD1tDAGL2zfjksShQu6XwDz7PZj4qNCvMHoO8I0P3gCejuQr1RCESGwo\/7m8mYQW6deCi70kF3E8ZNmrLmwbACZRuMv6iO4joqvj15qdxSPHJ69+CchOPFHA5V+H0AlILPQyrjoSospqS9h5enL2JLg1chLUOUt2UopoGHAoIBAQCIVJnM+WZCiBD4oU3P\/NvMImKhpxH4N4wMeOSlge9c9pxi3MAsrKsjlu0OudhDlcQB9UOELsWlSajy9AWjZo0s3TvRESIB1cMtZ1oYnpZC4ANepdjBlzG7jVBGBwM9HMikP0N6KOBm9Ou13OYE9U+07szttaXcoqkb3iFWz2ePQ2XUoqxpBYHGWfeZ64FDe\/uqv6V5ObygZ9ECpa1RtsTHEOx5IUBMJiBdWp48145iglGPUzNv4d22iVxnFE+5yjuSH\/3heVJgFzg4kIEjyRT+qk+hEAa6kJ1vAvqN\/MyI09jRgJHF6J9XJosLEVIxim+259vA8aARukMX1YVjP8bDAoIBAEeNFuMctrFCaT1Ixc6OBfkn2HQX3EZ9uoMpFiaxPDZaYWWi3k0Km5dzOGkeHtihFvuKoaTKRwntdTWUU+wQ\/yFfCVZUPTsZ2f4vMh40YFgyCy3++01Rg0Bf16LGXZqKXinmJeInGD62D25t3CixRfTaIIY3juaWEJkSuADXopf9pHmSkob+IXXSe13XXH\/qdi+dco5LFvVS\/P6s0H5iLmyQ9HzFYClsquPa+EvXaKXZOoec5QJeDrZCnid8NGc5NVkd59iUeVdDv8PZRCHkMsDihON+yWojwDJ3MCAU8qJVxw4SDdeQK\/NbfyLFwf2A0=";

    /**
     * 初始化方法
     */
    public function _initialize(){
        $request = Request();
        parent::_initialize();
        $this->module = $request->module(); //当前模块
        $this->controller = $request->controller();//当前控制器
        $this->action = $request->action(); //当前方法名
        
      
        $this->params = input();
        if (isset($this->params[ 'encrypt' ]) && !empty($this->params[ 'encrypt' ])) {
            $this->decryptParams();
        }
    }
    
    
     /**
     * 错误返回值函数
     * @param int $code
     * @param string $message
     * @param string $data
     * @return array
     */
    public function error_req($code = -1, $message = '', $data = '')
    {
        $arr = [
            'code' => $code,
            'meg' => $message,
            'data' => $data
        ];
        $this->write_log($arr);
        return json_encode($arr);
    }

    /**
     * 返回值函数
     * @param int $code
     * @param string $message
     * @param string $data
     * @return array
     */
    public function success_req($data = '', $code = 200, $message = 'success')
    {  
        $arr = [
            'code' => $code,
            'meg' => $message,
            'data' => $data
        ];
        return json_encode($arr,true);
    }
    
   
    
      /**
     * api请求参数解密
     */
    private function decryptParams()
    {
        $decrypted = RSA::decrypt(urldecode($this->params[ 'encrypt' ]), $this->private_key, $this->public_key);
        if ($decrypted[ 'code' ] >= 0) {
            $this->params = json_decode($decrypted[ 'data' ], true);
        } else {
            $this->params = [];
        }
        
    }

    /**
     * 检测token(使用私钥检测)
     */
    public function checkToken()
    {
        if (empty($this->params[ 'token' ])) return $this->error_req(-1,'未登陆');
        $key = '';
        $key = $this->private_key . $key;
        
        $decrypt = $this->decrypt($this->params[ 'token' ], $key);
        if (empty($decrypt)) return $this->error_req(-2,'系统出错');

        $data = json_decode($decrypt, true);

        if (!isset($data[ 'users_id' ]) || empty($data[ 'users_id' ])) return $this->error_req(-3,'系统出错');


        if ($data[ 'expire_time' ] < time()) {
            return $this->error_req(-1,'登录失效');
        } else if (( $data[ 'expire_time' ] - time() ) < 300 && !Cache::get('member_token' . $data[ 'users_id' ])) {
            $this->refresh_token = $this->createToken($data[ 'users_id' ]);
            Cache::set('user_token' . $data[ 'users_id' ], $this->refresh_token, 360);
        }

        $this->users_id = $data[ 'users_id' ];
        
    
        return $this->success_req($data);
    }

    /**
     * 创建token
     * @param int $expire_time 有效时间  0为永久 单位s
     */
    public function createToken($users_id, $expire_time = 172800)
    {
        $key = '';
        
        $key = $this->private_key . $key;
        
        $data = [
            'users_id' => $users_id,
            'create_time' => time(),
            'expire_time' => empty($expire_time) ? 0 : time() + $expire_time
        ];
        $token = $this->encrypt(json_encode($data), $key);
        return $token;
    }
    
     /**
     * 创建token
     * @param int $expire_time 有效时间  0为永久 单位s
     */
    public function createShopToken($shop_id, $type = 0, $expire_time = 172800)
    {
        $key = '';
        
        $key = $this->private_key . $key;
        $data = [
            'create_time' => time(),
            'expire_time' => empty($expire_time) ? 0 : time() + $expire_time
        ];
        if($type == 1)
        {
            $data['service_id'] = $shop_id;
        }
        else
        {
            $data['shop_id'] = $shop_id;
        }
        
        $token = $this->encrypt(json_encode($data), $key);
        return $token;
    }
    
    
     /**
     * 检测token(使用私钥检测)
     */
    public function checkShopToken($type = 0)
    {
        if (empty($this->params[ 'token' ])) return $this->error_req(-1,'未登陆');
        $key = '';
        $key = $this->private_key . $key;
        
        $decrypt = $this->decrypt($this->params[ 'token' ], $key);
        if (empty($decrypt)) return $this->error_req(-2,'系统出错');

        $data = json_decode($decrypt, true);
        if($type == 1)
        {
            if (!isset($data[ 'service_id' ]) || empty($data[ 'service_id' ])) return $this->error_req(-3,'系统出错');
            if ($data[ 'expire_time' ] < time()) {
            return $this->error_req(-1,'登录失效');
            } else if (( $data[ 'expire_time' ] - time() ) < 300 && !Cache::get('member_token' . $data[ 'service_id' ])) {
                $this->refresh_token = $this->createToken($data[ 'service_id' ]);
                Cache::set('user_token' . $data[ 'service_id' ], $this->refresh_token, 360);
            }
    
            $this->service_id = $data[ 'service_id' ];
        }   
        else
        {
            if (!isset($data[ 'shop_id' ]) || empty($data[ 'shop_id' ])) return $this->error_req(-3,'系统出错');
            if ($data[ 'expire_time' ] < time()) {
            return $this->error_req(-1,'登录失效');
            } else if (( $data[ 'expire_time' ] - time() ) < 300 && !Cache::get('member_token' . $data[ 'shop_id' ])) {
                $this->refresh_token = $this->createToken($data[ 'shop_id' ]);
                Cache::set('user_token' . $data[ 'shop_id' ], $this->refresh_token, 360);
            }
    
            $this->shop_id = $data[ 'shop_id' ];
            
        }
        
    
        return $this->success_req($data);
    }
    
    
     /**
     * 系统加密方法
     *
     * @param string $data 要加密的字符串
     * @param string $key 加密密钥
     * @param int $expire 过期时间 单位 秒
     * @return string
     */
    public function encrypt($data, $key = '', $expire = 0)
    {
        $key = md5(empty($key) ? 'niucloud' : $key);
    
        $data = base64_encode($data);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
    
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
    
        $str = sprintf('%010d', $expire ? $expire + time() : 0);
    
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }
        return str_replace(array(
            '+',
            '/',
            '='
        ), array(
            '-',
            '_',
            ''
        ), base64_encode($str));
    }
    
    /**
     * 系统解密方法
     *
     * @param string $data
     *            要解密的字符串 （必须是encrypt方法加密的字符串）
     * @param string $key
     *            加密密钥
     * @return string
     */
    public function decrypt($data, $key = '')
    {
        $key = md5(empty ($key) ? 'niucloud' : $key);
        $data = str_replace(array(
            '-',
            '_'
        ), array(
            '+',
            '/'
        ), $data);
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
            if ($x == $l) {
                $x = 0;
            }
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


    /**
     * json返回
     * @param $data
     */
    public function respone_json($data){
        exit(json_encode($data));
    }
    
    /**
     * 创建不重复的编号(抵抗高并发)
     * @param $prefix 前缀
     * @param $unique 唯一值
     */
    function createno($key, $prefix, $unique)
    {
    
        $time_str = date('YmdHi');
        $max_no = \think\Cache::get($key . $prefix . '_' . $unique . "_" . $time_str);
        if (!isset($max_no) || empty($max_no)) {
            $max_no = 1;
        } else {
            $max_no = $max_no + 1;
        }
        $no = $prefix . $time_str . $unique . sprintf("%04d", $max_no);
        \think\Cache::set($key . $prefix . '_' . $unique . "_" . $time_str, $max_no);
        return $no;
    }
    
    /**
     * [write_log 写入日志]
     * @param  [type] $data [写入的数据]
     * @return [type]       [description]
     */
    public function write_log($data){ 
        $years = date('Y-m');
        $url = $_SERVER['DOCUMENT_ROOT'].'/log/'.$years.'/'.date('Ymd').'_request_log.txt'; 
        
        $dir_name=dirname($url);
          //目录不存在就创建
          if(!file_exists($dir_name))
          {
            //iconv防止中文名乱码
           $res = mkdir(iconv("UTF-8", "GBK", $dir_name),0777,true);
          }
          $fp = fopen($url,"a");//打开文件资源通道 不存在则自动创建       
        fwrite($fp,date("Y-m-d H:i:s").var_export($data,true)."\r\n");//写入文件
        fclose($fp);//关闭资源通道
    }
    
    
    //post请求
    public function PostCurls($url, $post, $headers=null)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
      
        if (!empty($headers)) {curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);} // 设置HTTP头信息
        $res = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $res; // 返回数据，json格式
    }
    //get请求
    public function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        //$this->write_log($output);
        return json_decode($output, true);
    }
    
    //隐藏部分字符串
    public function StrSubstrReplace($str, $replacement = '*', $start = 3, $length = 4)

    {
    
        $len = mb_strlen($str,'utf-8');
        //$this->write_log($str.$len);
        if ($len > intval($start+$length)) {
        
            $str1 = mb_substr($str,0,$start,'utf-8');
            
            $str2 = mb_substr($str,intval($start+$length),NULL,'utf-8');
        
        } elseif($len < $start) {
        
            $str1 = '';
            
            $str2 = mb_substr($str,$len-1,1,'utf-8');
            
            $length = $len - 1;
        
        }
        else {
        
            $str1 = mb_substr($str,0,1,'utf-8');
            
            $str2 = mb_substr($str,$len-1,1,'utf-8');
            
            $length = $len - 2;
        
        }
        
        $new_str = $str1;
        
        for ($i = 0; $i < $length; $i++) {
        
            $new_str .= $replacement;
        
        }
        
        $new_str .= $str2;
        
        return $new_str;
    
    }
    

}