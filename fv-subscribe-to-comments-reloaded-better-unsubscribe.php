<?php
/*
Plugin Name: Subscribe to Comments Reloaded Better Unsubscribe
Plugin URI: http://foliovision.com/wordpress/plugins/subscribe-to-comments-reloaded-better-unsubscribe/
Description: Enhancement for Subscribe to Comments Reloaded - unsubscribe from comment notifications using a single click
Author: Foliovision
Version: 0.9.5
Author URI: http://foliovision.com/
*/

function FV_STCR_removeBrokenCanonical() {
  global $post;
  
  if($post->ID ==='9999999') {        
    remove_action( 'wp_head', 'wp_shortlink_wp_head');
    remove_action( 'wp_head', 'rel_canonical' );
    add_filter('fvseop_canonical_url','__return_false');
  }
  
}

function FV_STCR_checkEmail( $input ) {
  if( strpos( $input, 'Manage your subscriptions' ) === false || strpos($input, "sre=") === false || strpos($input, "srk=") === false ) {
    return false;
  }
  else{
    return true;
  }
}


function FV_STCR_changeEmail( $input ){
  global $FV_STCR_postID;

	$opt = get_option('subscribe_reloaded_manager_page');
	$permalink = trim(get_option('permalink_structure'));
	
	$add = "";  
	if( $permalink && $permalink[strlen($permalink)-1] == "/"  ){
		$add = "/";
	}
	
	preg_match( '~#comment-(\d+)~', $input, $comment_id );
	if( isset($comment_id[1]) && intval($comment_id[1]) > 0 ) {
		global $wpdb;
		$FV_STCR_postID = $wpdb->get_var( $wpdb->prepare( "SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = %d LIMIT 1", $comment_id[1] ) );
		$input = preg_replace( '~('.$opt.')(.*sre=.+)(&srk=.*)$~', '$1'.$add.'$2&fvunsub='.$FV_STCR_postID.'$3', $input );
		$input = str_replace('Manage your subscriptions:', 'Unsubscribe:',$input );
	}
  
  return $input;
}


function FV_STCR_UnsubscribeLink( $array ) {
  $isValidEmail = FV_STCR_checkEmail( $array['message'] );
  if( $isValidEmail ) {
    $array['message'] = FV_STCR_changeEmail( $array['message'] );
    $array['message'] .= "\n\nThis notification was sent to {$array['to']}.";
  }
  return $array;
}


function FV_STCR_Unsubscribe( $content ) {
  global $wpdb;
  
  if( !isset($_POST['sre']) && isset($_GET['fvunsub']) && isset($_GET['sre']) ) {    
    $fvstcrEmail = $_GET['sre'];
    $fvstcrID = $_GET['fvunsub'];

    $meta = get_post_meta($fvstcrID,'_stcr@_'.$fvstcrEmail);

    if( strpos($meta[0], "|Y") !== false ) {
      $unsubValue = str_replace('|Y', '|YC', $meta[0]);
      if( update_post_meta($fvstcrID, '_stcr@_'.$fvstcrEmail, $unsubValue) != false ) {
        //call filter
        add_filter( 'the_content', 'FV_STCR_ShowNotice' );
      }
    }
  }
  return $content;
}


function FV_STCR_ShowNotice( $content ){
  global $post;

  if( !isset( $_GET['fvunsub'] )) {
    return $content;
  }
  
  //management page
  $FV_STCR_postTitle = "";
  if( $post->ID == '9999999' ) {
    $FV_STCR_postTitle = get_the_title( $_GET['fvunsub'] );
    $FV_STCR_postLink = get_permalink( $_GET['fvunsub'] );
    $divStyle = 'style="margin: 5px 0 15px;background-color: #ffffe0;border-color: #e6db55;
                 padding: 0 .6em;-webkit-border-radius: 3px;border-radius: 3px;border-width: 1px;
                 border-style: solid;outline: 0;display: block;color: #333;font-family: sans-serif;
                 font-size: 12px;line-height: 1.4em;"'; 
    $pStyle = 'style="display: block;-webkit-margin-before: 1em;-webkit-margin-after: 1em;
               -webkit-margin-start: 0px;-webkit-margin-end: 0px;"'; 
    $strongStyle = 'style="font-weight: bold;"'; 
    $addedString = "<div $divStyle><p $pStyle><strong $strongStyle>You are now unsubscribed from <a href=\"". $FV_STCR_postLink ."\">$FV_STCR_postTitle</a>.</strong></p></div>";
    $content = $addedString.$content;
  }
  return $content;
}


add_action('wp_head','FV_STCR_removeBrokenCanonical',-1);
add_action('init', 'FV_STCR_Unsubscribe');
add_filter('wp_mail', 'FV_STCR_UnsubscribeLink');
add_filter('plugin_action_links', 'fv_subscribe_to_comments_reloaded_better_unsubscribe_plugin_action_links', 10, 2);


