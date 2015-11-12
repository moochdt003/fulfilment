<?php

function moa_tracking_page()
  {
   //post status and options
    $post = array(
          'comment_status' => 'closed',
          'ping_status' =>  'closed' ,
          'post_author' => 1,
          'post_date' => date('Y-m-d H:i:s'),
          'post_name' => 'MOAtracking',
          'post_status' => 'publish' ,
          'post_title' => 'MOA tracking',
          'post_type' => 'page',
    );  
    //insert page and save the id
    $newvalue = wp_insert_post( $post, false );
    //save the id in the database
    update_option( 'hclpage', $newvalue );
  }
  
  
  add_filter ('the_content', 'insertSubscribeNewsLetter');
function insertSubscribeNewsLetter($content) {
   if(is_single()) {
      $content.= '<div style="border:1px dotted #000; text-align:center; padding:10px;">';
      $content.= '<h4>Enjoyed this article?</h4>';
      $content.= '<p><a href="http://ithemes.com/feed/">Stay up to date, and subscribe to our RSS feed!</a></p>';
      $content.= '</div>';
   }
   return $content;
}