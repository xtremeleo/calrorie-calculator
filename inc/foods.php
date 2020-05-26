<?php
function fd_custom_post_type()
{
	//food
	$food_labels = array(
		'name' => __( 'Food', 'fd' ),
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
		'menu_name' => __( 'Food', 'fd' )
	);

	$food_args = array(
	'labels'=> $food_labels,
	'hierarchical'=> true,
	'description' => 'food',
	'supports'=> array('author'),
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
							'create_posts' 		=> 'do_not_allow', // false < WP 4.5, credit @Ewout
							'edit_post'  		=> 'edit_food',
							//~ 'read_post'         => 'read_food',
							'delete_post'       => 'delete_food',
							//~ 'delete_posts'       => 'delete_foods',
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
    $defaults['interest'] = 'Interest';
    $defaults['loan_amount'] = 'Loan Amount';
    $defaults['total_food_value'] = 'Expected Total Food Value';
    $defaults['weekly_monthly_food_value'] = 'Weekly/Monthly Food Value';
    $defaults['previous_amount_paid'] = 'Previous Amount Paid';
    $defaults['amount_paid'] = 'Amount Paid';
    $defaults['payment_date'] = 'Payment Date';
    $defaults['default_charges'] = 'Default Charges';
    //~ $defaults['w_m_balance'] = 'Weekly / Monthly Balance';
	$defaults['balance'] = 'Balance'; 
	$defaults['payment_status'] = 'Payment Status';
    
    return $defaults;
}
 
// Show Amount
function fd_columns_content($column_name, $post_ID) 
{
	$fields = array('interest','loan_amount','total_food_value','weekly_monthly_food_value','amount_paid', 'previous_amount_paid','payment_date','default_charges','w_m_balance','balance', 'payment_status');
	
	foreach($fields as $field)
	{
		if ($column_name == $field) 
		{
			echo get_post_meta( $post_ID, $field, true);
		}
	}
}

add_filter('manage_fd_food_posts_columns', 'fd_columns_head');
add_action('manage_fd_food_posts_custom_column', 'fd_columns_content', 10, 2);

/**
 * Register meta box(es).
 */
function fd_register_meta_boxes() 
{
    add_meta_box( 'fd_payment', __( 'Payment', 'textdomain' ), 'fd_payment_callback', 'fd_food');
}
add_action( 'add_meta_boxes', 'fd_register_meta_boxes' );
 
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function fd_payment_callback( $post ) 
{
	//global $post;
    // Display code/markup goes here. Don't forget to include nonces!
   // Add a nonce field so we can check for it later.
    wp_nonce_field( 'fd_payment_nonce' , 'fd_payment_nonce' );
    echo "<style>
			.fd_review_table {width: 100%;}
			.fd_review_table tr {width: 100%;}
			.fd_review_table tr th {width: 40%; padding: 10px; color: #ffffff; background-color: #1E90FF;}
		</style>";
		
		$fields = array(
					//~ array("display_name" => "Previous Amount Paid", "type" => "text", "name" => "previous_amount_paid" ),
					array("display_name" => "Amount Paid", "type" => "text", "name" => "amount_paid" ),
					array("display_name" => "Payment Date DD/MM/YYYY", "type" => "text", "name" => "payment_date" ),
					array("display_name" => "Default Charges", "type" => "text", "name" => "default_charges" ),
					//~ array("display_name" => "Weekly / Monthly Balance", "type" => "text", "name" => "w_m_balance" ),
					array("display_name" => "Balance", "type" => "text", "name" => "balance" ),
					array("display_name" => "Payment Status", "type" => "datalist", "name" => "payment_status", "datalist" => "Pending,Approved" ),
				);
	echo "<table class='fd_review_table'>
			<input type='hidden' id='loan_amount' value='".esc_attr( get_post_meta( $post->ID, "loan_amount", true ) )."' required />
			<input type='hidden' id='weekly_monthly_food_value' value='".esc_attr( get_post_meta( $post->ID, "weekly_monthly_food_value", true ) )."' required />";
	
	foreach($fields as $field)
	{
		if ($field['type'] == "text")
		{
			$required = ( $field['name'] == "default_charges" ? "" : "required");
			echo "<tr>
					<th style='text-align: left;' >".$field['display_name']."</th>
					<td><input type='text' id='".$field['name']."' name='".$field['name']."' value='".esc_attr( get_post_meta( $post->ID, $field['name'], true ) )."' style='width:100%;' ".$required." /></td>
				</tr>";
		}
		else
		{
			$datalist = explode(",", $field['datalist']);
			$options = "<option value='".esc_attr( get_post_meta( $post->ID, $field['name'], true ) )."'>".esc_attr( get_post_meta( $post->ID, $field['name'], true ) )."</option>";
			foreach($datalist as $data)
			{
				$options .= "<option value='".$data."'>".$data."</option>";
			}
			
			echo "<tr>
					<th style='text-align: left;' >".$field['display_name']."</th>
					<td>
						<select id='".$field['name']."' name='".$field['name']."' style='width:100%' required>
							".$options."
						</select>
					</td>
				</tr>";
		}
		
		
	}
	
	echo "
		<script>
			
				(function () {
				  var ap = document.getElementById('amount_paid').onchange = function(){
								var la = document.getElementById('loan_amount').value;
								var wmrv = document.getElementById('weekly_monthly_food_value').value;
								var pap = document.getElementById('previous_amount_paid').value;
								document.getElementById('w_m_balance').setAttribute('value', parseInt(wmrv) - (parseInt(pap) + parseInt(this.value)) );
								document.getElementById('balance').setAttribute('value', parseInt(la) - (parseInt(pap) + parseInt(this.value)) );
							}
				})();
		</script>
		</table>";

}

function fd_save_meta_box( $post_id ) 
{
	
    // Save logic goes here. Don't forget to include nonce checks!
     // Check if our nonce is set.
    //~ if ( ! isset( $_POST['fd_amount'] ) ) {
        //~ return;
    //~ }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['fd_payment_nonce'], 'fd_payment_nonce' ) ) {
        return;
    }

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
					//~ array("display_name" => "Previous Amount Paid", "type" => "text", "name" => "previous_amount_paid" ),
					array("display_name" => "Amount Paid", "type" => "text", "name" => "amount_paid" ),
					array("display_name" => "Payment Date", "type" => "text", "name" => "payment_date" ),
					array("display_name" => "Default Charges", "type" => "text", "name" => "default_charges" ),
					//~ array("display_name" => "Weekly / Monthly Balance", "type" => "text", "name" => "w_m_balance" ),
					array("display_name" => "Balance", "type" => "text", "name" => "balance" ),
					array("display_name" => "Payment Status", "type" => "text", "name" => "payment_status" ),
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
			$my_data = sanitize_text_field( $_POST[$field['name']] );
			
			// Update the meta field in the database.
			update_post_meta( $post_id, $field['name'], $my_data );
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
add_action( 'restrict_manage_posts', 'fd_food_table_filtering' );

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
add_filter( 'parse_query', 'fd_food_filtering' );

//~ update_user_meta( int $user_id, string $meta_key, mixed $meta_value, mixed $prev_value = '' )
//~ function fd_log_staff()
//~ {
	
//~ }
//~ add_action( 'save_post', 'my_project_updated_send_email' );
?>
