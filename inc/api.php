<?php 
function qb_api_get_foods( $data ) 
{
	if ($data['check'] === date("dmYydmd") )
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
				);
				
		$target_calories = $data['calories'];
		
		//divide calories into slot
		$slot = $target_calories / $data['meal'];
		
		//slot ratio 2:1:2
		//~ $breakfast_slots = $slot*2;
		//~ $lunch_slots = $slot*1;
		//~ $dinner_slots = $slot*2;
		
		$foods = array(
						array("name"=>"Breakfast", "code" => array('BK1', 'BK2')),
						array("name"=>"Lunch", "code" => "LU1"),
						array("name"=>"Dinner", "code" => "DN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
				);
		$foods_calories = array();
		//~ foreach($foods as $food)
		for ($i = 0; $i < $data['meal']; $i++)
		{
			$food = $foods[$i];
		
			$bkfood_mod .="<div class='meal-card'> <h5 class='title'>".$food['name']."</h5>";
			
			$fd_args = array('numberposts' => 1, 
							'post_type' => 'fd_food', 
							'orderby' => 'rand',
							'order'   => 'DESC',
							'meta_query' => array(
													'relation' => 'AND',
													array(
														'key'     => 'slot',
														'value'   => $food['code'],
													),
													array(
														'key'     => 'calories',
														'value'   => $slot,
														'compare' => '<',
													),
												),
							);
			
			$bkfoods = get_posts( $fd_args );
			//$bkfood_mod = array();
			foreach($bkfoods as $bkfood)
			{
				
				$bkfood_mod .= "<div class=''>
								<div style='width: 100px; height: 100px; float: left; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 130%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
								<a type='button' data-toggle='modal' data-target='#modal-".$bkfood->ID."' >
									<h5 class='sub-title'>".$bkfood->post_title."
										<br/><small>".get_post_meta($bkfood->ID,'serving', true )." Serving (".get_post_meta($bkfood->ID,'calories', true )." Calories )</small>
									</h5>
									<i class='fa fa-info-circle'></i>
								</a>
								
								<!-- Modal -->
								<div class='modal fade' id='modal-".$bkfood->ID."' tabindex='-1' role='dialog' aria-labelledby='#modal-".$bkfood->ID."Label' aria-hidden='true'>
									<div class='modal-dialog modal-md'>
										<div class='modal-content'>
											<div class='modal-header'>
												<h5 class='modal-title' id='modal-".$bkfood->ID."Label'>".$bkfood->post_title."</h5>
												<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>
											
											<div class='modal-body'>
												<div class='row'>
													<div class='col-6'>
														<p>Calories: ".get_post_meta($bkfood->ID,'calories', true )."</p>
														<p>CARBS: ".get_post_meta($bkfood->ID,'carbs', true )."</p>
													</div>
													
													<div class='col-6'>
														<p>Fats: ".get_post_meta($bkfood->ID,'fats', true )."</p>
														<p>Protein: ".get_post_meta($bkfood->ID,'protein', true )."</p>
													</div>
													
													<div class='col-12'>
														<p>Glycemic Score: ".get_post_meta($bkfood->ID,'glycemic', true )."</p>
														<p>Ingredients: ".get_post_meta($bkfood->ID,'ingredients', true )."</p>
														<p>Prep Time: ".get_post_meta($bkfood->ID,'prep', true )."</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
						
								
								</div>";
				
				//~ [$bkfood->ID]['title'] = ;
				
				//~ foreach($fields as $field )
				//~ {
					//~ $bkfood_mod[$bkfood->ID][$field['name']] = ;
				//~ }
				
				$foods_calories[] = get_post_meta($bkfood->ID,'calories', true );
			}
		
			$bkfood_mod .="</div>";
		}
		
		
		
		
		//~ $lr_args = array('numberposts' => -1, 
							//~ 'post_type' => 'fd_food', 
							//~ 'orderby' => 'ID',
							//~ 'order'   => 'DESC',
							//~ 'meta_query' => array(
								//~ array(
									//~ 'key'     => 'slot',
									//~ 'value'   => array( 3, 4 ),
									//~ 'compare' => 'IN',
								//~ ),
							//~ );
		
		//~ $loanrequests = new WP_Query( $lr_args );
		
		//$answers = get_post_meta( , 'qb_answers', true );
		
		//~ $mealplan = "<h3>Today&apos;Meal Plan</h3><h5>".$target_calories." Calories </h5><h4>".$data['meal']."</h4>".$bkfood_mod;
		$mealplan = "<div class='title-bar'><h3 class='title'>Today&apos;s Meal Plan</h3> <h5 class='sub-title'>".$target_calories." Target Calories | ".array_sum($foods_calories)." Calories </h5></div>".$bkfood_mod;
		
		return $mealplan;
		
	}
	else
	{
		return "wrong code";
	}
	
	
  
}

