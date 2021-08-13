<?php
global $config;

$config = array(
    "api_key" => "cb10677639f446a5f622",
    /*
     * Your API Key - This should be placed for you automatically when you download the package from CPABuild
     * You can see your API Key here: https://members.cpabuild.com/apiKey (Public API key)
     */

    "debug" => false,
    /*
     * Setting this to true will show live errors AND dump important server variables
     * Proceed with extreme caution. We do not recommend setting this to true unless specifically instructed to by technical support
     */

    "default" => "install",
    /*
    * This is the default URI that is used when someone visits the root of the directory
    * The default is "install" - this is a keyword that displays the installation process
    * Once installation is complete, you can change this to a private uri found here https://members.cpabuild.com/privateURL (use the bold text in the "On Your Website" column).
    * Example: my-landing-page
    */


    "cache_time" => 86400,
    /*
    * Time in seconds to store cache HTML files. Common values are:
    * 86400 for 1 day
    * 3600 for 1 hour
    * 0 to not use the cache at all
    * -1 to store cache files forever
    * You can always clear the cache manually by deleting all files in the /cache folder
    */

    "curl_mode" => "curl_pref",
    /*
    * "curl_pref" - Use cURL if its enabled on the server (default)
    * "curl_force" - Force cURL, skip server check
    * "off" - Use the native file_get_contents() PHP function to make requests
    *
    * Using cURL can solve error messages like "http:// wrapper is disabled" or "allow_url_fopen() disabled"
    * Using file_get_contents() ("off" mode) can solve error messages that involve cURL not being installed/enabled
    */


    "server_url" => "http://deployment.cpabuild.com/api.php",
    /*
     * The retrieve URL for requests. Don't edit this.
     */
);