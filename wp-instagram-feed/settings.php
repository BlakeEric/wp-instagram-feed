<?php

//Add an options page

if (is_admin()) {
  add_action('admin_menu', 'myprefix_instagram_menu');
  add_action('admin_init', 'myprefix_instagram_register_settings');
}

function custom_instagram_menu() {
	add_options_page('Instagram Feed', 'Instagram Feed', 'manage_options', 'myprefix_instagram_settings', 'myprefix_instagram_settings_output');
}

function myprefix_instagram_settings() {
	return array(
    array(
      'name'=> 'myprefix_instagram_access_token',
      'label'=> 'API Access Token'
    ),
	);
}

function myprefix_instagram_register_settings() {
	$settings = myprefix_instagram_settings();
	foreach($settings as $setting) {
		register_setting('myprefix_instagram_settings', $setting['name']);
	}
}


function myprefix_instagram_settings_output() {
  
	$settings = myprefix_instagram_settings();

	echo '<div class="wrap">';
      echo '<h2>Instagram API</h2>';
      echo '<p>' . 'All we need is a registered app and an API token to grab posts from your instagram account.' .'</p>';
      echo '<p>' . 'Most of this configuration can found on the application overview page on the <a target="_blank" href="https://www.instagram.com/developer/">https://www.instagram.com/developer/</a> website.' .'</p>';
      echo '<p>' . '<a href="http://instagram.pixelunion.net/">This tool</a> Can be used to generate an access token.' .'</p>';
      echo '<hr />';
      echo '<form method="post" action="options.php">';
      settings_fields('myprefix_instagram_settings');
  
      echo '<table>';
        foreach($settings as $setting) {
          echo '<tr>';
              echo '<td>'.$setting['label'].'</td>';
              echo '<td><input type="text" style="width: 400px" name="'.$setting['name'].'" value="'.get_option($setting['name']).'" /></td>';
          echo '</tr>';          
        }
      echo '</table>';
  
      submit_button();
  
      echo '</form>';
      echo '<hr />';
	echo '</div>';

}
