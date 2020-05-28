<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
Plugin Name: Calrorie Calculator
Plugin URI: 
Description: 
Author: Ikechukwu Mbilitem [xtremeleo]
Version: 1.0
Author URI: www.linkedin.com/in/xtremeleo/

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

function caca_scripts() {
	$ver = '1.1';
	wp_enqueue_style( 'style', "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css", null, $ver );
	wp_enqueue_style( 'style', plugin_dir_url( __FILE__ )."assets/css/main.css", null, $ver );
	wp_enqueue_script( 'jqueryfull', 'https://code.jquery.com/jquery-3.5.1.slim.min.js', array(), false );
	wp_enqueue_script( 'popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js', array(), false );
	wp_enqueue_script( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', array(), false );

	//wp_enqueue_script( 'polyfill', 'https://polyfill.io/v3/polyfill.min.js?features=Promise%2CArray.prototype.filter%2CMap%2CArray.prototype.find%2Cdocument.querySelector%2CArray.prototype.forEach%2CArray.prototype.includes', array(), true );
	
	//wp_enqueue_script( 'scripts', get_template_directory_uri() . '/assets/js/script.js', array('jqueryfull', 'TweenMax', 'ScrollToPlugin', 'barbaUmd', 'polyfill', 'urlsearchparam'), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'caca_scripts' );

function caca_front_form()
{
	?>
	<div style="width: 100%;">
			<div style="width: 100%; float: left; background-color: #FFF;">
				<div class="" style="width: 30%; float: left;" >
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/img/disk.png" width="100%" />
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
							<p> 
								I want to eat <input type="number" name="calories" style="width: 200px; padding: 5px; border: 1px solid grey;" placeholder="#####" /> Calories  
								| <a href="#"  data-toggle="modal" data-target="#exampleModal" >not sure, use <img src="<?php echo plugin_dir_url( __FILE__ );?>assets/img/calculator.png" width="30px" /></a>
							</p>
							<p> in  <select style="width: 200px; padding: 5px; border: 1px solid grey;" >
										<option value="1">1 meal</option>
										<option value="2">2 meals</option>
										<option value="3">3 meals</option>
										<option value="4" selected="">4 meals</option>
										<option value="5">5 meals</option>
										<option value="6">6 meals</option>
										<option value="7">7 meals</option>
										<option value="8">8 meals</option>
										<option value="9">9 meals</option>
									</select>
							</p>
							<button style="padding: 10px; background-color: #1E90FF; border: 1px solid grey;"  onclick="processFORM()">Generate</button>
							
							<!-- Modal -->
							<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							  <div class="modal-dialog modal-xl">
								<div class="modal-content">
								  <div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
									</button>
								  </div>
								  <div class="modal-body">
									...
								  </div>
								  <div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								  </div>
								</div>
							  </div>
							</div>
						</div>
					</form>
				</div>
			
			</div>
			
			<div style=" width: 100%; float: left;">
				<div style="width: 80%; margin: 2px auto;">
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
					</style>
					
					<div id="showLoader" style="margin-top: 138px; display: none; padding: 32px;">
						<div id="loader" ></div>
					</div>
					
					<div id="message" style="margin-top: 13px; display: none; padding: 32px;">
						<h3>Result</h3>
						<p>work.....</p>
					</div>
					
					<script>
						function processFORM()
						{
							
							event.preventDefault();
							//~ document.getElementById("transform").style.display = "none";
							document.getElementById("showLoader").style.display = "block";
							
							var myVar = setTimeout(showPage, 4000);
							
						}
						
						function showPage() {
						  document.getElementById("showLoader").style.display = "none";
						  document.getElementById("message").style.display = "block";
						  
						  var myVar2 = setTimeout(transformsubmit, 2000);
						  
						}
						
						function transformsubmit() {
						  document.getElementById("transform").submit();
						  
						}
						
						
					</script>
				</div>
			</div>
		</div>
	<?php
}
add_shortcode( 'caca_main_form', 'caca_front_form');
