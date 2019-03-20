<?php

/*
Plugin Name: DevIt_test
Plugin URI: devit.cloudaccess.host
Description: плагин расширяющий профиль пользователя дополнительными метаполями ['адрес', 'телефон', 'пол', 'семейный статус']. Данные должны хранится зашифрованными в БД для шифрования необходимо использовать публичный и приватный ключи RSA. На фронтенде вывести список пользователей и возможность перейти в профиль пользователя для просмотра  инфо должно быть получено из бд и расшифровано.
Version: 0.1
Author: Kirill Potapenkov	
Author URI: devit.cloudaccess.host
*/


include ("encr.php");


add_action('show_user_profile', 'potapenkov_user_profile_edit_action');
add_action('edit_user_profile', 'potapenkov_user_profile_edit_action');
function potapenkov_user_profile_edit_action($user) {
	echo"<h2>Devit</h2>".PHP_EOL;
	echo_new_user_meta($user);

}

add_action('personal_options_update', 'potapenkov_user_profile_update_action');
add_action('edit_user_profile_update', 'potapenkov_user_profile_update_action');
function potapenkov_user_profile_update_action($user_id) {
	
	$result[] = update_user_meta($user_id, 'devit_address', char_encode($_POST['devit_address']));
  	$result[] = update_user_meta($user_id, 'devit_phone', char_encode($_POST['devit_phone']));
  	$result[] = update_user_meta($user_id, 'devit_gender', char_encode($_POST['devit_gender']));
  	$result[] = update_user_meta($user_id, 'devit_family_status', char_encode($_POST['devit_family_status']));
  	
}




add_filter( 'template_include', 'potapenkov_replace_template', 99 );
function potapenkov_replace_template( $template ) {
	get_header(); 	?>

	<style>
		input{
			width: 100%;
		}
		.users-list{
			width: 80%; 
			margin: auto;
			padding: 50px;
		}
		.collapsible {
			background-color: #eee;
			color: #444;
			cursor: pointer;
			padding: 18px;
			width: 100%;
			border: none;
			text-align: left;
			outline: none;
			font-size: 15px;
		}
	
		.active, .collapsible:hover {
			background-color: #ccc;
		}

		
		.content {
			padding: 0 18px;
			display:none;
			overflow: hidden;
			background-color: #f1f1f1;	  		
  			transition: max-height 0.2s ease-out;
		}
	</style>
	<script>  
	document.addEventListener('DOMContentLoaded', function(){
		var coll = document.getElementsByClassName("collapsible");
		console.log(coll.length);
		for (var i = 0; i < coll.length; i++) {
			
			coll[i].addEventListener("click", function() {
				
			this.classList.toggle("active");
			var content = this.nextElementSibling;
			console.log(content);
			if (content.style.display === "block") {
			content.style.display = "none";
			//content.style.display = "none";
			} else {
			content.style.display = "block";
			//content.style.display = "content";
			}
		});
		}
	});
	</script>
  
	<div class="users-list"><?php
	
	foreach(get_users() as $user){
		
		$login = $user->user_login;
		
		echo "<button class='collapsible'>$login</button>
				<div class='content'>";
		
		if(is_user_logged_in() )
			echo"<a href=".get_edit_user_link($user->ID).">Edit $login's profile</a></br>";
		else
			echo"<a href=\"".get_admin_url(null, "user-edit.php?user_id=".$user->ID)."\">Edit $login's profile</a></br>";

		echo_new_user_meta($user, true).PHP_EOL;
		echo "</div>".PHP_EOL;		
	}
	
	get_footer();
}

function my_get_user_meta($user_id, $meta_name){
	global $wpdb;
	$sql = $wpdb->prepare( "SELECT `meta_value` FROM ".$wpdb->prefix."usermeta where `meta_key`=%s and `user_id`=%d;", $meta_name, $user_id);
	
	$result = $wpdb->get_var( $sql);
	
	return char_decode($result);
}


function echo_new_user_meta($user, $disabled=false){
	/* из задания: "для просмотра  инфо должно быть получено из бд и расшифровано" */
	if($disabled){ //при выводе на "фронтенд" применяем функцию, которая извлекает зи БД и расшифровывает поля
		$address = my_get_user_meta( $user->ID, 'devit_address');//адрес  
		$phone = my_get_user_meta( $user->ID, 'devit_phone');//телефон
		$gender = my_get_user_meta( $user->ID, 'devit_gender');//пол 
		$family_status = my_get_user_meta( $user->ID, 'devit_family_status');//семейный статус 
	}else{
		$address = char_decode(get_user_meta( $user->ID, 'devit_address', true ));//адрес  
		$phone = char_decode(get_user_meta( $user->ID, 'devit_phone', true ));//телефон
		$gender = char_decode(get_user_meta( $user->ID, 'devit_gender', true ));//пол 
		$family_status = char_decode(get_user_meta( $user->ID, 'devit_family_status', true ));//семейный статус 
	}
	?>
	<table class="form-table">
	<tr class="user-address-wrap">
	<th><label for="devit_address">	адрес</label></th>
	<td><input name="devit_address" type="address" id="devit_address" value="<?php echo $address."\""; if($disabled) echo " disabled";?> ></td></tr>
	
	<tr class="user-tel-wrap">
	<th><label for="devit_phone">телефон</label></th>
	<td><input name="devit_phone" type="tel" id="devit_phone" value="<?php echo $phone."\""; if($disabled) echo " disabled";?> ></td></tr>
		
	<tr class="user-gender-wrap">
	<th><label for="devit_gender">пол</label></th>
	<td><input name="devit_gender" type="text" id="devit_gender" value="<?php echo $gender."\""; if($disabled) echo " disabled";?> ></td></tr>
		
	<tr class="user-family-status-wrap">
	<th><label for="devit_family_status">семейный статус</label></th>
	<td><input name="devit_family_status" type="address" id="devit_family_status" value="<?php echo $family_status."\""; if($disabled) echo " disabled";?> ></td></tr>
	</table>

	<?php 

}