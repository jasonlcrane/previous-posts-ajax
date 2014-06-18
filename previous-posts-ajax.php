<?php
/*
Plugin Name: Previous Posts AJAX
Plugin URI:  https://github.com/jasonlcrane/previous-posts-ajax
Description: Load previous posts on the same page instead of loading a new page when clicking the previous posts link.
Version: 1.0
Author: Jason Crane
Author URI: http://jasonlcrane.com
License: GPLv2
*/
/*  Copyright 2014 Jason Crane  (email : jasonlcrane@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class PreviousPostsAjax
{

  /**
   * Constructor method:
   */
    function __construct()
    {

        /*******************************************************************************
         ** CONSTANTS **
         *******************************************************************************/
        define('PPAURL', plugins_url()."/".dirname(plugin_basename(__FILE__)));
        define('PPAPATH', WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__)));

        /*******************************************************************************
         ** ENQUEUE SCRIPTS AND STYLES **
         *******************************************************************************/
        add_action('wp_enqueue_scripts', array(&$this,'ppa_enqueue_scripts'), 40);
        add_action('wp_enqueue_scripts', array(&$this,'ppa_enqueue_styles'), 40);

        /*******************************************************************************
         ** AJAX METHODS **
         *******************************************************************************/
        add_action('wp_ajax_load_more_posts',  array(&$this,'get_previous_posts'));
        add_action('wp_ajax_nopriv_load_more_posts',  array(&$this,'get_previous_posts'));

        add_filter('previous_posts_link_attributes', array(&$this,'ppa_post_links'));
        add_filter('next_posts_link_attributes',array(&$this, 'ppa_post_links'));

    }

    public static function ppa_enqueue_scripts()
    {
      // TODO add setting for no_more_stories_text to come from plugin
      wp_enqueue_script('ppa-js', PPAURL.'/js/ppa.js', array('jquery'), '', true);
      wp_localize_script('ppa-js', 'ppa_ajax', array('ajaxurl' => admin_url('admin-ajax.php'), 'offset' => get_option( 'posts_per_page' ), 'no_more_posts_text' => 'No more posts'));
    }

    public static function ppa_enqueue_styles()
      {
        wp_enqueue_style('ppa-css', PPAURL.'/css/ppa.css');
      }

    public static function get_previous_posts()
    {

        $args = array(
            'posts_per_page' => $_POST['offset'],
            'paged'          => $_POST['page'],
            'orderby'        => 'post_date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
            'cat'            => $_POST['cat']
        );

        if ($_POST['year']) {
          $args['year'] = $_POST['year'];
        }
        if ($_POST['month']) {
          $args['monthnum'] = $_POST['month'];
        }

        $ppa_query = new WP_Query( $args );
        if ( $ppa_query->have_posts() ) {
            $html = '';
            while ( $ppa_query->have_posts() ) {
                $ppa_query->the_post();
                $html .= get_template_part('content', 'page');

            }

        }

        die($html);
    }

    // creates a string of attributes that will be added to the previous posts link
    public static function ppa_post_links($link) {
      global $wp_query;
      // get the year from the query
      $year = $wp_query->query_vars['year'];
      // get the month from the query
      $month = $wp_query->query_vars['monthnum'];
      if ($year == 0) {
      	$year = '';
      }
      if ($month == 0) {
      	$month = '';
      }
      // create $attributes array
      $atts = array();
      // add the category id if applicable
      $atts[] = 'data-catid="' . $wp_query->query_vars['cat'] . '"';
      // add the year if applicable
      $atts[] = 'data-year="' . $year  . '"';
      // add the month if applicable
      $atts[] = 'data-month="' . $month . '"';
      // add the search string if applicable
      $atts[] = 'data-s="' . $wp_query->query_vars['s'] . '"';
      // put the attributes array together and return it
      return implode($atts, ' ');
   }

}

/**
 * Create instance of controller
 */
$ppa = new PreviousPostsAjax();

/* EOF */

?>
