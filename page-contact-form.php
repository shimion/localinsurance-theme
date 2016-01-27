<?php
/*

 * Template Name: Page Contact Form

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


get_header();
// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
?>




<div id="content" class="site-content" role="main">
			
<article id="post-1987" class="post-1987 page type-page status-publish hentry">
		<div class="entry-content">
					<!-- 00000 -->
			<div>			<!-- <div id="dslc-content" class="dslc-content dslc-clearfix"> -->
		<div id="" class="dslc-modules-section " style="border-style:solid; border-right-style: hidden; border-left-style: hidden; background-repeat:repeat; background-position:left top; background-attachment:scroll; background-size:auto; " data-stellar-background-ratio="0.5">

				

				<div class="dslc-modules-section-wrapper dslc-clearfix"> <div class="dslc-modules-area dslc-col dslc-3-col dslc-first-col" data-size="3"> 
		<div id="dslc-module-795" class="dslc-module-front dslc-module-DSLC_Widgets dslc-in-viewport-check dslc-in-viewport-anim-none dslc-in-viewport" data-module-id="795" data-dslc-module-id="DSLC_Widgets" data-dslc-module-size="12" data-dslc-anim="none" data-dslc-anim-delay="0" data-dslc-anim-easing="ease" style="-webkit-animation: forwards 0.65s ease none;">

			
			
					<div class="dslc-widgets dslc-clearfix dslc-widgets-12-col">
				<div class="dslc-widgets-wrap dslc-clearfix">
					<div id="nav_menu-20" class="dslc-widget dslc-col widget_nav_menu">
                    <?php dynamic_sidebar( 'sidebar-contact' ); ?>
                    </div>				
                    </div>
			</div>
			
			
			
		</div><!-- .dslc-module -->
		 </div>
         <div class="dslc-modules-area dslc-col dslc-9-col dslc-last-col" data-size="9"> 
		<div id="dslc-module-793" class="dslc-module-front dslc-module-DSLC_Text_Simple dslc-in-viewport-check dslc-in-viewport-anim-none dslc-in-viewport" data-module-id="793" data-dslc-module-id="DSLC_Text_Simple" data-dslc-module-size="12" data-dslc-anim="none" data-dslc-anim-delay="0" data-dslc-anim-easing="ease" style="-webkit-animation: forwards 0.65s ease none;">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; // end of the loop. ?>
	
			

</div>
</div>
			
			
		</div><!-- .dslc-module -->
		 </div> </div></div> </div>						</div><!-- .entry-content -->
</article><!-- #post-## -->	</div>



<?php wp_reset_query(); ?>
<?php get_footer(); ?>