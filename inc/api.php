<?php 

function cc_food_mod($bkfood)
{
	return "<div class='food-plate'>
	
			<div style='position: relative; width: 100px; height: 100px; float: left; border-radius: 20%; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 180%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
		
			<h5 class='sub-title'> ".$bkfood->post_title." 
				<a id='modal".$bkfood->ID."btn' onmouseover=show_tooltips('modal".$bkfood->ID."') data-toggle='collapse' href='#modal-".$bkfood->ID."' role='button' aria-expanded='false' aria-controls='#modal-".$bkfood->ID."' >
					<i class='float-right fa fa-info-circle'></i>
				</a>
				
				<br/><small>".get_post_meta($bkfood->ID,'serving', true )*2 ." Serving (".get_post_meta($bkfood->ID,'calories', true )*2 ." Calories ) X2</small>
			</h5>
		
			<div class='collapse food-tip' id='modal".$bkfood->ID."'>
				<div class='col-12'>
					<p style='color: #90EE90;' >Calories: ".get_post_meta($bkfood->ID,'calories', true )."</p>
					<p >CARBS: ".get_post_meta($bkfood->ID,'carbs', true )."</p>
					
					<p >Fats: ".get_post_meta($bkfood->ID,'fats', true )."</p>
					<p >Protein: ".get_post_meta($bkfood->ID,'protein', true )."</p>
					
					<p >Glycemic Score: ".get_post_meta($bkfood->ID,'glycemic', true )."</p>
					<p style='color: #ADD8E6;' >Ingredients: ".get_post_meta($bkfood->ID,'ingredients', true )."</p>
					<p style='color: #52E552;' >Prep Time: ".get_post_meta($bkfood->ID,'prep', true )."</p>
				</div>
			</div>
	</div>";
}

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
				);
		$foods_calories = array();
		//~ foreach($foods as $food)
		for ($i = 0; $i < $data['meal']; $i++)
		{
			//$nos_plate = $slot / 300;
			$food = $foods[$i];
			$code = (is_array($food['code']) ? $food['code'][0] : $food['code']);
			
			$bkfood_mod .="<div class='meal-card'> <h5 class='title'>".$food['name']." <a onclick=".sprintf("individual_refresh('%s','%u','%s')", $food['name']."-food", $slot, $i )." ><small><i class='float-right fas fa-sync'></i></small> </a> </h5>";
			$bkfood_mod .="<div id='".$food['name']."-food' >";
			
			$fd_args = array('numberposts' => 3, 
							'post_type' => 'fd_food', 
							'orderby' => 'rand',
							'order'   => 'DESC',
							'meta_query' => array(
													array(
														'key'     => 'slot',
														'value'   => $food['code'],
													)
												),
							);
			
			$bkfoods = get_posts( $fd_args );
			$called_calories = array();
			
			foreach($bkfoods as $bkfood)
			{
				$called_calories[] = get_post_meta($bkfood->ID,'calories', true );
				
				if ($slot > array_sum($called_calories) )
				{
					$bkfood_mod .= cc_food_mod($bkfood);
					
					$foods_calories[] = get_post_meta($bkfood->ID,'calories', true )*2;
				}
				
				
			}
		
			$bkfood_mod .="</div> </div>";
		}
		
		$mealplan = "<div class='title-bar'><h3 class='title'>Today&apos;s Meal Plan</h3> <h5 class='sub-title'>".$target_calories." Target Calories | ".array_sum($foods_calories)." Calories <a onclick=processFORM() ><i class='fas fa-sync'></i></a></h5></div>".$bkfood_mod;
		
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
		$slot = $data['n'];
		
		$foods = array(
						array("name"=>"Breakfast", "code" => array('BK1', 'BK2')),
						array("name"=>"Lunch", "code" => "LU1"),
						array("name"=>"Dinner", "code" => "DN"),
						array("name"=>"Snack", "code" => "SN"),
				);
		
		$fd_args = array('numberposts' => -1, 
						'post_type' => 'fd_food', 
						'orderby' => 'rand',
						'order'   => 'DESC',
						'meta_query' => array(
												array(
													'key'     => 'slot',
													'value'   => $foods[$data['s']]['code'],
												)
											),
						);
		
		$bkfoods = get_posts( $fd_args );
		$called_calories = array();
		
		foreach($bkfoods as $bkfood)
		{
			$called_calories[] = get_post_meta($bkfood->ID,'calories', true );
			
			if ($slot > array_sum($called_calories) )
			{
				$bkfood_mod .= "<div class='food-plate'>
								<div style='position: relative; width: 100px; height: 100px; float: left; border-radius: 20%; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 180%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
								
									<h5 class='sub-title'> ".$bkfood->post_title." 
										<a id='modal".$bkfood->ID."btn' onmouseover=show_tooltips('modal".$bkfood->ID."') data-toggle='collapse' href='#modal-".$bkfood->ID."' role='button' aria-expanded='false' aria-controls='#modal-".$bkfood->ID."' >
											<i class='float-right fa fa-info-circle'></i>
										</a>
										
										<br/><small>".get_post_meta($bkfood->ID,'serving', true )*2 ." Serving (".get_post_meta($bkfood->ID,'calories', true )*2 ." Calories ) X2</small>
									</h5>
								
									<div class='collapse food-tip' id='modal".$bkfood->ID."'>
										<div class='col-12'>
											<p style='color: #90EE90;' >Calories: ".get_post_meta($bkfood->ID,'calories', true )."</p>
											<p >CARBS: ".get_post_meta($bkfood->ID,'carbs', true )."</p>
											
											<p >Fats: ".get_post_meta($bkfood->ID,'fats', true )."</p>
											<p >Protein: ".get_post_meta($bkfood->ID,'protein', true )."</p>
											
											<p >Glycemic Score: ".get_post_meta($bkfood->ID,'glycemic', true )."</p>
											<p style='color: #ADD8E6;' >Ingredients: ".get_post_meta($bkfood->ID,'ingredients', true )."</p>
											<p style='color: #52E552;' >Prep Time: ".get_post_meta($bkfood->ID,'prep', true )."</p>
										</div>
									</div>
									
								
								
						
								
								</div>";
							
							$foods_calories[] = get_post_meta($bkfood->ID,'calories', true );
			}
			
			
			
			//~ [$bkfood->ID]['title'] = ;
			
			//~ foreach($fields as $field )
			//~ {
				//~ $bkfood_mod[$bkfood->ID][$field['name']] = ;
			//~ }
			
		}
	
		$bkfood_mod .="</div>";
		
		return $bkfood_mod;
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
  
  register_rest_route( 'foodspec', 'check/(?P<check>\d+)/n/(?P<n>\d+)/s/(?P<s>\d+)/', array(
    'methods' => 'GET',
    'callback' => 'qb_api_get_food_spec',
  ) );
  
  //~ register_rest_route( 'question', '/id/(?P<id>\d+)/(?P<check>\d+)', array(
    //~ 'methods' => 'GET',
    //~ 'callback' => 'qb_api_get_answers',
  //~ ) );
  
} );

#http://talkerscode.com/webtricks/upload-image-from-url-using-php.php
?>
