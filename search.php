<?php

get_header('search');
?>




<div id="content" class="site-content" role="main">
			
<article id="post-1987" class="post-1987 page type-page status-publish hentry">
		<div class="entry-content">
					<!-- 00000 -->
			<div id="dslc-content" class="dslc-content dslc-clearfix">
		<div id="" class="dslc-modules-section " style="border-style:solid; border-right-style: hidden; border-left-style: hidden; background-repeat:repeat; background-position:left top; background-attachment:scroll; background-size:auto; " data-stellar-background-ratio="0.5">

				

				<div class="dslc-modules-section-wrapper dslc-clearfix"> <div class="dslc-modules-area dslc-col dslc-3-col dslc-first-col" data-size="3"> 
		<div id="dslc-module-795" class="dslc-module-front dslc-module-DSLC_Widgets dslc-in-viewport-check dslc-in-viewport-anim-none dslc-in-viewport" data-module-id="795" data-dslc-module-id="DSLC_Widgets" data-dslc-module-size="12" data-dslc-anim="none" data-dslc-anim-delay="0" data-dslc-anim-easing="ease" style="-webkit-animation: forwards 0.65s ease none;">

			
			
					<div class="dslc-widgets dslc-clearfix dslc-widgets-12-col">
				<div class="dslc-widgets-wrap dslc-clearfix">
					<div id="nav_menu-20" class="dslc-widget dslc-col widget_nav_menu">
                    <?php dynamic_sidebar( 'sidebar-search' ); ?>
                    </div>				
                    </div>
			</div>
			
			
			
		</div><!-- .dslc-module -->
		 </div>
         <div class="dslc-modules-area dslc-col dslc-9-col dslc-last-col" data-size="9"> 
		<div id="dslc-module-793" class="dslc-module-front dslc-module-DSLC_Text_Simple dslc-in-viewport-check dslc-in-viewport-anim-none dslc-in-viewport" data-module-id="793" data-dslc-module-id="DSLC_Text_Simple" data-dslc-module-size="12" data-dslc-anim="none" data-dslc-anim-delay="0" data-dslc-anim-easing="ease" style="-webkit-animation: forwards 0.65s ease none;">
        
        
<div class="dslc-module-heading">
						
						<!-- Heading -->

						
							<h2 class="dslca-editable-content" data-id="main_heading_title" data-type="simple" style="font-size: 32px;color: rgb(0, 48, 97); margin-bottom:10px;">Search Result<br></h2>

							<!-- View all -->
					</div>        

	<?php while ( have_posts() ) : the_post(); ?>
    	<div class="dslc-cpt-post-title"><h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2></div>
		<?php the_excerpt(); ?>
        
<div class="dslc-cpt-post-read-more" style="margin-bottom:10px;"><a href="<?php the_permalink() ?>">View Post</a></div>        
        
        
	<?php endwhile; // end of the loop. ?>
	
			

</div>
</div>
			
			
		</div><!-- .dslc-module -->
		 </div> </div></div> </div>						</div><!-- .entry-content -->
</article><!-- #post-## -->	</div>



<?php get_footer('search'); ?>