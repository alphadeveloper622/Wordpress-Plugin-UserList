<?php
/*
Plugin Name:  Customized User List
Plugin URI:   https://localhost 
Description:  Customized User List
Version:      1.0
Author:       Phuong 
Author URI:   https://localhost
License:      GPL2
License URI:  https://localhost
Text Domain:  wpb-user-list
Domain Path:  /languages
*/

function add_styles() {
    wp_enqueue_style( 'userlist',  plugin_dir_url( __FILE__ ) . '/css/userlist.css' );
}

add_action( 'wp_enqueue_scripts', 'add_styles' );

function user_list_handle(){
	$content = '';
 
	$users = get_users( array(
        'role' => 'kunde',
         'meta_key'   => 'oprettelses_dato',
    	 'orderby'    => 'meta_value',
    	 //'order'      => 'DESC',
    ) );
    setlocale( LC_TIME, 'da_DK' );
    foreach ( $users as $user ) {
		$user_meta = get_user_meta( $user->ID );
    	$args = array(
        'author' => $user->ID,
        'post_type' => 'reklamation',
        'tax_query' => array(
            array(
                'taxonomy' => 'rek-status',
                'field' => 'slug',
                'terms' => 'igangværende',
            ),
        ),
        'posts_per_page' => -1,
        );
    
		$query = new WP_Query( $args );
		$number_of_posts = $query->found_posts;
		if($number_of_posts !=0){
    	$content .='
    <div class="user-list-item">
        <p class="user-id">'.$user->display_name.'</p>
        <p class="user-address">'.$user_meta["gadenavn"][0].'&nbsp;'.$user_meta["postnummer"][0].'&nbsp;'.$user_meta["by"][0].'</p>
        <div class="user-posts-number"><i aria-hidden="true" class="far fa-file-alt"></i>&nbsp;'.$number_of_posts.'</div>
        <p class="user-date">'.date_i18n( 'd M Y', strtotime( $user_meta["oprettelses_dato"][0] ) ).'</p>
        <div class="user-posts-link">
        
        <a href="http://reklamation.inka-web.dk/stenhoej-alle-reklamationer-med-filter/?_sag='.$user->ID.'&_status=igangvaerende"><i aria-hidden="true" class="fas fa-step-forward"></i></a>
        </div>
    </div>
    ';}
	}
    
	 return $content;
}

function shortcodes_init(){
    add_shortcode('user_list', 'user_list_handle');
}
add_action('init', 'shortcodes_init');