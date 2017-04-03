<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.1
# APPLICATION : eonweb for eyesofnetwork project
#
# LICENCE :
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
#########################################
*/

include("../../header.php");
include("../../side.php");

?>

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.monitoring_passwd.title"); ?></h1>
		</div>
	</div>

	<?php
		$login=$_COOKIE['user_name'];
		$usrid=$_COOKIE['user_id'];
		$user_password1= "abcdefghijklmnopqrstuvwxyz";
		$user_password2= "abcdefghijklmnopqrstuvwxyz";
		$userDefaultpage = mysqli_result(sqlrequest("$database_eonweb","SELECT user_defaultpage FROM users WHERE user_id='".$usrid."'"),0);

		if(isset($_POST["update"])) {
			$user_password1 = retrieve_form_data("user_password1","");
			$user_password2 = retrieve_form_data("user_password2","");
			$user_language = retrieve_form_data("user_language","");
			$user_page = retrieve_form_data("user_defaultpage","");
			if (($user_password1 != "") && ($user_password1 != null) && ($user_password1 == $user_password2)) {
				if($user_password1!="abcdefghijklmnopqrstuvwxyz") {
					$user_password = md5($user_password1);

					// Insert into eonweb
					sqlrequest("$database_eonweb","UPDATE users set user_passwd='$user_password' WHERE user_id='$usrid';");
					
					// update password into nagvis if user is in
					$bdd = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
					$req = $bdd->query("SELECT userId, name FROM users WHERE name='".$login."'");
                    $nagvis_user_exist = $req->fetch();

                    // this is nagvis default salt for password encryption security
					$nagvis_salt = '29d58ead6a65f5c00342ae03cdc6d26565e20954';

					if($nagvis_user_exist["userId"] > 0){
						$nagvis_id = $nagvis_user_exist["userId"];
						$hashed_password = sha1($nagvis_salt.$user_password1);
						$bdd->exec("UPDATE users SET password = '$hashed_password' WHERE userId = $nagvis_id");
					}

					// logging action
					logging("admin_user","UPDATE PASSWORD : $usrid $login");
				}
				message(8," : ".getLabel("message.monitoring_passwd.ok"),'ok');
				$user_password1= "abcdefghijklmnopqrstuvwxyz";
				$user_password2= "abcdefghijklmnopqrstuvwxyz";
			}
			else {
				message(8," : ".getLabel("message.monitoring_passwd.error"),'warning');
			}
			if($user_language != '' || $user_language!=$langtmp){
				sqlrequest("$database_eonweb","UPDATE users set user_language='$user_language' WHERE user_id='$usrid';");
			}
			if($user_page != $userDefaultpage || $user_page == '' || $user_page == '0'){
				sqlrequest("$database_eonweb","UPDATE users set user_defaultpage='$user_page' WHERE user_id='$usrid';");
			}
		}	
		
		// Display user language selection  
		function GetUserLang() {

			global $database_eonweb;
			global $user_id;
			global $path_languages;

			// definition of variables and Research language files
			$path_label_lang = "label.admin_user.user_lang_"; 
			$files = array('en');
			$handler = opendir($path_languages);

			while ($file = readdir($handler)) {
				if(preg_match('#messages-(.+).json#', $file, $matches)){
					$files[] = $matches[1];
				}
			}

			closedir($handler);
			$files = array_filter($files);
			array_unshift($files,"0");
			$files = array_unique($files);

			// creation of a select and catch values
			$langtmp = mysqli_result(sqlrequest("$database_eonweb","SELECT user_language FROM users WHERE user_id='".$_COOKIE['user_id']."'"),0);
			$res = '<select class="form-control" name="user_language">';
			foreach($files as $v) {
				if($v == $langtmp){
					$res.="<option value='".$v."' selected=selected>".getLabel($path_label_lang.$v)."</option>";
				}
				else{
					$res.="<option value='".$v."'>".getLabel($path_label_lang.$v)."</option>";
				}
			}
			$res .= '</select>';

			return $res;
		}
	?>
	
	<form method='POST' name='form_user'>
		<div class="form-group">
			<div class="row">
				<label class="col-md-3"><?php echo getLabel("label.monitoring_passwd.pwd"); ?></label>
				<div class="col-md-9">
					<input class="form-control" type='password' name='user_password1' value='<?php echo $user_password1?>'>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-md-3"><?php echo getLabel("label.monitoring_passwd.pwd2"); ?></label>
				<div class="col-md-9">
					<input class="form-control" type='password' name='user_password2' value='<?php echo $user_password2?>'>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_lang"); ?></label>
			<div class="col-md-9">
				<?php echo GetUserLang(); ?>
			</div>
		</div>
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_defaultpage"); ?></label>
			<div class="col-md-9">
				<?php 
			// Display user language selection  
				$userLim = mysqli_result(sqlrequest("$database_eonweb","SELECT user_limitation FROM users WHERE user_id='".$usrid."'"),0);
				$groupID = mysqli_result(sqlrequest("$database_eonweb","SELECT group_id FROM users WHERE user_id='".$usrid."'"),0);
				
				$m = new Translator();
				// load right menu file according to user limitation (LEFT menu)
				if( $userLim != 0 ){
					$m->initFile($path_menu_limited, $path_menu_limited_custom);
				} else {
					$m->initFile($path_menus,$path_menus_custom);
				}
				$menus = $m->createPHPDictionnary();
				
				// creation of a autocomplete with all values that are possible
				$res2 = "<input id='user_defaultpage' class='form-control' type='text' name='user_defaultpage' onFocus='$(this).autocomplete({source: [";
				if(isset($menus["menutab"])){
					foreach($menus["menutab"] as $menutab){
						$tab_request = "SELECT tab_".$menutab["id"]." FROM groupright WHERE group_id=".$groupID.";";
						$tab_right = mysqli_result(sqlrequest($database_eonweb, $tab_request),0);				
						if($tab_right == 0){ continue; }
						
						if(isset($menutab["link"])){
							foreach($menutab["link"] as $menulink) {
								if($menulink["target"]=="frame") { $res2 .= '"'.$path_frame.urlencode($menulink['url']).'",';	}
								else{$res2 .= '"'.$menulink["url"].'",';}
							}
						}
						if(isset($menutab["menusubtab"])){
							foreach($menutab["menusubtab"] as $menusubtab) {
									foreach($menusubtab["link"] as $menulink) {
										if($menulink["target"]=="frame") { $res2 .= '"'.$path_frame.urlencode($menulink['url']).'",';	}
										else{$res2 .= '"'.$menulink["url"].'",';}
									}
							}
						}
					}
				}
				else{
					foreach($menus["menutab"] as $menutab){
						$tab_request = "SELECT tab_".$menutab["id"]." FROM groupright WHERE group_id=".$groupID.";";
						$tab_right = mysqli_result(sqlrequest($database_eonweb, $tab_request),0);				
						if($tab_right == 0){ continue; }
						
						if(isset($menus["link"])){
							foreach($menus["link"] as $menulink) {
								$res2 .= '"'.$menulink["url"].'",';
								if($menulink["target"]=="frame") { $res2 .= '"'.$path_frame.urlencode($menulink['url']).'",';		}
								else{$res2 .= '"'.$menulink["url"].'",';}
							}
						}
					}
				}
				$res2 = rtrim($res2, ",");
				$res2 .= "]})'>";
				
				echo 	$res2;
				?>
			</div>
		</div>
		<button class='btn btn-primary' type='submit' name='update' value='update'><?php echo getLabel("action.update"); ?></button>
		<button id="back_btn" class='btn btn-default' type='button' onclick='history.go(-1);'><?php echo getLabel("action.cancel"); ?></button>
	</form>

</div> <!-- !#page-wrapper -->

<?php include("../../footer.php"); ?>