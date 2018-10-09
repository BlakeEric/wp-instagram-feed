<?php
require('settings.php');

add_action( 'wp_ajax_nopriv_myprefix_get_instagram_posts', 'myprefix_get_instagram_posts' );
add_action( 'wp_ajax_myprefix_get_instagram_posts', 'myprefix_get_instagram_posts' );

function myprefix_get_instagram_posts( $count = 5 ) {

  if ( !get_option('myprefix_instagram_access_token')) {
    return false;
  }

  $ig_user_id = 'self';
  $token = get_option('myprefix_instagram_access_token'); 

  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    $count = $_GET['count'];
  }

  $data = array(
    'access_token' => $token,
    'count' => $count,
  );

  $url = 'https://api.instagram.com/v1/users/' . $ig_user_id . '/media/recent/?' . http_build_query($data);

  $remote_wp = wp_remote_get( $url );

  if ( $remote_wp['response']['code'] == 400 ) {
    return $remote_wp['response']['message'] . ': ' . $instagram_response->meta->error_message;
    // return false;
  }

  $cache_file = __DIR__ . '/.instacache';

  if (file_exists($cache_file) && json_decode(file_get_contents($cache_file))->cache_expire > time()) {
    // If the cache is valid return the data
    $data = file_get_contents($cache_file);
  } else {
    $result = array('posts' => json_decode($remote_wp['body']), 'cache_expire' => time() + 10800);
    $data = json_encode($result);
    file_put_contents( $cache_file, $data );
  }

  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    echo $data;
    die();
  } else {
    return $data;
  }
}

/**
 * Enable AJAX requests
 */
add_action( 'wp_enqueue_scripts', 'myprefix_instagram_scripts' );

function myprefix_instagram_scripts() {
  wp_enqueue_script( 'myprefix_instagram_ajax', plugins_url( '/instagram-ajax.js', __FILE__ ), array('jquery'), '1.0', true );

  wp_localize_script( 'myprefix_instagram_ajax', 'myprefix_instagram', array(
    'ajax_url' => admin_url( 'admin-ajax.php' )
  ));
}



