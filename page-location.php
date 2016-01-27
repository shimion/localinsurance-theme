<?php
/*

 * Template Name: Page location

 */
/**
 * The template for displaying all pages.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This is the template that displays all pages by default.
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    http://themeforest.net/licenses
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();
// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
if ( LBMN_THEME_DEBUG ) echo '<!-- FILE: '.__FILE__.' -->';
?>

<div id="content" class="site-content" role="main">
	<article id="post-1300" class="post-1300 page type-page status-publish hentry">
		<div class="entry-content"><!-- 00000 -->
        
        <h1><a href="#5">Shimion</a></h1>
       
            <div id="searchresultsmapcontainer">
               <!-- <div class="searchresultsmap search-map" id="map" style="width:100%; height:500px;"></div>-->

            </div>
		</div><!-- .entry-content -->
    </article>
</div><!-- #content -->



<!--
 <script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>
<script type="text/javascript">
                        var address_index = 0, map, infowindow, geocoder, bounds, mapinfo, intTimer;
                    
                        window.onload = function() {
                            // Creating an object literal containing the properties you want to pass to the map
                            var options = {
                                zoom:36,
                                center:		new google.maps.LatLng(1.2896700, 103.8500700),
                                mapTypeId:	google.maps.MapTypeId.ROADMAP
                            };
				
			
                        
                            // Creating the map
                            map = new google.maps.Map(document.getElementById('map'), options);
                            infowindow = new google.maps.InfoWindow();
							
                            geocoder = new google.maps.Geocoder();
                            bounds = new google.maps.LatLngBounds();		
                    
                            //******* ARRAY BROUGHT OVER FROM SEARCHRESULTS.PHP **********
                            mapinfo = [["5","5 Sophia Hills, Singapore","Sophia Hills","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/1597_Sopia-Hills_Perspective1-1024x921-241x136.jpg","1000000","http:\/\/property.dummy.shimion.com\/sophia-hills\/",null],["95 Marine Parade Road Singapore","95 Marine Parade Road Singapore","SKIES @ VANTAGE BAY","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/Skies-Vantage-Bay-241x136.jpg","12000","http:\/\/property.dummy.shimion.com\/skies-vantage-bay\/",null],["380 Jalan Besar","380 Jalan Besar","RIVERBANK","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/riverbank-clubhouse-241x136.jpg","40000","http:\/\/property.dummy.shimion.com\/riverbank\/",null],[" 96 Paya Lebar Crescent Singapore"," 96 Paya Lebar Crescent Singapore","THE RISE @ OXLEY","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/rise-oxley-facade01-241x136.jpg","26000","http:\/\/property.dummy.shimion.com\/the-rise-oxley\/",null],[" 101 Irrawaddy Road "," 101 Irrawaddy Road ","HIGHLINE RESIDENCES","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/highline-residences-facade-241x136.jpg","27000","http:\/\/property.dummy.shimion.com\/highline-residences\/",null],[" Geylang East Ave 1 Singapore"," Geylang East Ave 1 Singapore","COMMONWEALTH TOWERS","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/Commonwealth-tower-condo-241x136.jpg","31000","http:\/\/property.dummy.shimion.com\/commonwealth-towers\/",null],["2 Leedon Heights Singapore","2 Leedon Heights Singapore","TRIO","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/facade-night-1024x746-241x136.jpg","19000","http:\/\/property.dummy.shimion.com\/trio\/",null],["199 Sixth Avenue Singapore","199 Sixth Avenue Singapore","THE BRIDGE @ CAMBODIA","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/Facade-1024x590-241x136.jpg","37000","http:\/\/property.dummy.shimion.com\/the-bridge-cambodia\/",null],[" 51 Kampong Bugis Singapore"," 51 Kampong Bugis Singapore","VICTORIA ONE \u2013 MELBO","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/victoriaone-lounge1-1024x477-241x136.jpg","32000","http:\/\/property.dummy.shimion.com\/victoria-one-melbourne-2\/",null],["456 Balestier Road Singapore","456 Balestier Road Singapore","NORTH PARK RESIDENCES","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/feature-picture11-241x136.jpg","25000","http:\/\/property.dummy.shimion.com\/north-park-residences-2\/",null],[" Pasir Ris Grove "," Pasir Ris Grove ","VICTORIA ONE \u2013 MELBO","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/10\/victoriaone-bedroom-241x136.jpg","30000","http:\/\/property.dummy.shimion.com\/victoria-one-melbourne\/",null],["Yishun Central 1 Singapore","Yishun Central 1 Singapore","CHANCERY HILL VILLAS","http:\/\/property.dummy.shimion.com\/wp-content\/uploads\/2014\/09\/poperty-featured-1024x381-241x136.jpg","20000","http:\/\/property.dummy.shimion.com\/chancery-hill-villas\/",null]];
                            
                            intTimer = setInterval("call_geocode();", 300);
                        }
                        
                        
                        
                        function call_geocode() {
                        if( address_index >= mapinfo.length ) {
                                clearInterval(intTimer);
                                return;
                            }
                        
                              geocoder.geocode( { address: mapinfo[address_index][0] }, (function(index) {
                                        return function(results, status) {
                                            if (status == google.maps.GeocoderStatus.OK) {
                                                // Scale our bounds
                                                bounds.extend(results[0].geometry.location);
                                                var $address 	= mapinfo[index][0];
                                                var $street 	= mapinfo[index][1];
                                                var $title 	= mapinfo[index][2];
                                                var $img_src 	= mapinfo[index][3];
                                                var $price 	= mapinfo[index][4];
                                                var $link 	= mapinfo[index][5];
                                                var $pricecustom 	= mapinfo[index][6];
                                                
                                                                        
                                               var theicon = new google.maps.MarkerImage('http://property.dummy.shimion.com/wp-content/uploads/2014/10/maphouse.png');
                                                                                
                                               
                                                var marker = new google.maps.Marker({
                                                position:                results[0].geometry.location,
                                                icon: theicon,
                                                map:                          map,
                                                scrollwheel:           false,
                                                streetViewControl:true,
                                                title:                     $title
                                           });
                                                
                                        
                                                
                                                
                                                
                                                google.maps.event.addListener( marker, 'click', function() {
                                                    // Setting the content of the InfoWindow
                                                    var content = '<div id="info">' + '<img src="' + $img_src  + '"/>' + '<div id="inner_info"><h3 id="' + $address + '">' + $title + '</h3>' + '<p>' + $street + '</p>' + '<p><a href="' + $link + '">Read More</a></p>' + '</div></div>';
													
		var myOptions = {
                     content: boxText,
                    disableAutoPan: false,
                    maxWidth: 0,
                    pixelOffset: new google.maps.Size(-60, -180),
                    zIndex: null,
                    boxStyle: {
                        background: "url('http://property.dummy.shimion.com/wp-content/uploads/2014/10/map-info-window.png') no-repeat",
                        opacity: 0.75,
                        width: "280px"
                    },
                    closeBoxMargin: "0",
                    closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
                    infoBoxClearance: new google.maps.Size(1, 1),
                    isHidden: false,
                    pane: "floatPane",
                    enableEventPropagation: false
                };
													
													var ib = new InfoBox(myOptions)
													                                                  
                                                    ib.open(map, marker);
													                                                });
                                                
                                                map.fitBounds(bounds);
                                                if (mapinfo.length == 1) {
                                                    map.setZoom(12);
                                                }
                                            } else {
                                                // error!! alert (status);
                                            }
                                    }})(address_index));
                            
                        address_index++;		
                        }
                    
                    
</script>	

-->
<?php wp_reset_query(); ?>
<?php get_footer(); ?>