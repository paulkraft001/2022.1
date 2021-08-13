<?php
global $config, $_CWD, $statusArr, $current_url;
$statusArr = [];

if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['REQUEST_URI']))
{
    die('Missing required $SERVER variables. Please run from browser');
}
$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$current_url = substr($current_url, 0, strrpos($current_url, "/") + 1);
$current_dir = preg_replace('#/+#', '/', $_SERVER['REQUEST_URI']);
$current_dir = substr($current_dir, 0, strrpos($current_dir, "/"));

function changeStatus($id, $status)
{
    global $statusArr;
    $statusArr[$id] = $status;
    echo "<script>document.getElementById(\"$id\").className += \" status-$status\";</script>";
}

function mod_rewrite_support()
{
    if (!function_exists("apache_get_modules"))
        return false;
    return in_array("mod_rewrite", apache_get_modules());
}

function is_htaccess_running()
{
    global $current_url;
    return (cpaBuildGetContents($current_url . "prettyTest.php") == 1);
}

function ping($url)
{
    $html = "";
    echo "Attempting to ping URL: $url";
    try
    {
        $html = cpaBuildGetContents($url);
        if (!empty($html))
        {
            echo "<div class='status-success'>Successfully pinged $url</div>";
        }
    }
    catch (Exception $exception)
    {
        echo "ERROR PINGING URL: $url";
        var_dump($exception);
    }
    return $html;
}

if (empty($_CWD))
{
    die("Script must be run from index");
}


$target_config_file = $_CWD . "/config.php";
$target_cache_folder = $_CWD . "/cache";
$target_file_path = $_CWD . "/.htaccess";
$target_file_exists = file_exists($target_file_path);
$template_file_path = $_CWD . "/htaccess_template.txt";
$template_file_exists = file_exists($template_file_path);

$c = print_r($config, true);
?>
<html>
<head>
    <title>Installing CPABuild</title>
    <style>
        div.status {
            display: list-item;
            margin-left: 1.3em;
            margin-bottom: 10px;
            list-style-type: circle;
        }

        ol li {
            margin-top: 6px;
        }

        .status-error {
            color: #9c0000
        }

        .status-success {
            color: green
        }
    </style>
</head>
<body>
<h1>Installing CPABuild Deployment Package</h1>


<hr>
<h3 id="cache">Cache Folder</h3>
<div class='status status-normal'>Checking for /cache folder...</div>
<?php
$cache_exists = file_exists($_CWD . "/cache");
if ($cache_exists)
{
    echo "<div class='status status-success'>Cache folder exists!</div>";
    changeStatus("cache", "success");
} else
{
    echo "<div class='status status-error'>Cache folder does not exist! Attempting to create...</div>";
    mkdir($_CWD . "/cache/");
    file_put_contents($_CWD . "/cache/install_text.txt", "Hello world!");
    if (file_exists($_CWD . "/cache/install_text.txt"))
    {
        echo "<div class='status status-success'>Created cache test file!</div>";
        changeStatus("cache", "success");
    } else
    {
        echo "<div class='status status-error'>Failed to create writeable cache. This will slow down your website!
<br><br>
<strong style='font-size: 20px;'>IMPORTANT: To fix, please add write permissions for apache to folder $_CWD. This can be done in your FTP client, file manager, or terminal.</strong>
<br>
<pre>Example terminal command: sudo chmod -R 777 $_CWD</pre>
</div>";
        changeStatus("cache", "error");
    }
}
?>
<hr>
<h3 id="htaccess">Pretty URL Install</h3>
<p>
    Pretty URL test: <a href='<?php echo $current_url ?>cpabuild-deployment-test'
                        target='_blank'><?php echo $current_url ?>cpabuild-deployment-test</a>
</p>
<p>
    Non-Pretty URL test: <a href='<?php echo $current_url ?>?cpabuild-deployment-test'
                            target='_blank'><?php echo $current_url ?>?cpabuild-deployment-test</a> (Notice the added ?
    question mark)
</p>
<div class='status status-normal'>Checking for .htaccess file...</div>
<?php


