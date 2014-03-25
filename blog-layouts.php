<?php
/**
	Plugin Name: Blog Layouts
	Plugin URI: http://weblumia.com/wp-blog-layouts/
	Description: To make your blog layout more attractive and colorful.
	Version: 1.4.2
	Author: Jinesh.P.V
	Author URI: http://weblumia.com/wp-blog-layouts/
	License: GPLv2 or later
 */
/**
	Copyright 2013 Jinesh.P.V (email: jinuvijay5@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */


add_action( 'admin_menu', 'wp_blog_layouts_add_menu' );
add_action( 'admin_init', 'wp_blog_layouts_reg_function' );
add_action( 'admin_init', 'wp_blog_layouts_admin_stylesheet' );
add_action( 'admin_init', 'wp_blog_layouts_scripts' );
add_action( 'wp_head', 'wp_blog_layouts_stylesheet', 20 );
add_shortcode( 'wp_blog_layouts', 'wp_blog_layouts_views' );

register_activation_hook( __FILE__, 'wp_blog_layouts_activate' );

function wp_blog_layouts_add_menu() {
	add_menu_page( 'Blog layouts', 'Blog layouts', 'administrator', 'layouts_settings', 'wp_blog_layouts_menu_function' );
	add_submenu_page( 'layouts_settings', 'Blog layouts Settings', 'Settings', 'manage_options', 'layouts_settings', 'wp_blog_layouts_add_menu' ); 
}

function wp_blog_layouts_reg_function() {
	
	if( 'posts' == get_option( 'show_on_front' ) && '0' == get_option( 'page_on_front' ) ){
		update_option( "show_on_front", 'page' );
		update_option( "page_on_front", 2 );
		
		$layouts					=	array();
		$layouts['ID']				=	2;
		$layouts['post_content']	=	'[wp_blog_layouts]';
		wp_update_post( $layouts );
	}
	
	$settings						=	get_option( "wp_blog_layouts_settings" );
	if ( empty( $settings ) ) {
		$settings = array(
							'template_category'	 		=>      '',
							'template_name'	 			=>      '',
							'template_bgcolor'	 		=>      '',
							'template_ftcolor'	 		=>      ''
						);
						
		add_option( "wp_blog_layouts_settings", $settings, '', 'yes' );
	}
}

if( $_REQUEST['action'] === 'save' && $_REQUEST['updated'] === 'true' ){
	
	update_option( "page_on_front", $_POST['page_on_front'] );
	update_option( "posts_per_page", $_POST['posts_per_page'] );
	update_option( "rss_use_excerpt", $_POST['rss_use_excerpt'] );
	
	$o_layouts						=	array();
	$o_layouts['ID']				=	2;
	$o_layouts['post_content']		=	'';
	wp_update_post( $o_layouts );
	
	$layouts						=	array();
	$layouts['ID']					=	$_POST['page_on_front'];
	$layouts['post_content']		=	'[wp_blog_layouts]';
	wp_update_post( $layouts );
	
	$settings						=	$_POST;
	$settings						=	is_array( $settings ) ? $settings : unserialize( $settings );
	$updated						=	update_option( "wp_blog_layouts_settings", $settings );
}

function wp_blog_layouts_activate() {
	
}

function wp_blog_layouts_admin_stylesheet() {
	
	$adminstylesheetURL 			= 	plugins_url( 'css/admin.css', __FILE__ );
	$adminstylesheet 				= 	dirname( __FILE__ )  . '/css/admin.css';
	
	$colorpickerstylesheetURL 		= 	plugins_url( 'css/colorpicker.css', __FILE__ );
	$colorpickerstylesheet 			= 	dirname( __FILE__ )  . '/css/colorpicker.css';
	
	if ( file_exists( $adminstylesheet ) ) {
		
		wp_register_style( 'wp-blog-layouts-admin-stylesheets', $adminstylesheetURL );
		wp_enqueue_style( 'wp-blog-layouts-admin-stylesheets' );
	}
	
	if ( file_exists( $colorpickerstylesheet ) ) {
		
		wp_register_style( 'wp-blog-layouts-colorpicker-stylesheets', $colorpickerstylesheetURL );
		wp_enqueue_style( 'wp-blog-layouts-colorpicker-stylesheets' );
	}
}

