<?php
function fd_custom_post_type()
{
	//food
	$food_labels = array(
		'name' => __( 'Foods', 'fd' ),
		'singular_name' => __( 'Food', 'fd' ),
		'add_new' => __( 'Add New', 'fd' ),
		'add_new_item' => __( 'Add New Food', 'fd' ),
		'edit_item' => __( 'Edit Food', 'fd' ),
		'new_item' => __( 'New Food', 'fd' ),
		'all_items' => __( 'All food', 'fd' ),
		'view_item' => __( 'View Food', 'fd' ),
		'search_items' => __( 'Search Food', 'fd' ),
		'not_found' => __( 'No Food found', 'fd' ),
		'not_found_in_trash' => __( 'No Food found in the Trash','fd' ),
		'parent_item_colon' => '',
		'menu_name' => __( 'Foods', 'fd' )
	);

	$food_args = array(
	'labels'=> $food_labels,
	'hierarchical'=> true,
	'description' => 'food',
	'supports'=> array('title', 'editor', 'thumbnail'),
	'public'=> false,
	'show_ui' => true,
	'show_in_menu' => true,
	'show_in_nav_menus' => false,
	'publicly_queryable' => false,
	'exclude_from_search' => true,
	'has_archive' => true,
	'query_var' => true,
	'show_admin_column' => true,
	'can_export' => true,
	'rewrite' => array( 'slug' => 'food' ),
	'capability_type' => array( 'post', 'food','foods'),
	'capabilities' => array(
							'create_posts' 		=> 'create_food', // false < WP 4.5, credit @Ewout
							'edit_post'  		=> 'edit_food',
							//~ 'read_post'         => 'read_food',
							'delete_post'       => 'delete_food',
							'delete_posts'       => 'delete_foods',
							'edit_others_posts' => 'edit_others_foods',
						  ),
	'menu_icon'   => 'dashicons-portfolio'
	);
	
	register_post_type('fd_food', $food_args);

}

function fd_add_new_admin_caps()
{
	//Add New Admin Capabilites
	$admin_role = get_role( 'administrator' );
	$admin_role->add_cap( 'create_food');
	$admin_role->add_cap( 'read_food');
	$admin_role->add_cap( 'edit_food');
	$admin_role->add_cap( 'read_others_food');
	$admin_role->add_cap( 'read_others_foods');
	$admin_role->add_cap( 'edit_others_food');
	$admin_role->add_cap( 'edit_others_foods');
	$admin_role->add_cap( 'delete_food');
	$admin_role->add_cap( 'delete_foods');
	$admin_role->add_cap( 'publish_food');
	$admin_role->add_cap( 'edit_published_food');
	$admin_role->add_cap( 'read_private_food');
	
	
}

add_action('init', 'fd_custom_post_type');
add_action( 'admin_init', 'fd_add_new_admin_caps');


// ADD NEW COLUMN
function fd_columns_head($defaults) 
{
	$fields = array(
					array('name'=>'Calories','value' => 'calories'),
					array('name'=>'Ingredients','value' => 'ingredients'),
					array('name'=>'Prep Time','value' => 'prep'),
					array('name'=>'CARBS','value' => 'carbs'),
					array('name'=>'Fat','value' => 'fats'),
					array('name'=>'Protein','value' => 'protein'),
					array('name'=>'Glycemic Score','value' => 'glycemic'),
					array('name'=>'Serving','value' => 'serving'),
					array('name'=>'Slot Time','value' => 'slot'),
				);
	
	foreach($fields as $field )
	{
		$defaults[$field['value']] = $field['name'];
	}
    
    return $defaults;
}
add_filter('manage_fd_food_posts_columns', 'fd_columns_head');
 

function fd_columns_content($column_name, $post_ID) 
{
	
	$fields = array(
					array('name'=>'Calories','value' => 'calories'),
					array('name'=>'Ingredients','value' => 'ingredients'),
					array('name'=>'Prep Time','value' => 'prep'),
					array('name'=>'CARBS','value' => 'carbs'),
					array('name'=>'Fat','value' => 'fats'),
					array('name'=>'Protein','value' => 'protein'),
					array('name'=>'Glycemic Score','value' => 'glycemic'),
					array('name'=>'Serving','value' => 'serving'),
					array('name'=>'Slot Time (Breakfast 1, Breakfast 2, Lunch 1, Lunch 2, Dinner)','value' => 'slot'),
				);
	
	foreach($fields as $field)
	{
		if ($column_name == $field['value']) 
		{
			echo get_post_meta( $post_ID, $field['value'], true);
		}
	}
}
add_action('manage_fd_food_posts_custom_column', 'fd_columns_content', 10, 2);


/**
 * Register meta box(es).
 */
function fd_register_meta_boxes() 
{
	$fields = array(
					array('name'=>'Calories','value' => 'calories'),
					array('name'=>'Ingredients','value' => 'ingredients'),
					array('name'=>'Prep Time','value' => 'prep'),
					array('name'=>'CARBS','value' => 'carbs'),
					array('name'=>'Fat','value' => 'fats'),
					array('name'=>'Protein','value' => 'protein'),
					array('name'=>'Glycemic Score','value' => 'glycemic'),
					array('name'=>'Serving','value' => 'serving'),
					array('name'=>'Slot Time (Breakfast 1, Breakfast 2, Lunch 1, Lunch 2, Dinner)','value' => 'slot'),
				);
	
	foreach($fields as $field )
	{
		add_meta_box( 'fb_'.$field['value'], $field['name'], 'fd_callback', 'fd_food','advanced','default', $field['value']);
	}
    
}
add_action( 'add_meta_boxes', 'fd_register_meta_boxes' );
 
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function fd_callback( $post, $metabox ) 
{
	//global $post;
    // Display code/markup goes here. Don't forget to include nonces!
   // Add a nonce field so we can check for it later.
    //wp_nonce_field( 'fd_payment_nonce' , 'fd_payment_nonce' );
    echo "<input type='text' id='".$metabox['args']."' name='".$metabox['args']."' value='".esc_attr( get_post_meta( $post->ID, $metabox['args'], true ) )."' style='width: 100%;padding: 4px;' />";

}

