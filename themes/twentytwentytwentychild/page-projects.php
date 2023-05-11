<?php
/*
Template Name: Projects Page Template
*/

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">
    <?php
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

      endwhile;

      // Display pagination links
      the_posts_pagination( array(
        'prev_text' => __( 'Previous', 'textdomain' ),
        'next_text' => __( 'Next', 'textdomain' ),
      ) );

    else :

      // Display message if no projects found
      get_template_part( 'template-parts/content', 'none' );

    endif;

    wp_reset_postdata();
    ?>

  </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
?>