function wp_blog_layouts_scripts() {
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'cpicker', plugins_url( '/js/cpicker.js', __FILE__ ), '1.2' );
	wp_enqueue_script( 'eye', plugins_url( '/js/eye.js', __FILE__ ), '2.0' );
	wp_enqueue_script( 'bound', plugins_url( '/js/bound.js', __FILE__ ), '1.8.5' );
	wp_enqueue_script( 'layout', plugins_url( '/js/layout.js', __FILE__ ), '1.0.2' );
}

function wp_blog_layouts_stylesheet() {
	
	if( !is_admin() ){
		$stylesheetURL 			= 	plugins_url( 'css/layouts_css.php', __FILE__ );
		$stylesheet 			= 	dirname( __FILE__ )  . '/css/layouts_css.php';
		
		if ( file_exists( $stylesheet ) ) {
			
			wp_register_style( 'wp-blog-layouts-stylesheets', $stylesheetURL );
			wp_enqueue_style( 'wp-blog-layouts-stylesheets' );
		}
	}
}

function continue_reading_link() {
	return ' <a href="' . esc_url( get_permalink() ) . '">' . __( 'Read more', 'twentyeleven' ) . '</a>';
}

function auto_excerpt_more( $more ) {
	return ' &hellip;' . continue_reading_link();
}
add_filter( 'excerpt_more', 'auto_excerpt_more' );


function wp_blog_layouts_views(){
	
	$settings							=	get_option( "wp_blog_layouts_settings" );
	
    if( !isset( $settings['template_name'] ) || empty( $settings['template_name'] ) ) {
        return '[wp_blog_layouts] '.__('Invalid shortcode', 'wp_blog_layouts').'';

    }
	
    $theme								=	$settings['template_name'];
    $cat								=	$settings['template_category'];
	
	foreach( $cat as $catObj ):
		$category						.=	$catObj . ',';
	endforeach;
	$cat								=	rtrim( $category, ',' );
	$posts_per_page						=	get_option( 'posts_per_page' );
	$paged								=	lumiapaged();
	
	
	$posts								=	query_posts( array( 'cat' =>  $cat , 'posts_per_page' => $posts_per_page, 'paged' => $paged ) );
	
	while ( have_posts() ) : the_post();
		if( $theme == 'classical' ){
			wp_classical_layout();
		} elseif( $theme == 'lightbreeze' ){
			$class		=	' lightbreeze';
			wp_lightbreeze_layout();
		} elseif( $theme == 'spektrum' ){
			$class		=	' spektrum';
			wp_spektrum_layout();
		} elseif( $theme == 'evolution' ){
			$class		=	' evolution';
			wp_evolution_layout();
		}
	endwhile;
	
	echo '<div class="wl_pagination_box' . $class . '">';
		lumia_pagination();
	echo '</div>';
	
	wp_reset_query();
}

/****************************** display function for classical layout *************************************/

