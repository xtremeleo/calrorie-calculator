<?php 

function cc_food_mod($bkfood)
{
	return "<div class='food-plate'>
	
			<div style='position: relative; width: 100px; height: 100px; float: left; border-radius: 20%; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 180%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
		
			<h5 class='sub-title'> ".$bkfood->post_title." 
				<a id='modal".$bkfood->ID."btn' onmouseover=show_tooltips('modal".$bkfood->ID."') data-toggle='collapse' href='#modal-".$bkfood->ID."' role='button' aria-expanded='false' aria-controls='#modal-".$bkfood->ID."' >
					<i class='float-right fa fa-info-circle'></i>
				</a>
				
				<br/><small>".get_post_meta($bkfood->ID,'serving', true ) ." Serving (".get_post_meta($bkfood->ID,'calories', true ) ." Calories ) </small>
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

function cc_food_modx($bkfood, $x)
{
	return "<div class='food-plate'>
	
			<div style='position: relative; width: 100px; height: 100px; float: left; border-radius: 20%; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 180%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
		
			<h5 class='sub-title'> ".$bkfood->post_title." 
				<a id='modal".$bkfood->ID."btn' onmouseover=show_tooltips('modal".$bkfood->ID."') data-toggle='collapse' href='#modal-".$bkfood->ID."' role='button' aria-expanded='false' aria-controls='#modal-".$bkfood->ID."' >
					<i class='float-right fa fa-info-circle'></i>
				</a>
				
				<br/><small>".get_post_meta($bkfood->ID,'serving', true )*$x ." Serving (".get_post_meta($bkfood->ID,'calories', true )*$x ." Calories ) </small>
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

function cc_get_meals($target_calories)
{
	$meals = array();
	$foods = array(
				array("name"=>"Breakfast", "code" => array('BK1', 'BK2')),
				array("name"=>"Lunch", "code" => "LU1"),
				array("name"=>"Dinner", "code" => "DN"),
				//~ array("name"=>"Snack", "code" => "SN"),
			);
	
	foreach($foods as $food)
	{
		if($target_calories < 2000 )
		{
			$fd_args = array('numberposts' => 3, 
						'post_type' => 'fd_food', 
						'orderby' => 'rand',
						'order'   => 'DESC',
						'meta_query' => array(
								'relation' => 'AND',
								array('key' => 'calories','value'   => 200, 'compare' => '<'),
								array('key' => 'slot','value' => $food['code'])
							),
					);
					
				
		}
		else
		{
			$fd_args = array('numberposts' => 3, 
						'post_type' => 'fd_food', 
						'orderby' => 'rand',
						'order'   => 'DESC',
						'meta_query' => array(
											array('key' => 'slot','value' => $food['code'])
										),
					);
					
				
		}
		
		
		$mlfoods = get_posts( $fd_args );
		
		foreach($mlfoods as $mlfood)
		{
			$meals[] = array("section" => (is_array($food['code']) ? "BK" : $food['code'] ), "food" => $mlfood, "calories" =>  get_post_meta($mlfood->ID,'calories', true ), "serve" => get_post_meta($mlfood->ID,'serving', true ) );
		}
	}
	
	return $meals;
	
}

function cc_get_meals_spec($target_calories, $code)
{
	$meals = array();

	if($target_calories < 2000 )
	{
		$fd_args = array('numberposts' => 3, 
					'post_type' => 'fd_food', 
					'orderby' => 'rand',
					'order'   => 'DESC',
					'meta_query' => array(
							'relation' => 'AND',
							array('key' => 'calories','value'   => 200, 'compare' => '<'),
							array('key' => 'slot','value' => $code)
						),
				);
				
			
	}
	else
	{
		$fd_args = array('numberposts' => 3, 
					'post_type' => 'fd_food', 
					'orderby' => 'rand',
					'order'   => 'DESC',
					'meta_query' => array(
										array('key' => 'slot','value' => $code)
									),
				);
				
			
	}
	
	
	$mlfoods = get_posts( $fd_args );
	
	foreach($mlfoods as $mlfood)
	{
		$meals[] = array("section" => (is_array($food['code']) ? "BK" : $food['code'] ), "food" => $mlfood, "calories" =>  get_post_meta($mlfood->ID,'calories', true ), "serve" => get_post_meta($mlfood->ID,'serving', true ) );
	}
	
	return $meals;
}

function cc_count_calories($meals)
{
	$total_calories = array();
	
	foreach($meals as $meal)
	{
		$total_calories[] = $meal['calories'] * $meal['serve'];
	}
	
	return array_sum($total_calories);
}

function cc_check_calories($meals, $target_calories)
{
	for ($i = 0; $i < count($meals); $i++)
	{
		if (cc_count_calories($meals) < $target_calories)
		{
			$meals[$i]['serve'] = $meals[$i]['serve'] + 1;
		}
		else
		{
			return $meals;
		}
		
		
	}
	
}

function qb_api_get_foods( $data ) 
{
	if ($data['check'] === date("dmYydmd") )
	{
		$target_calories = $data['calories'];
		
		$categories = array(
						array("name"=>"Breakfast", "code" => "BK"),
						array("name"=>"Lunch", "code" => "LU1"),
						array("name"=>"Dinner", "code" => "DN"),
						array("name"=>"Snack", "code" => "SN"),
				);
		
		$foods = cc_get_meals($target_calories);
		
		$checked_foods = cc_check_calories($foods, $target_calories);
		
		for ($i = 0; $i < $data['meal']; $i++)
		{
			$bkfood_mod .="<div class='meal-card'> <h5 class='title'>".$categories[$i]['name']." <a onclick=".sprintf("individual_refresh('%s','%u','%s')", $categories[$i]['name']."-food", $slot, $i )." ><small><i class='float-right fas fa-sync'></i></small> </a> </h5>";
			$bkfood_mod .="<div id='".$categories[$i]['name']."-food' >";
			
			foreach($checked_foods as $food)
			{
				if ($food['section'] == $categories[$i]['code'] )
				{
					$bkfood_mod .= cc_food_modx($food['food'], $food['serve']);
					$foods_calories[] = $food['calories'] * $food['serve'];
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
		
		$checked_foods = cc_get_meals_spec($slot, $foods[$data['s']]['code']);
		
		foreach($checked_foods as $food)
		{
			if ($food['section'] == $categories[$i]['code'] )
			{
				$bkfood_mod .= cc_food_modx($food['food'], $food['serve']);
				$foods_calories[] = $food['calories'] * $food['serve'];
			}
			
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
  
  
} );

#http://talkerscode.com/webtricks/upload-image-from-url-using-php.php
?>