function fd_save_meta_box( $post_id ) 
{
	
    // Save logic goes here. Don't forget to include nonce checks!
     // Check if our nonce is set.
    //~ if ( ! isset( $_POST['fd_amount'] ) ) {
        //~ return;
    //~ }

    // Verify that the nonce is valid.
    //~ if ( ! wp_verify_nonce( $_POST['fd_nonce'], 'fd_nonce' ) ) {
        //~ return;
    //~ }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    {
        return;
    }

    // Check the user's permissions.
    //~ if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        //~ if ( ! current_user_can( 'edit_page', $post_id ) ) {
            //~ return;
        //~ }

    //~ }
    //~ else {

        //~ if ( ! current_user_can( 'edit_post', $post_id ) ) {
            //~ return;
        //~ }
    //~ }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    //~ if ( ! isset( $_POST['fd_amount'] ) ) {
        //~ return;
    //~ }

    

    
    $fields = array(
					array('name'=>'Calories','value' => 'calories'),
					array('name'=>'Ingredients','value' => 'ingredients'),
					array('name'=>'Prep Time','value' => 'prep'),
					array('name'=>'CARBS','value' => 'carbs'),
					array('name'=>'Fat','value' => 'fats'),
					array('name'=>'Protein','value' => 'protein'),
					array('name'=>'Glycemic Score','value' => 'glycemic'),
					array('name'=>'Serving','value' => 'serving'),
					array('name'=>'Slot Time (Breakfast 1, Breakfast 2, Lunch 1, Lunch 2, Dinner)','value' => 'slot'),
				);
				
	foreach($fields as $field)
	{	
		// Make sure that it is set.
		//~ if ( empty( $_POST[$field['name']]) ) 
		//~ {
			//~ return;
		//~ }
		//~ else
		//~ {
			// Sanitize user input.
			$my_data = sanitize_text_field( $_POST[$field['value']] );
			
			// Update the meta field in the database.
			update_post_meta( $post_id, $field['value'], $my_data );
		//~ }
		
		
	}
	
    
}
add_action( 'save_post', 'fd_save_meta_box');


function fd_food_table_filtering() 
{
	global $typenow;
	global $wp_query;
    
    if ( $typenow == 'fd_food' ) 
    { 
		// Your custom post type slug
		$statuses = array( "Approved", "Pending"); // Options for the filter select field
		$current_status = '';
      
		if( isset( $_GET['payment_status'] ) ) 
		{
			$current_status = $_GET['payment_status']; // Check if option has been selected
		} 
		
		?>
      
		<select name="payment_status" id="slug">
			<option value="" ><?php _e( 'Select Status ', 'fd_food' ); ?></option>
			<?php 
				foreach( $statuses as $key=>$value ) 
				{ 
					?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $current_status ); ?> ><?php echo esc_attr( $value ); ?></option>
					<?php 
				} 
			?>
      </select>
  <?php }
}
//~ add_action( 'restrict_manage_posts', 'fd_food_table_filtering' );

function fd_food_filtering( $query ) 
{
	global $pagenow;
	
	// Get the post type
	$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
	
	if ( is_admin() && $pagenow=='edit.php' && $post_type == 'fd_food' && isset( $_GET['payment_status'] ) && !empty($_GET['payment_status']) ) 
	{
		$query->query_vars['meta_key'] = 'payment_status';
		$query->query_vars['meta_value'] = $_GET['payment_status'];
		$query->query_vars['meta_compare'] = '=';
		
	}
}
//~ add_filter( 'parse_query', 'fd_food_filtering' );

//~ update_user_meta( int $user_id, string $meta_key, mixed $meta_value, mixed $prev_value = '' )
//~ function fd_log_staff()
//~ {
	
//~ }
//~ add_action( 'save_post', 'my_project_updated_send_email' );


function fd_foods_menu() 
{
	add_submenu_page( 'edit.php?post_type=fd_food', 'Import Foods Data', 'Import', 'manage_options', 'import-foods-data', 'fd_import_foods');
}

add_action( 'admin_menu', 'fd_foods_menu' );

	
function fd_import_foods()
{
	?>
	<div class="wrap">
		<h1>Import Food CSV file</h1>
		
		<form action="" method="post" enctype="multipart/form-data">
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="blogname">CSV File</label>
							<br>
							<small>sample format for the <a href="<?php echo esc_url( plugins_url( '../assets/files/sample.csv', __FILE__ ) );?>" download>CSV file</a>.</small>
						</th>
						<td><input name="fd_foodcsvfile" type="file" accept="text/csv" class="regular-text"></td>
					</tr>
				</tbody>
			</table>
			
			<p class="submit"><button type="submit" name="action" id="submit" class="butesc_url( plugins_url( 'images/wordpress.png', __FILE__ ) ) ton button-primary" value="fd_import">Import</button></p>
		</form>
		
	</div>
	<?php
}

?>