function wp_classical_layout(){
	?>
    
	<div class="blog_template classical">
		<div class="blog_header">
			<?php the_post_thumbnail( 'full' );?>
			<h1><a href="<?php the_permalink();?>"><?php the_title();?></a></h1>
			<div class="metadatabox">
					<div class="icon-date"></div>Posted by <span><?php the_author();?></span> on <?php the_time( __( 'F d, Y' ) );?>
					<?php
					$categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
					if ( $categories_list ):
						printf( __( ' %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $categories_list );
						$show_sep = true;
					endif; ?>
				<div class="metacomments">
					<div class="icon-comment"></div><?php comments_popup_link( '0', '1', '%' ); ?>
				</div>
			</div>
			<?php
            $tags_list = get_the_tag_list( '', __( ', ', 'twentyeleven' ) );
            if ( $tags_list ):?>
                <div class="tags">
                    <div class="icon-tags"></div>
                    <?php
                    printf( __( '%2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
                    $show_sep = true;
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="post_content">
            <?php if( get_option( 'rss_use_excerpt' ) == 0 ):?>
                <?php the_content(); ?>
            <?php else:?>
                <?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) );?>
            <?php endif;?>
        </div>
	</div>
	<?php
}

/****************************** display function for lightbreeze layout *************************************/

function wp_lightbreeze_layout(){
	?>
    
	<div class="blog_template">
		<div class="blog_header">
			<?php the_post_thumbnail( 'full' );?>
			<h1><a href="<?php the_permalink();?>"><?php the_title();?></a></h1>
			<div class="metadatabox">
				<div class="metadate">
					<div class="icon-author"></div>Posted by <span><?php the_author();?></span><br />
					<span class="mdate"><?php the_time( __( 'F d, Y' ) );?></span>
				</div>
				<div class="metacats">
					<div class="icon-cats"></div>
					<?php
					$categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
					if ( $categories_list ):
						printf( __( '%2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $categories_list );
						$show_sep = true;
					endif; ?>
				</div>
				<div class="metacomments">
					<div class="icon-comment"></div>
					<?php comments_popup_link( 'No Comments', '1 Comment', '% Comments' ); ?>
				</div>
			</div
		></div>
	<div class="post_content">
		<?php if( get_option( 'rss_use_excerpt' ) == 0 ):?>
			<?php the_content(); ?>
		<?php else:?>
			<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) );?>
		<?php endif;?>
	</div>
	<?php
	$tags_list = get_the_tag_list( '', __( ', ', 'twentyeleven' ) );
	if ( $tags_list ):?>
		<div class="tags">
			<div class="icon-tags"></div>
			<?php
			printf( __( '%2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
			$show_sep = true;
			?>
		</div>
	<?php endif; ?>
	</div>
	<?php
}

/****************************** display function for spektrum layout *************************************/

function wp_spektrum_layout(){
	?>
    
	<div class="blog_template spektrum">
		<?php the_post_thumbnail( 'full' );?>
            <div class="blog_header">
            	<span class="date"><span class="number-date"><?php the_time( __( 'd' ) );?></span><?php the_time( __( 'F' ) );?></span>
				<h1><a href="<?php the_permalink();?>"><?php the_title();?></a></h1>
        </div>
        <div class="post_content">
            <?php if( get_option( 'rss_use_excerpt' ) == 0 ):?>
                <?php the_content(); ?>
            <?php else:?>
                <?php the_excerpt();?>
            <?php endif;?>
        </div>
        <div class="post-bottom">
        	<span class="categories">
				<?php
                $categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
                if ( $categories_list ):
                    printf( __( 'Catrgories : %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $categories_list );
                    $show_sep = true;
                endif; ?>
            </span>
            <span class="details">
            	<a href="<?php the_permalink();?>">Read more</a>
            </span>
        </div>
	</div>
	<?php
}


/****************************** display function for evolution layout *************************************/

function wp_evolution_layout(){
	?>
    
	<div class="blog_template evolution">
        <div class="blog_header">
            <span class="date"><span class="number-date"><?php the_time( __( 'd' ) );?></span><?php the_time( __( 'F' ) );?></span>
            <div class="title">
            	<h1><a href="<?php the_permalink();?>"><?php the_title();?></a></h1>
                <div class="metadate">
                	Posted by <span class="author"><?php the_author();?></span> on <span class="time"><?php the_time( __( 'F d, Y' ) );?></span>
                    under
                    <?php
                    $categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
                    if ( $categories_list ):
                        printf( __( '%2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $categories_list );
                        $show_sep = true;
                    endif;
                    ?>
                </div>
            </div>
            <span class="comment"><span class="icon_cnt"><?php comments_popup_link( '0', '1', '%' ); ?></span></span>
        </div>
		<?php the_post_thumbnail( 'full' );?>
        <div class="post_content">
            <?php if( get_option( 'rss_use_excerpt' ) == 0 ):?>
                <?php the_content(); ?>
            <?php else:?>
                <?php the_excerpt();?>
            <?php endif;?>
        </div>
        <div class="post-bottom">
            <a href="<?php the_permalink();?>">Read more &raquo;</a>
        </div>
	</div>
	<?php
}


function wp_blog_layouts_menu_function() {
?>

<div class="wrap">
    <h2><?php _e( 'Blog Layouts Settings', 'wp-blog_layouts' ) ?></h2>
    <?php if ( 'true' == esc_attr( $_GET['updated'] ) ) echo '<div class="updated" ><p>Layout Settings updated.</p></div>';?>
    <?php $settings			=	get_option( "wp_blog_layouts_settings" );?>
    <form method="post" action="?page=layouts_settings&action=save&updated=true">
    	<!--script type="text/javascript">jQuery( window ).load( function() { jQuery( "#template_category" ).attr( "multiple", "multiple" );});</script-->
        <div class="wl-pages" >
            <div class="wl-page wl-settings active">
                <div class="wl-box wl-settings">
                    <h3 class="header"><?php _e( 'General Settings', 'wp-blog_layouts' ) ?></h3>
                        <table>
                            <tbody>
                                <tr>
                                    <td><?php _e( 'Blog page displays', 'wp-blog_layouts' ) ?></td>
                                    <td>
                                        <?php printf( __( '%s' ), wp_dropdown_pages( array( 'name' => 'page_on_front', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => get_option( 'page_on_front' ) ) ) ); ?>
                                     </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Blog pages show at most', 'wp-blog_layouts' ) ?></td>
                                    <td>
                                        <input name="posts_per_page" type="number" step="1" min="1" id="posts_per_page" value="<?php echo get_option( 'posts_per_page' ); ?>" class="small-text" /> <?php _e( 'posts' ); ?>
                                     </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'For each article in a feed, show ', 'wp-blog_layouts' ) ?></td>
                                    <td>
                                        <input name="rss_use_excerpt" type="radio" value="0" <?php checked( 0, get_option( 'rss_use_excerpt' ) ); ?>	/> <?php _e( 'Full text' ); ?>
                                        <input name="rss_use_excerpt" type="radio" value="1" <?php checked( 1, get_option( 'rss_use_excerpt' ) ); ?> /> <?php _e( 'Summary' ); ?>
                                     </td>
                                </tr>
                            </tbody>
                        </table>
                        <h3 class="header"><?php _e( 'Global Settings', 'wp-blog_layouts' ) ?></h3>
                        <table>
                            <tbody>
                                <tr>
                                    <td><?php _e( 'Blog page categories', 'wp-blog_layouts' ) ?></td>
                                    <td>
                                    	<?php $categories 	=	get_categories( array( 'child_of' => '', 'hide_empty' => 1 ) );?>
                                        <select name="template_category[]" id="template_category" multiple="multiple">
                                        	<option><?php echo '&mdash; Select &mdash;';?></option>
                                        	<?php foreach( $categories as $categoryObj ):?>
                                            	<option value="<?php echo $categoryObj->term_id;?>" <?php if( @in_array( $categoryObj->term_id, $settings['template_category'] ) ) { echo 'selected="selected"'; } ?>><?php echo $categoryObj->name;?></option>
                                            <?php endforeach;?>
                                        </select>
                                     </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Blog Templates', 'wp-blog_layouts' ) ?></td>
                                    <td>
                                        <select name="template_name" id="template_name">
                                        	<option value="">---select---</option>
                                            <option value="classical" <?php if( $settings["template_name"] == 'classical' ){?> selected="selected"<?php }?>>Classical</option>
                                            <option value="lightbreeze" <?php if( $settings["template_name"] == 'lightbreeze' ){?> selected="selected"<?php }?>>Light Breeze</option>
                                            <option value="spektrum" <?php if( $settings["template_name"] == 'spektrum' ){?> selected="selected"<?php }?>>Spektrum</option>
                                            <option value="evolution" <?php if( $settings["template_name"] == 'evolution' ){?> selected="selected"<?php }?>>Evolution</option>
                                        </select>
                                     </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Choose a background color for blog layout', 'wp-blog_layouts' ) ?></td>
                                    <td>
                                        <div id="bgcolorSelector"><div style="background-color:<?php echo $settings["template_bgcolor"];?>"></div></div>
                                        <input type="hidden" name="template_bgcolor" id="template_bgcolor" value="<?php echo $settings["template_bgcolor"];?>"/>
                                     </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Choose a font color for blog layout', 'wp-blog_layouts' ) ?></td>
                                    <td>
                                        <div id="ftcolorSelector"><div style="background-color:<?php echo $settings["template_ftcolor"];?>"></div></div>
                                        <input type="hidden" name="template_ftcolor" id="template_ftcolor" value="<?php echo $settings["template_ftcolor"];?>"/>
                                     </td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
        <div class="wl-box wl-publish">
            <h3 class="header"><?php _e('Publish', 'wp-blog_layouts') ?></h3>
            <div class="inner">
                <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wp-blog_layouts' ); ?>" />
                <p class="wl-saving-warning"></p>
                <div class="clear"></div>
            </div>
        </div>
    </form>
</div>

<?php }
function lumia_pagination( $args = array() ) {
	
	if ( !is_array( $args ) ) {
		$argv = func_get_args();
		$args = array();
		foreach ( array( 'before', 'after', 'options' ) as $i => $key )
			$args[ $key ] = $argv[ $i ];
	}
	$args = wp_parse_args( $args, array(
		'before' => '',
		'after' => '',
		'options' => array(),
		'query' => $GLOBALS['wp_query'],
		'type' => 'posts',
		'echo' => true
	) );

	extract( $args, EXTR_SKIP );
	$instance = new LBNavi_Call( $args );	

	list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();

	if ( 1 == $total_pages && !$options['always_show'] )
		return;

	$pages_to_show = 100;
	$larger_page_to_show = 3;
	$larger_page_multiple = 10;
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor( $pages_to_show_minus_1/2 );
	$half_page_end = ceil( $pages_to_show_minus_1/2 );
	$start_page = $paged - $half_page_start;

	if ( $start_page <= 0 )
		$start_page = 1;

	$end_page = $paged + $half_page_end;

	if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 )
		$end_page = $start_page + $pages_to_show_minus_1;

	if ( $end_page > $total_pages ) {
		$start_page = $total_pages - $pages_to_show_minus_1;
		$end_page = $total_pages;
	}

	if ( $start_page < 1 )
		$start_page = 1;

	$out = '';
	$options['style']				=	1;
	$options['pages_text']			=	'Page %CURRENT_PAGE% of %TOTAL_PAGES%';
	$options['current_text']		=	'%PAGE_NUMBER%';
	$options['page_text']			=	'%PAGE_NUMBER%';
	$options['first_text']			=	'&laquo; First';
	$options['last_text']			=	'Last &raquo;';
	$options['prev_text']			=	'';
	$options['next_text']			=	'';
	$options['dotright_text']		=	'';
	
	switch ( intval( $options['style'] ) ) {
		
		
		// Normal
		case 1:
			// Text
			if ( !empty( $options['pages_text'] ) ) {
				$pages_text = str_replace(
					array( "%CURRENT_PAGE%", "%TOTAL_PAGES%" ),
					array( number_format_i18n( $paged ), number_format_i18n( $total_pages ) ),
				$options['pages_text'] );
				$out .= "<span class='pages'>$pages_text</span>";
			}

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				// First
				$first_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $total_pages ), $options['first_text'] );
				$out .= $instance->get_single( 1, 'first', $first_text, '%TOTAL_PAGES%' );
			}

			// Previous
			if ( $paged > 1 && !empty( $options['prev_text'] ) )
				$out .= $instance->get_single( $paged - 1, 'previouspostslink', $options['prev_text'] );

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				if ( !empty( $options['dotleft_text'] ) )
					$out .= "<span class='extend'>{$options['dotleft_text']}</span>";
			}

			// Smaller pages
			$larger_pages_array = array();
			if ( $larger_page_multiple )
				for ( $i = $larger_page_multiple; $i <= $total_pages; $i+= $larger_page_multiple )
					$larger_pages_array[] = $i;

			$larger_page_start = 0;
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page < ($start_page - $half_page_start) && $larger_page_start < $larger_page_to_show ) {
					$out .= $instance->get_single( $larger_page, 'smaller page', $options['page_text'] );
					$larger_page_start++;
				}
			}

			if ( $larger_page_start )
				$out .= "<span class='extend'>{$options['dotleft_text']}</span>";

			// Page numbers
			$timeline = 'smaller';
			foreach ( range( $start_page, $end_page ) as $i ) {
				if ( $i == $paged && !empty( $options['current_text'] ) ) {
					$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['current_text'] );
					$out .= "<span class='current'>$current_page_text</span>";
					$timeline = 'larger';
				} else {
					$out .= $instance->get_single( $i, "page $timeline", $options['page_text'] );
				}
			}

			// Large pages
			$larger_page_end = 0;
			$larger_page_out = '';
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page > ($end_page + $half_page_end) && $larger_page_end < $larger_page_to_show ) {
					$larger_page_out .= $instance->get_single( $larger_page, 'larger page', $options['page_text'] );
					$larger_page_end++;
				}
			}

			if ( $larger_page_out ) {
				$out .= "<span class='extend'>{$options['dotright_text']}</span>";
			}
			$out .= $larger_page_out;

			if ( $end_page < $total_pages ) {
				if ( !empty( $options['dotright_text'] ) )
					$out .= "<span class='extend'>{$options['dotright_text']}</span>";
			}

			// Next
			if ( $paged < $total_pages && !empty( $options['next_text'] ) )
				$out .= $instance->get_single( $paged + 1, 'nextpostslink', $options['next_text'] );

			if ( $end_page < $total_pages ) {
				// Last
				$out .= $instance->get_single( $total_pages, 'last', $options['last_text'], '%TOTAL_PAGES%' );
			}
			break;

		// Dropdown
		case 2:
			$out .= '<form action="" method="get">'."\n";
			$out .= '<select size="1" onchange="document.location.href = this.options[this.selectedIndex].value;">'."\n";

			foreach ( range( 1, $total_pages ) as $i ) {
				$page_num = $i;
				if ( $page_num == 1 )
					$page_num = 0;

				if ( $i == $paged ) {
					$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['current_text'] );
					$out .= '<option value="'.esc_url( $instance->get_url( $page_num ) ).'" selected="selected" class="current">'.$current_page_text."</option>\n";
				} else {
					$page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['page_text'] );
					$out .= '<option value="'.esc_url( $instance->get_url( $page_num ) ).'">'.$page_text."</option>\n";
				}
			}

			$out .= "</select>\n";
			$out .= "</form>\n";
			break;
	}
	$out = $before . "<div class='wl_pagination'>\n$out\n</div>" . $after;

	$out = apply_filters( 'lumia_pagination', $out );

	if ( !$echo )
		return $out;

	echo $out;
}
class LBNavi_Call {