function fv_subscribe_to_comments_reloaded_better_unsubscribe_plugin_action_links($links, $file) {
  	$plugin_file = basename(__FILE__);
  	if (basename($file) == $plugin_file) {
      $settings_link =  'No settings';
  		array_unshift($links, $settings_link);
  	}
  	return $links;
}

// cron and it's functions start here

// no needed
/*
add_filter('cron_schedules','FV_STCR_cron_schedules');


function FV_STCR_cron_schedules($schedules){
  
  $schedules['5minutes'] = array(
    'interval' => 300,
    'display' => __('Every 5 minutes')
  );
  
  $schedules['60minutes'] = array(
    'interval' => 3600,
    'display' => __('Every hour')
  );
  
  return $schedules;
}
*/


register_deactivation_hook(__FILE__, 'FV_STCR_sharing_deactivation');


function FV_STCR_sharing_deactivation(){
  wp_clear_scheduled_hook('FV_STCR_sharing_cron_event');
}


add_action('FV_STCR_sharing_cron_event','FV_STCR_sharing_cron');

function FV_STCR_check_sendgrid_options(){
  
  $options = array(
    'password' => '',
    'email' => ''
  );
  
  $password = get_option('smtp_pass');
  $email = get_option('smtp_user');
  if($password == '' || $email == ''){
    
    global $mailer;
    if((isset($mailer->options['smtp_pass'])) && (isset($mailer->options['smtp_user']))){
      $password = $mailer->options['smtp_pass'];
      $email = $mailer->options['smtp_user'];
    }
    
  }
  
  if($password == '' || $email == ''){
    return false;
  }else{
    $options['password'] = $password;
    $options['email'] = $email;
  }
  return $options;
  
}


function FV_STCR_sharing_cron(){
  
  if(FV_STCR_check_sendgrid_options() != false){
    
    $options = FV_STCR_check_sendgrid_options();
    $password = $options['password'];
    $email = $options['email'];
    
    
    $json = @file_get_contents('https://sendgrid.com/api/bounces.get.json?api_user='.$email.'&api_key='.$password.'&type=hard');
    $result = @json_decode($json);
    
    $json = @file_get_contents('https://sendgrid.com/api/invalidemails.get.json?api_user='.$email.'&api_key='.$password.'&type=hard');
    $result2 = @json_decode($json);
    
    $json = @file_get_contents('https://sendgrid.com/api/spamreports.get.json?api_user='.$email.'&api_key='.$password.'&type=hard');
    $result3 = @json_decode($json);
    
    $json = @file_get_contents('https://sendgrid.com/api/unsubscribes.get.json?api_user='.$email.'&api_key='.$password.'&type=hard');
    $result4 = @json_decode($json);
    
    if($result != NULL ){
      
      $iCount = 0;
      $all_users = array_merge($result,$result2,$result3,$result4);
      
      global $wpdb;
      
      foreach($all_users as $user){
	
	$sql = "SELECT post_id,meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key ='_stcr@_$user->email' ";
	$results = $wpdb->get_results( $sql);
	
	foreach($results as $result){
	  
	  if( strpos($result->meta_value, "|Y") !== false && strpos($result->meta_value, "|YC") == false ) {
	    $iCount++;
	    $unsubValue = str_replace('|Y', '|YC', $result->meta_value);
	    update_post_meta($result->post_id,"_stcr@_$user->email",$unsubValue);
	  }
	  
	}
	
      }
      
      if($iCount > 0){
	update_option( 'stcrbe_last_run', time() );
	update_option( 'stcrbe_unsubscribed', $iCount );
      }
      
      
    }
    
  }
}

if (is_admin()){
  if ( !wp_next_scheduled( 'FV_STCR_sharing_cron_event' ) ) {
    wp_schedule_event( time(), 'hourly', 'FV_STCR_sharing_cron_event' );
  }
}

function FV_STCR_smtp_options_notice(){
  if($_GET['page']=='subscribe-to-comments-reloaded/options/index.php'){
    if(FV_STCR_check_sendgrid_options()){
      echo '<div class="updated"><p> Subscribe to Comments Reloaded Better Unsubscribe found that you are using SendGrid. It will automatically unsubscribe addresses which are reported as bounces.  </p></div>';
    }else{
      echo '<div class="error fade"><p> Subscribe to Comments Reloaded Better Unsubscribe can be set to process SendGrid bounces to prevent spam reports. Just use WP Mail STMP or Mailer plugin with your SendGrid login details and will make sure bounced email addresses are unsubscribed.  </p></div>';
    }
  }
}

add_action('admin_notices','FV_STCR_smtp_options_notice');

// cron and it's functions end here


?>
