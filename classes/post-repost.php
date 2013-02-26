<?php 
//handle everything for cron jobs

class Post_Repost{
	
	//constants
	const hook = "wp_auto_post_repost";
	const interval = "twicedaily";
	
	//contains all the hooks
	static function init(){
		register_activation_hook(POSTREPOST_FILE, array(get_class(), 'create_scheduler'));
		register_deactivation_hook(POSTREPOST_FILE, array(get_class(), 'clear_scheduler'));
		add_action(self::hook, array(get_class(), 'schedule_posts_to_repost'));
		
		add_action('admin_menu', array(get_class(), 'admin_menu'));
		
		//add_action('init', array(get_class(), 'schedule_checking'));
	}
	
	static function schedule_checking(){
		var_dump(wp_next_scheduled(self::hook));
		var_dump(time());
	}
	
	
	/*
	 * handle scheduler
	 * */
	static function create_scheduler(){
		
		if(!wp_next_scheduled(self::hook)) {
			wp_schedule_event( current_time( 'timestamp' ), self::interval, self::hook);
		}
	}
	
	/*
	 * clear the scheduler
	 * */
	static function clear_scheduler(){
		wp_clear_scheduled_hook(self::hook);
	}
	
	
	//original function
	static function schedule_posts_to_repost(){
		global $wpdb;
		$posts = self::get_post_to_be_reposted();
		
		//now doing the mian job
		if($posts){
			$scheduled_categories = self::get_scheduled_categories();
			foreach($posts as $post){
				$categories = wp_get_object_terms($post->ID, 'category', array('fields' => 'ids'));
				if($categories){
					foreach($categories as $cat){
						if(in_array($cat, $scheduled_categories)){
							self::update_post($post->ID);
							break;
						}
					}
				}
			}
					
		}
	}
	
	
	
	//update operation occurs here
	static function update_post($post_id){
		$post = get_post($post_id);
				
		$post_date = strtotime($post->post_date);
		
		$new_year = date('Y', $post_date) + 1;
		$new_date = $new_year . date('-m-d H:i:s', $post_date);
		
		//var_dump($post); die();
		
		return wp_insert_post(array(
			'ID' => $post_id,
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,
			'post_status' => 'future',
			'post_date' => $new_date		
		));
				
	}
	
	
	
	//return the post
	static function get_post_to_be_reposted(){
		$current_time = current_time( 'timestamp' );
		$tomorrow = $current_time + 24 * 60 * 60 ;
		$year = (int) date('Y', $tomorrow) - 1;
		$tomorrows_date_of_prvious_year = $year . date('-m-d', $tomorrow) . ' 00:00:00';
		$tomorrows_date_of_prvious_year_interval = $year . date('-m-d', $tomorrow) . ' 23:59:59';

		//query sql
		global $wpdb;
		$sql = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date BETWEEN '$tomorrows_date_of_prvious_year' AND '$tomorrows_date_of_prvious_year_interval' ";
		
		return $wpdb->get_results($sql);
		
	}
	
	
	//admin menu 
	static function admin_menu(){
		add_options_page( 'postrepost option page', 'Post Scheduler', 'manage_options', 'postrepost', array(get_class(), 'options_page_content'));
	}
	
	
	//contains the options page
	static function options_page_content(){
		if($_POST['postrepostscheuler'] == 'submitted'){
			update_option('post-repost-scheduler-categories', $_POST['scheduler_categories']);
		}
		include POSTREPOST_DIR . '/includes/options-page.php';
	}
	
	
	//returns the categories
	static function get_all_categories(){
		$options = array(
			'taxonomy' => 'category',
			'type' => 'post',
			'hide_empty' => 0
		);
		$categories = get_categories($options);
		return $categories;
	}
	
	
	//retyrns scheudled category lists
	static function get_scheduled_categories(){
		return get_option('post-repost-scheduler-categories');
	}
}
