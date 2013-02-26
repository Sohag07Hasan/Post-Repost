<?php 
/*
 * plugin name: Post Repost 
 * Description: The plugin will schedule a post for the next day
 * author: Mahibul Hasan
 * Plugin uri: http://sohag07hasan.elance.com
 * authr uri: http://sohag07hasan.elancec.com
 * */

define("POSTREPOST_FILE", __FILE__);
define("POSTREPOST_DIR", dirname(__FILE__));
include POSTREPOST_DIR . '/classes/post-repost.php';
Post_Repost::init();



?>