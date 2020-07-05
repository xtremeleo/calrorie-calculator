<?php
function fd_actions()
{	
	
	
	if (!empty($_POST['action']))
	{
		global $errors;
		$errors = new WP_Error();
		$success = null;
		
		$action = $_POST['action'];
		
		if ($action == "fd_import")
		{
			$foods = array();
			//$meta_input = array();
			$fields = array(
							array('name'=>'image','value' => 'image'),
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
								elseif($c == 1)
								{
									
									$path = $data[$c];
									$upload = wp_upload_bits($data[0].".png", null,  file_get_contents($path));
									
									$wp_upload_dir = wp_upload_dir();
									
									$attachment = array(
															'guid'           => $upload['url'], 
															'post_mime_type' => $upload['type'],
															'post_title'     => $data[0],
															'post_content'   => '',
															'post_status'    => 'inherit'
														);
														
									// Insert the attachment.
									$attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );
									 
									// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
									require_once( ABSPATH . 'wp-admin/includes/image.php' );
									
									// Generate the metadata for the attachment, and update the database record.
									$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
									wp_update_attachment_metadata( $attach_id, $attach_data );
									 
									set_post_thumbnail( $post_id, $attach_id );

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
		
		if($action == "signup_subscripter")
		{
			$user_name = sanitize_text_field($_POST['userName']);
			$user_login = sanitize_text_field($_POST['userEmail']);
			$user_pass = wp_generate_password( 12, false );
			$user_email = sanitize_email($_POST['userEmail']);
			$sanitized_user_login = sanitize_user( $user_login );
			
			if ( '' == $sanitized_user_login ) 
			{
				$errors->add( 'empty_username', __( '<strong>Error</strong>: Please enter a valid Email Address.' ) );
			}
			elseif ( username_exists( $sanitized_user_login ) ) 
			{
				$errors->add( 'username_exists', __( '<strong>Error</strong>: This Email Address is already registered. Please choose another one.' ) );
		 
			}
			else 
			{
				/** This filter is documented in wp-includes/user.php */
				$illegal_user_logins = (array) apply_filters( 'illegal_user_logins', array() );
				if ( in_array( strtolower( $sanitized_user_login ), array_map( 'strtolower', $illegal_user_logins ), true ) ) {
					$errors->add( 'invalid_username', __( '<strong>Error</strong>: Sorry, that username is not allowed.' ) );
				}
			}
			
			// Check the email address.
			if ( '' == $user_email ) 
			{
				$errors->add( 'empty_email', __( '<strong>Error</strong>: Please type your email address.' ) );
				
			} 
			elseif ( ! is_email( $user_email ) ) 
			{
				$errors->add( 'invalid_email', __( '<strong>Error</strong>: The email address isn&#8217;t correct.' ) );
				$user_email = '';
			} 
			elseif ( email_exists( $user_email ) ) 
			{
				$errors->add( 'email_exists', __( '<strong>Error</strong>: This email is already registered, please choose another one.' ) );
			}
			
			//Checking for error
			if ( $errors->has_errors() ) 
			{
				return $errors;
			}
			else
			{
				$user_id   = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
				
				if ( ! $user_id || is_wp_error( $user_id ) ) 
				{
					$errors->add('registerfail', __( '<strong>Error</strong>: Registration failed' ) );
					return $errors;
				}
				else
				{
					$errors = null;
					
					$userdata = array('ID'=> $user_id,'nickname' => $user_name );
					$update_user_id = wp_update_user( $userdata );
					
					$success = "You have successfully registered";
					return $success;
				}
				
				
			}
			
			
			
		}
	}
	
}

add_action('init', 'fd_actions');

?>
