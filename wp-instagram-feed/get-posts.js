function getInstagramPosts(count, callback) {
  jQuery.ajax({
    url: bc_instagram.ajax_url,
    data: {
      action: 'myprefix_get_instagram_posts',
      count: count
    },
    error: function( err ) {
      return false;
    },
    success: callback 
  });
}
