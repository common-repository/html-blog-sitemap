<?php

/*
Plugin Name: HTML Blog Sitemap
Plugin URI: http://phpwebhost.co.za/html-blog-sitemap-wordpress-plugin/
Description: <a href="http://phpwebhost.co.za/html-blog-sitemap-wordpress-plugin/" target="_blank">HTML Blog Sitemap</a> Adds a HTML sitemap of your blog posts by entering the shortcode [pwh_blog_sitemap] into a new page or post.
Version: 1.0.2
Author: phpwebhost.co.za
Author URI: http://phpwebhost.co.za/
Change Log:
	See readme.txt for complete change log

Contributors:
	John Mc Murray
	
Copyright 2014 phpwebhost.co.za (http://phpwebhost.co.za)

License: GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt)

Modified from original HTML Page Sitemap: 26 January 2014:
Based on the HTML PAGE sitemap from Plugins podcast <a href="http://www.pluginspodcast.com/plugins/html-page-sitemap/" target="_blank">Plugins: The WordPress Plugins Podcast</a>.

*/

add_action( 'admin_menu', 'pwh_html_blog_sitemap_menu');
add_action('admin_init', 'pwh_html_blog_sitemap_page_init');

define("RECURSION_LEVEL", 400);
define("ADD_OWN_CATEGORY", 1);
define("SKIP_OWN_CATEGORY", 0);












function pwh_html_blog_sitemap_page_init()
{	
	
	register_setting('pwh_html_blog_sitemap_option_group', 'pwh_html_blog_sitemap_array_key', 'pwh_check_ID');
		
	
        add_settings_section(
	    'pwh_html_blog_sitemap_setting_section_id2',
	    'Your gratuity setting',
	    'print_gratuity_mode_info',
	    'pwh_html_blog_sitemap_gratuity_mode_settings'
	);	
		
	add_settings_field(
	    'pwh_html_blog_sitemap_some_id2', 
	    'Enable Link:', 
	    'create_html_blog_sitemap_gratuity_checkbox', 
	    'pwh_html_blog_sitemap_gratuity_mode_settings',
	    'pwh_html_blog_sitemap_setting_section_id2'			
	);
 

}


    function print_gratuity_mode_info(){


	print 'Tick here to allow a link back to phpwebhost.co.za on your blog page.<p>Link will be displayed as: <b>HTML Blog Sitemap plugin by <a href="http://www.phpwebhost.co.za" target="_new">PHP Web Host</a></b>';

	$pwh_html_blog_sitemap_GratuityMode = get_option('pwh_html_blog_sitemap_gratuity_mode');


    }
	







    function create_html_blog_sitemap_gratuity_checkbox()
    {

	$pwh_html_blog_sitemap_CurrentSetting = get_option('pwh_html_blog_sitemap_gratuity_mode');

	?>

	<input type="checkbox" name="pwh_html_blog_sitemap_array_key[gratuity_mode]" <?php print $pwh_html_blog_sitemap_CurrentSetting=="on"? " checked ": "";?> >

        <?php
    }

    	function pwh_check_ID($input)
	{
	
	        $gratuity_mode = $input['gratuity_mode'];
	        update_option('pwh_html_blog_sitemap_gratuity_mode', $gratuity_mode);	
   	}




function pwh_html_blog_sitemap_create_admin_page()
{
?>
<div class="wrap">
<?php 
screen_icon(); 
?>


	    <h2>HTML Blog Sitemap Settings</h2>	

            <p>
            To use this plugin, create a blank page, or post, and add the code <b>[pwh_blog_sitemap]</b> to that page. That's it!
            <p>
		
	    <form method="post" action="options.php">
	        <?php
                    // This prints out all hidden setting fields
		    settings_fields('pwh_html_blog_sitemap_option_group');	
		    do_settings_sections('pwh_html_blog_sitemap_gratuity_mode_settings');

		submit_button(); 
                ?>
	    </form>


            <p>
            <b>Need help?</b> Go to <a href="http://www.phpwebhost.co.za/html-blog-sitemap-wordpress-plugin/" target="_new">HTML Blog Sitemap Page</a> and ask in the comments
            <p>

	</div>
<?php
}
	




