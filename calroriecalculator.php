<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
Plugin Name: Calrorie Calculator
Plugin URI: 
Description: 
Author: Hashtag Solutions
Version: 1.0
Author URI: #

instructions 3.3. Order Management:

1. Automatically restrict users from shopping on the site for 30 days after placing an order.

2. Automatically re-enable users to shop if item requested isn’t collected/claimed after

forty eight (48) hours.

3. Automated SMS and email notification to users for orders.

4. Checkout system restricting order to same email and username registered.

5. Special Offers will have a different timeline restriction to only forty (48) hours rather than the general limit of thirty (30) days.

*/


define( 'CACA__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( CACA__PLUGIN_DIR . 'inc/foods.php' );
require_once( CACA__PLUGIN_DIR . 'inc/actions.php' );
require_once( CACA__PLUGIN_DIR . 'inc/api.php' );

function caca_scripts() {
	$ver = '1.1';
	wp_enqueue_style( 'style', "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css", null, $ver );
	wp_enqueue_style( 'style', plugin_dir_url( __FILE__ )."assets/css/main.css", null, $ver );
	wp_enqueue_script( 'jqueryfull', 'https://code.jquery.com/jquery-3.5.1.slim.min.js', array(), false );
	wp_enqueue_script( 'popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js', array(), false );
	wp_enqueue_script( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', array(), false );
	wp_enqueue_script( 'fontawesome', 'https://kit.fontawesome.com/2b817816c7.js', array(), false );
	wp_enqueue_script( 'custom', plugin_dir_url( __FILE__ )."assets/js/main.js", array(), false );

	//wp_enqueue_script( 'polyfill', 'https://polyfill.io/v3/polyfill.min.js?features=Promise%2CArray.prototype.filter%2CMap%2CArray.prototype.find%2Cdocument.querySelector%2CArray.prototype.forEach%2CArray.prototype.includes', array(), true );
	
	//wp_enqueue_script( 'scripts', get_template_directory_uri() . '/assets/js/script.js', array('jqueryfull', 'TweenMax', 'ScrollToPlugin', 'barbaUmd', 'polyfill', 'urlsearchparam'), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'caca_scripts' );

function caca_front_form()
{
	?>
	<div style="width: 100%;">
			
			<div style="width: 100%; float: left; background-color: #FFF;">
				<div class="" style="width: 100%; float: left;" >
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/img/home.png" width="100%" />
					<br/>
					<div id="form_alert" class="d-none" role='alert'>
						<strong>Hold on!</strong> Please note that you only specify a minimum of 1000 calories.
					</div>
				</div>
				
				<div class="" style="width: 30%; float: left;" >
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/img/disk.png" width="90%" />
				</div>
				
				<div class="" style="width: 70%; float: left;" >
<!--
					<div class="" style="width: 100%; margin: 2px auto;" >
						<div style="width: 100px; height: 100px; float: left; padding: 5px; margin: 5px; border-radius: 10px; background-color: grey;"></div>
						<div style="width: 100px; height: 100px; float: left; padding: 5px; margin: 5px; border-radius: 10px; background-color: grey;"></div>
						<div style="width: 100px; height: 100px; float: left; padding: 5px; margin: 5px; border-radius: 10px; background-color: grey;"></div>
						<div style="width: 100px; height: 100px; float: left; padding: 5px; margin: 5px; border-radius: 10px; background-color: grey;"></div>
						<div style="width: 100px; height: 100px; float: left; padding: 5px; margin: 5px; border-radius: 10px; background-color: grey;"></div>
						<div style="width: 100px; height: 100px; float: left; padding: 5px; margin: 5px; border-radius: 10px; background-color: grey;"></div>
					</div>
-->
					
					<form class="form">
						
						
						<div style="padding: 10px;">
							<p></p>
							<input type="number" id="calories" name="calories" style="width: 100%;  display: block; padding: 10px; border: 1px solid grey;" placeholder="I want to eat ### calories" min="1000" />   
<!--
								| <a href="#"  data-toggle="modal" data-target="#exampleModal" >not sure, use <img src="<?php echo plugin_dir_url( __FILE__ );?>assets/img/calculator.png" width="30px" /></a>
-->
							<input type="hidden" id="meal" name="meal" style="width: 100%; display: block; padding: 5px; margin-top: 2px; border: 1px solid grey;" value="3" />
					
							<button style="padding: 10px;  margin-top: 2px; background-color: #50A6FA; border: 1px solid grey; border-radius: 10px;"  onclick="processFORM()">Generate</button>
							
							
						</div>
					</form>
				</div>
			
			</div>
			
			
			
			<div style=" width: 100%; float: left; background-color: #ffffff;">
				<div style="width: 90%; margin: 2px auto;">
					<style>
						/* Center the loader */
						#loader {
						  position: absolute;
						  left: 50%;
						  top: 50%;
						  z-index: 1;
						  width: 150px;
						  height: 150px;
						  margin: -75px 0 0 -75px;
						  border: 16px solid #f3f3f3;
						  border-radius: 50%;
						  border-top: 16px solid #3498db;
						  width: 120px;
						  height: 120px;
						  -webkit-animation: spin 2s linear infinite;
						  animation: spin 2s linear infinite;
						}

						@-webkit-keyframes spin {
						  0% { -webkit-transform: rotate(0deg); }
						  100% { -webkit-transform: rotate(360deg); }
						}

						@keyframes spin {
						  0% { transform: rotate(0deg); }
						  100% { transform: rotate(360deg); }
						}

						/* Add animation to "page content" */
						.animate-bottom {
						  position: relative;
						  -webkit-animation-name: animatebottom;
						  -webkit-animation-duration: 1s;
						  animation-name: animatebottom;
						  animation-duration: 1s
						}

						@-webkit-keyframes animatebottom {
						  from { bottom:-100px; opacity:0 } 
						  to { bottom:0px; opacity:1 }
						}

						@keyframes animatebottom { 
						  from{ bottom:-100px; opacity:0 } 
						  to{ bottom:0; opacity:1 }
						}

						#myDiv {
						  display: none;
						  text-align: center;
						}
						
						.title{ color: #4D4D4D; font-size: 24px;font-weight: 200; display: inline-block; margin: 0px!important;}
						
						.sub-title{ color: #4D4D4D; font-size: 14px;font-weight: bold; display: inline-block; margin: 0px!important;}
						
						.title-bar {width: 100%; padding: 5px 0px; margin: 2px;}
						
						.meal-card {width: 100%; float: left;padding: 5px; background: #FFFFFF; box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.12); border-radius: 5px; transition: box-shadow 0.15s ease-in-out; margin: 10px 0px;}
						
						.meal-card:hover { box-shadow: 0 6px 14px 0 rgba(0, 0, 0, 0.22); z-index: 10;}
						
						.meal-card .title{ color: #4D4D4D; font-size: 20px;font-weight: 200; display: block; margin: 0px!important;}
						
						.meal-card .sub-title{ color: #4D4D4D; font-size: 14px;font-weight: bold; display: block; margin: 0px!important;}
						
						.food-tip {}
						
						.food-tip { width: 100%; float: left; margin-top: 2%; letter-spacing: 0.3px; font-size: 1.8rem; background-color: #1E1717; z-index: 1;} 
						
						.food-tip h5 { margin: 0px; color: #FFA500;} 
						.food-tip p { margin: 0px; color: #FFA500;} 
						
						.food-plate {width: 100%; float: left; margin: 12px 0px; }
						
					</style>
					
					<div id="showLoader" style="margin-top: 138px; display: none; padding: 32px;">
						<div id="loader" ></div>
					</div>
					
					<div id="message" style="margin-top: 13px; display: none; padding: 15px;">
						<h3>Result</h3>
						<p>work.....</p>
					</div>
					
					<script>
						function processFORM()
						{
							
							event.preventDefault();
							//~ document.getElementById("transform").style.display = "none";
							document.getElementById("showLoader").style.display = "block";
							showPage();
							
							//~ var myVar = setTimeout(showPage, 4000);
							
						}
						
						function showPage() 
						{
							var calories = document.getElementById("calories").value;
							
							if (calories >= 1000 )
							{
								var meal = document.getElementById("meal").value;
								var xhttp = new XMLHttpRequest();
								
								xhttp.onreadystatechange = function() 
								{
									if (this.readyState == 4 && this.status == 200) 
									{

										document.getElementById("message").innerHTML = JSON.parse(this.responseText);
										document.getElementById("showLoader").style.display = "none";
										document.getElementById("message").style.display = "block";
										
									}
								};
								
								xhttp.open("GET", "<?php echo site_url("wp-json/food/check/".date("dmYydmd"));?>/calories/" + calories + "/meal/" + meal, true);
								xhttp.send();
							}
							else
							{
								document.getElementById("form_alert").setAttribute("class", "alert alert-warning alert-dismissible fade show");
								document.getElementById("showLoader").style.display = "none";
								document.getElementById("message").style.display = "none";
								
								setTimeout(form_alert_close, 10000);
							}
							
							
  
						  //~ var myVar2 = setTimeout(transformsubmit, 2000);
						  
						}
						
						function individual_refresh(id, n, s) 
						{
							
							var xhttp = new XMLHttpRequest();
							
							xhttp.onreadystatechange = function() 
							{
								if (this.readyState == 4 && this.status == 200) 
								{

									document.getElementById(id).innerHTML = JSON.parse(this.responseText);
									
								}
							};
							
							xhttp.open("GET", "<?php echo site_url("wp-json/foodspec/check/".date("dmYydmd"));?>/n/" + n + "/s/" + s, true);
							xhttp.send();
							
							
						  //~ var myVar2 = setTimeout(transformsubmit, 2000);
						  
						}
						
						function transformsubmit() 
						{
						  document.getElementById("transform").submit();
						}
						
						function form_alert_close()
						{
							document.getElementById("form_alert").setAttribute("class", "d-none")
						}
						
						function show_tooltips(id)
						{
							document.getElementById(id).style.display='block';
							
							document.getElementById(id+"btn").onmouseout = function(){
								document.getElementById(id).style.display='none';
							}
							
						}
						
					</script>
				</div>
			</div>
		</div>
	<?php
}
add_shortcode( 'caca_main_form', 'caca_front_form');
