<?php
function fd_actions()
{	
	global $errors;
	$errors = new WP_Error();
	
	if (!empty($_POST['action']))
	{
		$action = $_POST['action'];
		
		if ($action == "fd_import")
		{
			$foods = array();
			//$meta_input = array();
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
				
			if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
				
			$uploadedfile = $_FILES['fd_foodcsvfile'];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			
			if ( $movefile ) 
			{
				//var_dump( $movefile);
				
				$row = 0;
				
				if (($handle = fopen($movefile['file'], "r")) !== FALSE) 
				{
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
					{
						if ($row > 0)
						{
							$num = count($data);
						
							for ($c = 0; $c < $num; $c++) 
							{
								if ($c == 0)
								{
									$foods = array('post_title' => $data[$c], 'post_author' => $current_user->ID, 'post_status' => 'publish', 'post_type' => 'fd_food');
									$post_id = wp_insert_post( $foods );
								}
								else
								{
									update_post_meta( $post_id, $fields[$c-1]['value'], $data[$c] );
								}
								
							}
							
							
							//~ print_r($meta_input);
						}
						
						$row++;
					}
					
					fclose($handle);
				}
				
				
				$redirect_to = admin_url("edit.php?post_type=fd_food");
				wp_safe_redirect( $redirect_to );
				exit();
			} 
			else 
			{
				$errors->add( 'file_upload', __( '<strong>Error</strong>: Please re-upload document.' ) );
				return $errors;
				
			}
		}
	
	}
	
}

add_action('init', 'fd_actions');
	
	$name = "apple";
	$bit = file_get_contents($image);
	$upload = wp_upload_bits( $name, null, $bits, date("Y/m"));
	
?>
