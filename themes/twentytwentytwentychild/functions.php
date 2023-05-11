<?php
/* enqueue scripts and style from parent theme */
   
function twentytwentyone_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_uri(),
	array( 'twenty-twenty-one-style' ), wp_get_theme()->get('Version') );
}
add_action( 'wp_enqueue_scripts', 'twentytwentyone_styles');

// redirect users

add_action( 'init', 'redirect_users_with_ip' );
function redirect_users_with_ip() {
  if ( strpos( $_SERVER['REMOTE_ADDR'], '77.29' ) === 0 ) {
    wp_redirect( 'https://www.ikonicsolution.com/' );
    exit;
  }
}

function custom_post_type() {
  // Register custom post type
  register_post_type( 'projects',
    array(
      'labels' => array(
        'name' => __( 'Projects' ),
        'singular_name' => __( 'Project' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'projects'),
      'menu_icon' => 'dashicons-portfolio',
      'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' )
    )
  );
  
  // Register custom taxonomy for the post type
  register_taxonomy(
    'project_type',
    'projects',
    array(
      'labels' => array(
        'name' => __( 'Project Types' ),
        'singular_name' => __( 'Project Type' )
      ),
      'public' => true,
      'hierarchical' => true
    )
  );
}
add_action( 'init', 'custom_post_type' );


function architecture_projects_ajax_handler() {
  // Check if user is logged in
  if ( is_user_logged_in() ) {
    $posts_per_page = 6;
  } else {
    $posts_per_page = 3;
  }
  
  // Query for projects
  $args = array(
    'post_type' => 'projects',
    'posts_per_page' => $posts_per_page,
    'tax_query' => array(
      array(
        'taxonomy' => 'project_type',
        'field' => 'slug',
        'terms' => 'Architecture'
      )
    )
  );
  
  $projects_query = new WP_Query( $args );
  
  $data = array();
  
  if ( $projects_query->have_posts() ) {
    while ( $projects_query->have_posts() ) {
      $projects_query->the_post();
      $data[] = array(
        'id' => get_the_ID(),
        'title' => get_the_title(),
        'link' => get_permalink()
      );
    }
  }
  
  wp_reset_postdata();
  
  $response = array(
    'success' => true,
    'data' => $data
  );
  
  wp_send_json( $response );
}
add_action( 'wp_ajax_architecture_projects', 'architecture_projects_ajax_handler' );
add_action( 'wp_ajax_nopriv_architecture_projects', 'architecture_projects_ajax_handler' );



function hs_give_me_coffee() {
  // Set the API endpoint URL
  $api_url = 'https://coffee.alexflipnote.dev/random.json';
  
  // Make the API request using the WordPress HTTP API
  $response = wp_remote_get( $api_url );
  
  // Check for errors
  if ( is_wp_error( $response ) ) {
    return 'Error: ' . $response->get_error_message();
  }
  
  // Get the response body and decode it from JSON
  $body = wp_remote_retrieve_body( $response );
  $data = json_decode( $body );
  
  // Check for errors
  if ( ! $data ) {
    return 'Error: Invalid response from API';
  }
  
  // Return the coffee image URL
  return $data->file;
}
