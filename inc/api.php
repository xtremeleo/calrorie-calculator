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
				);
		$foods_calories = array();
		//~ foreach($foods as $food)
		for ($i = 0; $i < $data['meal']; $i++)
		{
			//$nos_plate = $slot / 300;
			$food = $foods[$i];
			$code = (is_array($food['code']) ? $food['code'][0] : $food['code']);
			
			$bkfood_mod .="<div class='meal-card'> <h5 class='title'>".$food['name']." <a onclick=".sprintf("individual_refresh('%s','%u','%s')", $food['name']."-food", $slot, $code )." ><small><i class='float-right fas fa-sync'></i></small> </a> </h5>";
			
			$fd_args = array('numberposts' => -1, 
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
				
				if (array_sum($called_calories) > $slot )
				{
					
				}
				else
				{
					$bkfood_mod .= "<div id='".$food['name']."-food' class='food-plate'>
								<div style='width: 100px; height: 100px; float: left; border-radius: 20%; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 180%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
								
									<h5 class='sub-title'> ".$bkfood->post_title." 
										<a id='modal".$bkfood->ID."btn' onmouseover=$('#modal-".$bkfood->ID."').modal('show') type='button' data-toggle='modal' data-target='#modal-".$bkfood->ID."' >
											<i class='float-right fa fa-info-circle'></i>
										</a>
										
										<br/><small>".get_post_meta($bkfood->ID,'serving', true )." Serving (".get_post_meta($bkfood->ID,'calories', true )." Calories )</small>
									</h5>
								
								<!-- Modal -->
								<div class='modal fade' id='modal-".$bkfood->ID."' tabindex='-1' role='dialog' aria-labelledby='#modal-".$bkfood->ID."Label' aria-hidden='true'>
									<div class='modal-dialog modal-md food-tip'>
										<div class='modal-content float'>
											<div class='modal-header'>
												<h5 class='modal-title' id='modal-".$bkfood->ID."Label'>".$bkfood->post_title."</h5>
												
											</div>
											
											<div class='modal-body'>
												<div class='row'>
													<div class='col-12'>
												<p style='color: #90EE90;' >Calories: ".get_post_meta($bkfood->ID,'calories', true )."</p>
												<p style='' >CARBS: ".get_post_meta($bkfood->ID,'carbs', true )."</p>
												
												<p style='' >Fats: ".get_post_meta($bkfood->ID,'fats', true )."</p>
												<p style='' >Protein: ".get_post_meta($bkfood->ID,'protein', true )."</p>
												
												<p style='' >Glycemic Score: ".get_post_meta($bkfood->ID,'glycemic', true )."</p>
												<p style='color: #9D0303;' >Ingredients: ".get_post_meta($bkfood->ID,'ingredients', true )."</p>
												<p style='color: #52E552;' >Prep Time: ".get_post_meta($bkfood->ID,'prep', true )."</p>
											</div>
												</div>
											</div>
										</div>
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
		$fd_args = array('numberposts' => 1, 
						'post_type' => 'fd_food', 
						'orderby' => 'rand',
						'order'   => 'DESC',
						'meta_query' => array(
												'relation' => 'AND',
												array(
													'key'     => 'slot',
													'value'   => $data['s'],
												),
												array(
													'key'     => 'calories',
													'value'   => $data['n'],
													'compare' => '<',
												),
											),
						);
		
		$bkfoods = get_posts( $fd_args );
		//$bkfood_mod = array();
		foreach($bkfoods as $bkfood)
		{
			
			$bkfood_mod .= "<div id='".$food['name']."-food' class=''>
							<div style='width: 100px; height: 100px; float: left; margin-right: 10px; background-color: #BFBFBF; background-repeat: no-repeat; background-size: 180%; background-image: url(".get_the_post_thumbnail_url($bkfood->ID, '200' ).");'></div>
							<a type='button' data-toggle='modal' data-target='#modal-".$bkfood->ID."' >
								<h5 class='sub-title'> ".$bkfood->post_title." <a type='button' data-toggle='modal' data-target='#modal-".$bkfood->ID."' ><i class='float-right fa fa-info-circle'></i></a>
									<br/><small>".get_post_meta($bkfood->ID,'serving', true )." Serving (".get_post_meta($bkfood->ID,'calories', true )." Calories )</small>
								</h5>
							</a>
							
							<!-- Modal -->
							<div class='modal fade' id='modal-".$bkfood->ID."' tabindex='-1' role='dialog' aria-labelledby='#modal-".$bkfood->ID."Label' aria-hidden='true'>
								<div class='modal-dialog modal-md food-tip'>
									<div class='modal-content float'>
										<div class='modal-header'>
											<h5 class='modal-title' id='modal-".$bkfood->ID."Label'>".$bkfood->post_title."</h5>
											
										</div>
										
										<div class='modal-body'>
											<div class='row'>
												<div class='col-12'>
											<p style='color: #90EE90;' >Calories: ".get_post_meta($bkfood->ID,'calories', true )."</p>
											<p style='' >CARBS: ".get_post_meta($bkfood->ID,'carbs', true )."</p>
											
											<p style='' >Fats: ".get_post_meta($bkfood->ID,'fats', true )."</p>
											<p style='' >Protein: ".get_post_meta($bkfood->ID,'protein', true )."</p>
											
											<p style='' >Glycemic Score: ".get_post_meta($bkfood->ID,'glycemic', true )."</p>
											<p style='color: #9D0303;' >Ingredients: ".get_post_meta($bkfood->ID,'ingredients', true )."</p>
											<p style='color: #52E552;' >Prep Time: ".get_post_meta($bkfood->ID,'prep', true )."</p>
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
  
  register_rest_route( 'food_spec', 'check/(?P<check>\d+)/n/(?P<n>\d+)/s/(?P<s>\d+)/', array(
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