	protected $args;

	function __construct( $args ) {
		$this->args = $args;
	}

	function __get( $key ) {
		return $this->args[ $key ];
	}

	function get_pagination_args() {
		global $numpages;

		$query = $this->query;

		switch( $this->type ) {
		case 'multipart':
			// Multipart page
			$posts_per_page = 1;
			$paged = max( 1, absint( get_query_var( 'page' ) ) );
			$total_pages = max( 1, $numpages );
			break;
		case 'users':
			// WP_User_Query
			$posts_per_page = $query->query_vars['number'];
			$paged = max( 1, floor( $query->query_vars['offset'] / $posts_per_page ) + 1 );
			$total_pages = max( 1, ceil( $query->total_users / $posts_per_page ) );
			break;
		default:
			// WP_Query
			$posts_per_page = intval( $query->get( 'posts_per_page' ) );
			$paged = max( 1, absint( $query->get( 'paged' ) ) );
			$total_pages = max( 1, absint( $query->max_num_pages ) );
			break;
		}

		return array( $posts_per_page, $paged, $total_pages );
	}

	function get_single( $page, $class, $raw_text, $format = '%PAGE_NUMBER%' ) {
		if ( empty( $raw_text ) )
			return '';

		$text = str_replace( $format, number_format_i18n( $page ), $raw_text );

		return "<a href='" . esc_url( $this->get_url( $page ) ) . "' class='$class'>$text</a>";
	}

	function get_url( $page ) {
		return ( 'multipart' == $this->type ) ? get_multipage_link( $page ) : get_pagenum_link( $page );
	}
}

function lumiapaged(){
	if( strstr( $_SERVER['REQUEST_URI'], 'paged' ) || strstr( $_SERVER['REQUEST_URI'], 'page' ) ){
		if( isset( $_REQUEST['paged'] ) ){
			$paged						=	$_REQUEST['paged'];
		} else {
			$uri						=	explode( '/', $_SERVER['REQUEST_URI'] );
			$uri						=	array_reverse( $uri );
			$paged						=	$uri[1];
		}
	}else{
		$paged							=	1;
	}
	
	return $paged;
}

?>