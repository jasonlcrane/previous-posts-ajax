<?php

/*
 * class PPA_Views
 *
 * Templates for the Previous Posts AJAX plugin.
*/

class PPA_Views
{

  /**
 * Returns template file
 *
*/
  public static function template_chooser($template) {

  // Post ID
  $post_id = get_the_ID();

  return self::get_template_hierarchy('content');

}

/**
 * Get the custom template if is set
 *
*/

  public static function get_template_hierarchy( $template ) {
    // Get the template slug
    $template_slug = rtrim($template, '.php');
    $template      = $template_slug . '.php';

    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template(array($template)) ) {
      $file = $theme_file;
    }
    else {

      $file = PPAPATH . '/includes/templates/' . $template;
    }

    return apply_filters( 'repl_template_'.$template, $file);
  }


}

/* EOF */
?>