function pwh_html_blog_sitemap_menu() {

	if(!is_admin())
	{
		print "Sorry, this functionality is only available to site admins...";
		return;
	}

	add_options_page( 'HTML Blog Sitemap Options', 'HTML Blog Sitemap', 'manage_options', 'pwh_html_blog_sitemap', 'pwh_html_blog_sitemap_create_admin_page' );
}

function GetSubCategoryArray($ParentCategory, &$Array)
{
	$args = array(
  	'orderby' => 'name',
	'parent' => $ParentCategory,
	'current_category' => 1,
	'hide_empty' => 0
  	);

	$Array = get_categories( $args );
}




function PrintBlogPosts($ParentCategory)
{
	wp_reset_postdata();

	$args = array(
	'category'         => $ParentCategory,
	'orderby'          => 'post_date',
	'order'            => 'DESC',
	'suppress_filters' => true 
	);

	$posts_array = get_posts( 'cat='.$ParentCategory );
	print "<ul>";
	
	foreach	 ($posts_array as $post )
	{

		$categories = get_the_category($post->ID);

		if($ParentCategory != 0)
		{
			echo '<li><a href="'.get_permalink($post->ID).'">' . $post->post_title . '</a></li>';	
		}
                else
                {
                        if($ParentCategory == $categories[0]->cat_ID)
                        {
                              echo '<li><a href="'.get_permalink($post->ID).'">' . $post->post_title . '</a></li>';	
                        }
                }
	}

	print "</ul>";

	wp_reset_postdata();

}


function PrintCategoriesRecursive($ParentCategory, $Level, $HeadingSize, $AddOwnCategory)
{
	if($Level < 1)
	{
		return;
	}

	if( ($Level == RECURSION_LEVEL) && ($AddOwnCategory == ADD_OWN_CATEGORY) )
	{
		echo '<h'.$HeadingSize.'><u><a href="' . get_category_link( $ParentCategory ) . '">' . get_cat_name( $ParentCategory ) . '</a></u></h'.$HeadingSize.'>';

		PrintBlogPosts($ParentCategory);
		print "<ul>";
	}

	print "<ul>";

	$categories = array();
	GetSubCategoryArray($ParentCategory, $categories);

	foreach ( $categories as $category ) 
	{
		echo '<h'.$HeadingSize.'><u><a href="./?PostID='.$category->term_id. '">' . $category->name . '</a></u></h'.$HeadingSize.'>';


		PrintBlogPosts($category->term_id);
		PrintCategoriesRecursive($category->term_id, --$Level, 2, ADD_OWN_CATEGORY);	
	}

	if( ($Level == RECURSION_LEVEL) && ($AddOwnCategory == ADD_OWN_CATEGORY) )
	{
		print "<ul>";
	}
	print "</ul>";
}

function pwh_blog_sitemap_shortcode_handler( $args )
{


        
        add_option( 'pwh_html_blog_sitemap_gratuity_mode', 'on' );
	
	if(isset($_REQUEST["PostID"]))
	{
		PrintCategoriesRecursive($_REQUEST["PostID"], RECURSION_LEVEL, 1, ADD_OWN_CATEGORY);
	}
	else
	{
		PrintCategoriesRecursive(0, RECURSION_LEVEL, 1, ADD_OWN_CATEGORY);
	}

        $pwh_html_blog_sitemap_GratuityMode = get_option('pwh_html_blog_sitemap_gratuity_mode');

        if($pwh_html_blog_sitemap_GratuityMode == "on")
        {
	    print "<p>&nbsp;<p>HTML Blog Sitemap plugin by <a href=\"http://www.phpwebhost.co.za\" target=\"_new\">PHP Web Host</a>";
        }

}

add_shortcode('pwh_blog_sitemap', 'pwh_blog_sitemap_shortcode_handler');

?>
