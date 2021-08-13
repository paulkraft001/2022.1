<?php
GLOBAL $config;
if ($config['debug'] === true) {
    ini_set('display_errors', 1);
}
function getRequest()
{
    $keys = array_keys($_GET);
    if (count($keys) == 0) {
        return "";
    }
    if (isset($_GET['u'])) {
        return sanitizeRequest($_GET['u']);
    }
    return sanitizeRequest($keys[0]);

}

function sanitizeRequest($request)
{
    $request = preg_replace('/[^A-Za-z0-9-_~]/', '', $request);
    return ($request);
}

function cacheContents($contents, $file)
{
    GLOBAL $_CWD, $config;
    if($config['cache_time']==0)return false;

    $success = file_put_contents($file, $contents);
    if (!$success) {
        //Failed to write. Lets try CHMOD and go again?
        chmod($_CWD . "/cache", 0777); //Advanced users may want to remove this line!
        $success = file_put_contents($file, $contents);
    }
    if (!$success) {
        debugMessage("CACHING ERROR! Run chmod 777 $_CWD/cache to enable write permissions.");
    }
}

function debugMessage($message)
{
    GLOBAL $config;
    if (isset($config['debug']) && $config['debug'] === true) {
        echo "<p>DEBUG MESSAGE: " . $message . "</p>\n<br>\n";
    }
}

function getProtocol()
{
    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $isSecure = true;
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
        $isSecure = true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        $isSecure = true;
    }
    return $isSecure ? 'https' : 'http';
}

function cpaBuildGetContents($url)
{
    GLOBAL $config;
    $ctx = stream_context_create(array(
        'http' =>
            array(
                'timeout' => 1, //1 second timeout
            )
    ));
    $mode=isset($config['curl_mode']) ? $config['curl_mode'] : "curl_pref";

    if ($mode=="curl_force" || (function_exists('curl_version') && in_array  ('curl', get_loaded_extensions()) && $mode!=="off")) {
        debugMessage("USING cURL MODE TO SEND REQUEST TO ".$url);
        try
        {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1); //1 second timeout
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        catch (Exception $exception){
            echo "Error trying to get url: $url using CURL mode. Error: ".$exception->getMessage();
        }

    }
    else{
        debugMessage("USING file_get_contents MODE TO SEND REQUEST TO ".$url);
        try{
            return file_get_contents($url,false,$ctx);
        }
        catch (Exception $exception){
            echo "Error trying to get url: $url using FILE_GET_CONTENTS mode. Error: ".$exception->getMessage();
        }
    }
    return '';
}