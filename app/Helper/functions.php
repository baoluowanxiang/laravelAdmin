<?php
/**
 * 自定义函数库
 * @filename  helpers.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2017-8-9 17:08:32
 */


/**
 * Models类 快捷函数
 * @param $classname
 * @param string $path
 * @return \Illuminate\Database\Eloquent\Model
 */
function M($classname, $path='Models')
{
   return load_class($classname,$path);
}


/**
 * 服务类快捷函数
 * @param $classname
 * @param string $path
 * @return \App\Services\Service;
 */
function S($classname, $path='Services')
{
    return load_class($classname.'Service',$path);
}


/**
 * 加载类,单例模式实例化
 * @param $classname
 * @param $path
 * @return mixed
 * @throws Exception
 */
function load_class($classname,$path)
{
    $classname = ucfirst($classname);
    $class = "\\App\\".$path."\\". $classname;
    if (!class_exists($class)) {
        throw new \Exception('找不到文件'.$class);
    }
    static $classes = [];
    $key = md5($class);
    if (!isset($classes[$key])) {
        //$classes[$key] = (new ReflectionClass($class))->newInstance();
        $classes[$key] = new $class;
    }
    return $classes[$key];
}



/**
 * 数组转树
 * @param type $list
 * @param type $root
 * @param type $pk
 * @param type $pid
 * @param type $child
 * @return type
 */
function list_to_tree($list, $root = 0, $pk = 'id', $pid = 'parentid', $child = '_child')
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = 0;
            if (isset($data[$pid])) {
                $parentId = $data[$pid];
            }
            if ((string)$root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

function node_tree($arr, $id = 0, $level = 0)
{
    static $array = array();
    foreach ($arr as $v) {
        if ($v['parentid'] == $id) {
            $v['level'] = $level;
            $array[] = $v;
            node_tree($arr, $v['id'], $level + 1);
        }
    }
    return $array;
}


function arr2str($arr, $str = '')
{

    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                return arr2str($v, $str);
            } else {
                $str .= "<p>{$k}-->{$v}</p>";
            }
        }
    }
    return $str;
}


/**
 * curl
 * @param type $url
 * @param type $postFields
 * @param type $headers
 * @return type
 * @throws Exception
 */
function my_curl($url, $postFields = null, $headers = '', $readTimeout = 5, $connectTimeout = 5)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($readTimeout) {
        curl_setopt($ch, CURLOPT_TIMEOUT, $readTimeout);
    }
    if ($connectTimeout) {
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
    }
    //https 请求
    if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    if (is_array($postFields) && 0 < count($postFields)) {

        $postBodyString = "";
        $postMultipart = false;
        //只支持一维数组.......
        foreach ($postFields as $k => $v) {
            if ("@" != substr($v, 0, 1)) {//判断是不是文件上传
                $postBodyString .= "$k=" . urlencode($v) . "&";
            } else {//文件上传用multipart/form-data，否则用www-form-urlencoded
                $postMultipart = true;
            }
        }
        unset($k, $v);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($postMultipart) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
        }
    } else {
        //post 数据是字符串name=du&age=10
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    }

    $reponse = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $reponse;
}

/**
 * 发邮件
 * @param string $subject 主题
 * @param string $str 内容
 * @param string $to 收件人 多人以 逗号 分隔
 * @param string $attach 附件 多个以 逗号 分隔
 * @param string $blade 模板
 * @param string $send_name 发件人 1系统,2客服
 */
function send_mail($subject, $str = '', $to = '', $attach = '', $blade = '', $send_name = 1)
{
    if (!$to) {
        $to_arr = explode(',', config('mail.to_email'));
    } else {
        if (is_array($to)) {
            $to_arr = $to;
        } else {
            $to_arr = explode(',', $to);
        }
    }

    if ($attach) {
        if (is_array($attach)) {
            $attach_arr = $attach;
        } else {
            $attach_arr = explode(',', $attach);
        }
    }
    if (!$blade) {
        $blade = 'emails.mail';
    }

    if ($send_name == 2) {
        //使用客服邮件
        config(['mail.from.address' => env('MAIL_USERNAME2')]);
        config(['mail.from.name' => env('MAIL_FROM_NAME2')]);
        config(['mail.username' => env('MAIL_USERNAME2')]);
        config(['mail.password' => env('MAIL_PASSWORD2')]);
    }
    \Mail::send($blade, ['str' => $str], function ($message) use ($subject, $to_arr, $attach_arr) {

        $message->subject($subject . '[' . config('app.env') . ']');

        foreach ($to_arr as $mail) {
            $message->to($mail);
        }

        if (is_array($attach_arr) && count($attach_arr) > 0) {
            foreach ($attach_arr as $file) {
                $message->attach($file);
            }
        }
    });
}

//获取参数链接
function getParams($arr, $key, $params = '')
{
    if (!$params) {
        $params = request()->all();
    }
    foreach ($arr as $k => $v) {
        if (isset($params[$key])) {
            unset($params[$key]);
        }
        $arr[$k] = ['name' => $v['name'], 'val' => $v['val'], 'url' => http_build_query(array_merge([$key => $v['val']], $params))];
    }
    return $arr;
}


/**
 * 数组排序，按指定的KEY
 * @author Zhenxun Du <5552123@qq.com>
 * @date    2015年9月23日 17:01:42
 * @param $array 要排序的2维数组
 * @param $key   要排序的key
 * @param $orderBy asc从小到大，desc从大到小
 */
function sort2array($array, $key, $orderBy = 'asc')
{
    usort($array, function ($a, $b) use ($key, $orderBy) {
        return $orderBy == 'asc' ? strnatcmp($a[$key], $b[$key]) : strnatcmp($b[$key], $a[$key]);
    });
    return $array;
}

/**
 * 保存远程图片
 * @param type $url
 * @param type $filePath
 * @param type $fileName
 * @return type
 */
function saveHttpFile($url, $filePath, $fileName)
{

    $up_file_path = config('app.file_path') . $filePath;

    if (!file_exists(public_path() . '/' . $up_file_path)) {
        mkdir(public_path() . '/' . $up_file_path, 0755, true);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    file_put_contents(public_path() . '/' . $up_file_path . '/' . $fileName, curl_exec($ch));
    curl_close($ch);
    return $filePath . '/' . $fileName;
}





