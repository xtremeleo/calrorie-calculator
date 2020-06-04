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
		$slot = $target_calories / 5;
		
		//slot ratio 2:1:2
		$breakfast_slots = $slot*2;
		$lunch_slots = $slot*1;
		$dinner_slots = $slot*2;
		
		$foods = array(
						array("name"=>"Breakfast", "code" => "BK1", "slot" => $breakfast_slots / 2 ),
						array("name"=>"Breakfast 2", "code" => "BK2", "slot" => $breakfast_slots / 2 ),
						array("name"=>"Lunch", "code" => "LU1", "slot" => $lunch_slots ),
						array("name"=>"Dinner", "code" => "DN", "slot" => $dinner_slots ),
				);
		//~ foreach($foods as $food)
		for ($i = 0; $i < $data['meal']; $i++)
		{
			$food = $foods[$i];
		
			$bkfood_mod .="<div> <h5 style='border-bottom: 1px solid #FFA500;'>".$food['name']."</h5>";
			
			$fd_args = array('numberposts' => 2, 
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
														'value'   => $food['slot'],
														'compare' => '<',
													),
												),
							);
			
			$bkfoods = get_posts( $fd_args );
			//$bkfood_mod = array();
			foreach($bkfoods as $bkfood)
			{
				
				$bkfood_mod .= "<div class=''>
								<div style='width: 100px; height: 100px; float: left; background-repeat: no-repeat; background-size: 100%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
								<h5>".$bkfood->post_title."
									<br/><small>".get_post_meta($bkfood->ID,'calories', true )."</small>
								</h5>
								</div>";
				
				//~ [$bkfood->ID]['title'] = ;
				
				//~ foreach($fields as $field )
				//~ {
					//~ $bkfood_mod[$bkfood->ID][$field['name']] = ;
				//~ }
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
		$mealplan = "<h3>Today&apos;Meal Plan</h3><h5>".$target_calories." Calories </h5>".$bkfood_mod;
		
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
  
  //~ register_rest_route( 'question', '/id/(?P<id>\d+)/(?P<check>\d+)', array(
    //~ 'methods' => 'GET',
    //~ 'callback' => 'qb_api_get_answers',
  //~ ) );
  
} );
?>
