<?php
header('HTTP/1.1 400 Not Found', true, 400);
?>
<html>
<head></head>
<body>
<p>The page your were looking for could not be found.</p>
<h1>Webmaster</h1>
<p>You are seeing this page because the request to our server for this url failed. Here are things you can try: </p>
<ul>
	<li>Make sure the URI is valid.</li>
	<hr>
	<br>
	<li>In config.php, you can change
		<pre>"debug" => false,</pre>
		to
		<pre>"debug" => true,</pre>
		to show errors and debug information.
	</li>
	<hr>
	<br>
	<li>In config.php, you can change
		<pre>"curl_mode" => false,</pre>
		to
		<pre>"curl_mode" => true,</pre>
		to switch request modes. This can solve some errors.
	</li>
	<hr>
	<br>
	<li>Make sure your server doesn't have a firewall blocking outgoing requests.</li>
	<hr>
	<br>
	<li>Contact a CPABuild admin or support member for more help.</li>
	<hr>
	<br>
</ul>
<p><strong>This page is shown to visitors when the URL can't be found. Edit it by changing 404.php</strong></p>
</body>
</html>