function qb_api_get_food_spec( $data ) 
{
	if ($data['check'] === date("dmYydmd") )
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
				);
				
		$target_calories = $data['calories'];
		
		//divide calories into slot
		$slot = $target_calories / $data['meal'];
		
		//slot ratio 2:1:2
		//~ $breakfast_slots = $slot*2;
		//~ $lunch_slots = $slot*1;
		//~ $dinner_slots = $slot*2;
		
		$foods = array(
						array("name"=>"Breakfast", "code" => array('BK1', 'BK2')),
						array("name"=>"Lunch", "code" => "LU1"),
						array("name"=>"Dinner", "code" => "DN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
						array("name"=>"Snack", "code" => "SN"),
				);
		$foods_calories = array();
		//~ foreach($foods as $food)
		for ($i = 0; $i < $data['meal']; $i++)
		{
			$food = $foods[$i];
		
			$bkfood_mod .="<div> <h5 style='border-bottom: 1px solid #FFA500;'>".$food['name']."</h5>";
			
			$fd_args = array('numberposts' => 1, 
							'post_type' => 'fd_food', 
							'orderby' => 'rand',
							'order'   => 'DESC',
							'meta_query' => array(
													'relation' => 'AND',
													array(
														'key'     => 'slot',
														'value'   => $food['code'],
													),
													array(
														'key'     => 'calories',
														'value'   => $slot,
														'compare' => '<',
													),
												),
							);
			
			$bkfoods = get_posts( $fd_args );
			//$bkfood_mod = array();
			foreach($bkfoods as $bkfood)
			{
				
				$bkfood_mod .= "<div class=''>
								<div style='width: 100px; height: 100px; float: left; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 130%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
								<a type='button' >
									<h5>".$bkfood->post_title."
										<br/><small>".get_post_meta($bkfood->ID,'calories', true )." Calories</small>
										<br/><small>".get_post_meta($bkfood->ID,'serving', true )." Serving</small>
										
									</h5>
								</a>
								</div>";
				
				//~ [$bkfood->ID]['title'] = ;
				
				//~ foreach($fields as $field )
				//~ {
					//~ $bkfood_mod[$bkfood->ID][$field['name']] = ;
				//~ }
				
				$foods_calories[] = get_post_meta($bkfood->ID,'calories', true );
			}
		
			$bkfood_mod .="</div>";
		}
		
		
		
		
		//~ $lr_args = array('numberposts' => -1, 
							//~ 'post_type' => 'fd_food', 
							//~ 'orderby' => 'ID',
							//~ 'order'   => 'DESC',
							//~ 'meta_query' => array(
								//~ array(
									//~ 'key'     => 'slot',
									//~ 'value'   => array( 3, 4 ),
									//~ 'compare' => 'IN',
								//~ ),
							//~ );
		
		//~ $loanrequests = new WP_Query( $lr_args );
		
		//$answers = get_post_meta( , 'qb_answers', true );
		
		//~ $mealplan = "<h3>Today&apos;Meal Plan</h3><h5>".$target_calories." Calories </h5><h4>".$data['meal']."</h4>".$bkfood_mod;
		$mealplan = "<h3>Today&apos;s Meal Plan</h3><h5>".$target_calories." Target Calories | ".array_sum($foods_calories)." Calories </h5>".$bkfood_mod;
		
		return $mealplan;
		
	}
	else
	{
		return "wrong code";
	}
	
	
  
}

add_action( 'rest_api_init', function () {
  
  register_rest_route( 'food', 'check/(?P<check>\d+)/calories/(?P<calories>\d+)/meal/(?P<meal>\d+)/', array(
    'methods' => 'GET',
    'callback' => 'qb_api_get_foods',
  ) );
  
  register_rest_route( 'food_spec', 'check/(?P<check>\d+)/calories/(?P<calories>\d+)/meal/(?P<meal>\d+)/', array(
    'methods' => 'GET',
    'callback' => 'qb_api_get_food_spec',
  ) );
  
  //~ register_rest_route( 'question', '/id/(?P<id>\d+)/(?P<check>\d+)', array(
    //~ 'methods' => 'GET',
    //~ 'callback' => 'qb_api_get_answers',
  //~ ) );
  
} );
?>