if ($target_file_exists && is_htaccess_running())
{
    echo "<div class='status status-success'>Htaccess file is set in place and appears to be working. Please test with the Pretty URL link above.</div>";
    changeStatus("htaccess", "success");
} else
{
    if (!$template_file_exists)
    {
        echo "<div class='status status-error'>Missing required HTACCESS TEMPLATE file. Please download the package and try again. Tried to get template from file: $template_file_path</div>";
        changeStatus("htaccess", "error");
    } else
    {
        $htaccess_contents = file_get_contents($template_file_path);

        if (strpos($_CWD, $current_dir) !== false)
        {
            $htaccess_contents = str_replace("#RewriteBase", "RewriteBase " . $current_dir, $htaccess_contents);
        }

        file_put_contents($target_file_path, $htaccess_contents);

        $target_file_exists = file_exists($target_file_path);
        $htaccess_is_running = is_htaccess_running();

        if ($target_file_exists && $htaccess_is_running)
        {
            echo "<div class='status status-success'>Htaccess file is set in place and appears to be working. Please test with the Pretty URL link above.</div>";
            changeStatus("htaccess", "success");
            unlink($template_file_path);
        } elseif (!$target_file_exists)
        {
            echo "<div class='status status-error'>Server rejected file creation. PHP does not have permission to write files. Give apache write permissions or use non-pretty formats.</div>";
            echo "<div class='status status-error'>You can also manually rename <pre>$template_file_path</pre> to <pre>$target_file_path</pre></div>";
            changeStatus("htaccess", "error");
        } else
        {
            echo "<div class='status status-error'>Creation was successful but Htaccess does not seem to be running. Fix with <strong>AllowOverride All</strong> in apache settings, or just use non-pretty urls.</div>";
            changeStatus("htaccess", "error");
        }
    }
}
?>
<hr>
<h1>Summary</h1>
<ol>
    <?php
    if ($statusArr['cache'] == 'error')
    {
        echo "<li class='status status-error'>The cache did not install successfully. Please follow the instructions in the <u>Cache Folder</u> section.</li>";
    }
    if ($statusArr['htaccess'] == 'error')
    {
        echo "<li class='status status-error'>Htaccess installation was not successful. Use non-pretty format for urls or attempt to fix by following instructions in that section.</li>";
        if ($target_file_exists)
            echo "<li class='status status-error'>If you get a <strong>403 Forbidden error</strong>, delete the .htaccess (you may have to enable \"hidden\" files in your file manager).</li>";
    }
    ?>
    <li>Start adding URL's to your website here: <a target="_blank" href="https://members.cpabuild.com/privateURL">https://members.cpabuild.com/privateURL</a>
    </li>
    <li>Change your homepage (currently this page). Edit the file <strong>config.php</strong> and change the line
        <pre>"default"=>"install",</pre>
        to
        <pre>"default"=>"my-private-uri",</pre>
        Where <strong>my-private-uri</strong> is the URI from CPABuild (you can use any private uri as a homepage).
    </li>
    <li>When you are satisfied, delete the <strong>install.php</strong> file. If you ever need to run the installation
        again, just re-download the package from CPABuild.
    </li>
    <li>You can check if your package is up to date by visiting <a href="<?php echo $current_url; ?>version.php"
                                                                   target="_blank"><?php echo $current_url; ?>
            version.php</a></li>
</ol>
<hr>
<h3 id="Ping">Ping Tests</h3>
<?php
ping("https://www.google.com");
$ping = ping("http://deployment.cpabuild.com/ping.php");
if ($ping !== "PONG")
{
    echo "<div class='status-error'>Expected PONG response. Instead got:</div>";
    var_dump($ping);
}
ping("http://deployment.cpabuild.com/api.php");

?>
<hr>
<h3 id="Debug">Debug Information</h3>
<ul>
    <li>Target config file: <?php echo $target_config_file ?></li>
    <li>Target cache folder: <?php echo $target_cache_folder ?></li>
    <li>Target .htaccess template: <?php echo $template_file_path ?></li>
</ul>
<h4 id="Config">Config Dump</h4>
<pre><?php echo $c ?></pre>
</body>
</html>