<?php
require('settings.php');

/**
 * Fetches Instagram posts. Will write posts to local cache to avoid sending 
 * too many API requests. If cache does not exist or is over 3 hours old will 
 * fetch new data from API and rewrite to local cache.
 *
 * @param integer $count. Optional. The number of posts to retrieve. Default 5.
 * @return JSON array containing post data. 
 */
function myprefix_get_instagram_posts( $count = 5 ) {
  
  
  if ( !get_option('myprefix_instagram_access_token')) {
    return false;
  }
  
  /**
   * Set up query options
   */
  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    $count = $_GET['count'];
  }

  $ig_user_id = 'self';
  $token = get_option('myprefix_instagram_access_token'); 

  $params = array(
    'access_token' => $token,
    'count' => $count,
  );
  
  
  /**
   * Send GET request to Instagram
   */
  $url = 'https://api.instagram.com/v1/users/' . $ig_user_id . '/media/recent/?' . http_build_query($params);

  $remote_wp = wp_remote_get( $url );

  /**
   * Handle API response
   */
  if ( $remote_wp['response']['code'] == 400 ) {
    return $remote_wp['response']['message'] . ': ' . $instagram_response->meta->error_message;
  }

  $cache_file = __DIR__ . '/.instacache';

  /**
   * Handle API response
   */
  if (file_exists($cache_file) && json_decode(file_get_contents($cache_file))->cache_expire > time()) {
    // If the cache is valid return the data
    $data = file_get_contents($cache_file);
  } else {
    $result = array('posts' => json_decode($remote_wp['body']), 'cache_expire' => time() + 10800);
    $data = json_encode($result);
    file_put_contents( $cache_file, $data );
  }

  /**
   * Determine if function was called on client or server side and return accordingly
   */
  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    echo $data;
    die();
  } else {
    return $data;
  }
}

/**
 * Enqueue scripts 
 */
function myprefix_instagram_scripts() {
  wp_enqueue_script( 'myprefix_instagram_ajax', plugins_url( '/instagram-ajax.js', __FILE__ ), array('jquery'), '1.0', true );

  wp_localize_script( 'myprefix_instagram_ajax', 'myprefix_instagram', array(
    'ajax_url' => admin_url( 'admin-ajax.php' )
  ));
}

/**
 * Enable AJAX requests
 */
add_action( 'wp_ajax_nopriv_myprefix_get_instagram_posts', 'myprefix_get_instagram_posts' );
add_action( 'wp_ajax_myprefix_get_instagram_posts', 'myprefix_get_instagram_posts' );
add_action( 'wp_enqueue_scripts', 'myprefix_instagram_scripts' );

