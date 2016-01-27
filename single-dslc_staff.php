<?php
/**
 * The template for displaying blog posts and custom post types pages.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This is the template that displays standard WordPress posts and
 * all custom post type pages. By default our theme includes
 * the next custom types:
 * 	– Projects     > dslc_projects
 * 	– Galleries    > dslc_galleries
 * 	– Staff        > dslc_staff
 * 	– Downloads    > dslc_downloads
 * 	– Testimonials > dslc_testimonials
 * 	– Partners     > dslc_partners
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

// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
if ( LBMN_THEME_DEBUG ) echo '<!-- FILE: '.__FILE__.' -->';

get_header();
?>
<section>
<div id="content" class="bootstrap site-content" role="main" style="clear:both;">
	<?php
		while ( have_posts() ) {
			the_post();
			//get_template_part( 'content' );
		

	?>

	<div class="col-sm-3">
		<?php 				
		wp_nav_menu( array(
					'menu'            => 'Sidebar','container'       => ' ',
				) );
		 ?>
    </div>
	<div class="col-sm-9">
					<!-- 00000 -->
			<div id="dslc-content" class="dslc-content dslc-clearfix">
		 
		<div id="" class="dslc-modules-section " style="border-style:solid; border-right-style: hidden; border-left-style: hidden; background-repeat:repeat; background-position:left top; background-attachment:scroll; background-size:auto; padding-left:20px; " data-stellar-background-ratio="0.5">

				

				<div class="dslc-modules-section-wrapper dslc-clearfix"> <div class="dslc-modules-area dslc-col dslc-3-col dslc-first-col" data-size="2"> 
		<div id="dslc-module-78" class="dslc-module-front dslc-module-DSLC_Text_Simple dslc-in-viewport-check dslc-in-viewport-anim-none dslc-in-viewport" data-module-id="78" data-dslc-module-id="DSLC_Text_Simple" data-dslc-module-size="12" data-dslc-anim="none" data-dslc-anim-delay="0" data-dslc-anim-easing="ease" style="-webkit-animation: forwards 0.65s ease none;">

			
			
		<div class="dslc-text-module-content"><p><img class="alignleft wp-image-1506 size-full" src="<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' ); $url = $thumb['0']; echo $url; ?>" ></p></div>
			
			
		</div><!-- .dslc-module -->
		 </div> <div class="dslc-modules-area dslc-col dslc-9-col dslc-last-col" data-size="10"> 
		<div id="dslc-module-79" class="dslc-module-front dslc-module-DSLC_Text_Simple dslc-in-viewport-check dslc-in-viewport-anim-none dslc-in-viewport" data-module-id="79" data-dslc-module-id="DSLC_Text_Simple" data-dslc-module-size="12" data-dslc-anim="none" data-dslc-anim-delay="0" data-dslc-anim-easing="ease" style="-webkit-animation: forwards 0.65s ease none;">

			
			
		<div class="dslc-text-module-content">
            <h2 style="margin-top:0px;"><?php the_title(); ?></h2>
            <?php the_content(); ?>
        </div>
			
			
		</div><!-- .dslc-module -->
		 </div> </div></div> </div>	
    </div>


    
    
    
    <?php } ?>
    
</div><!-- #content -->
</section>

<style>
.calltoaction-area { clear:both;}
</style>

<?php

//All the comment forms and threads outputted by LiveComposer
comments_template('/comments.php');

get_footer();
?>
