<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Bastien PUJOS
# VERSION : 5.2
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
			<h1 class="page-header"><?php echo getLabel("label.admin_notifier.method.title"); ?></h1>
		</div>
	</div>
	
	<?php
	// Get post data
	$method_id=retrieve_form_data("id",null);
	$method_name=retrieve_form_data("method_name",null);
	$method_name_old=retrieve_form_data("method_name_old",null); 
	$method_type=retrieve_form_data("type",null);
	if(is_null($method_type) or ($method_type!="host" and $method_type!="service")) { $method_type="host"; }
	$method_line=retrieve_form_data("method_line",null);
		
	// ADD or UPDATE
	if(isset($_POST["add"]) || isset($_POST["update"])) {
		
		// Check if method exists
		if($method_name){
			$sql_test = "SELECT count(name) FROM methods WHERE name='".$method_name."' AND type='".$method_type."'";
			$test_exist = sqlrequest($database_notifier,$sql_test);
			$method_exist=mysqli_result($test_exist,0);
		}

		// Tests
		if(!$method_name || $method_name==""){
			message(7," : Your method need a name",'warning');
		}elseif(!$method_line || $method_line==""){
			message(7," : Your method need a method line",'warning');
		}elseif((isset($_POST["add"]) && $method_exist!=0) || (isset($_POST["update"]) && $method_exist!=0 && $method_name != $method_name_old)){
			message(7," : This method name already exist",'warning');
		}elseif(isset($_POST["add"])){
			$sql_add = "INSERT INTO methods VALUES('','".$method_name."','".$method_type."','".addslashes($method_line)."')";
			$method_id = sqlrequest($database_notifier,$sql_add,true);
			message(6," : Method have been added",'ok');
			$method_name_old=$method_name;
		}elseif(isset($_POST["update"])){
			$sql_update = "UPDATE methods SET name='".$method_name."', type='".$method_type."', line='".addslashes($method_line)."' WHERE id='".$method_id."' AND type='".$_POST['type']."'";
			sqlrequest($database_notifier,$sql_update,true);
			message(6," : Method have been updated",'ok');
			$method_name_old=$method_name;
		}
	} 
	// DISPLAY
	elseif(isset($_GET["id"])) {
		if(is_numeric($_GET["id"])) {
			$method_sql=sqlrequest($database_notifier,"SELECT * from methods where id='".$_GET["id"]."'");
			
			if(mysqli_result($method_sql,0,"id")) {
				$method_id=mysqli_result($method_sql,0,"id");
				$method_name=mysqli_result($method_sql,0,"name");
				$method_name_old=mysqli_result($method_sql,0,"name");
				$method_type=mysqli_result($method_sql,0,"type");
				$method_line=mysqli_result($method_sql,0,"line");
			} else {
				message(7," : Method does not exist",'warning');
			}
		} else {
			$method_id=null;
		}
	}
	?>
		
	<form action="./methods.php" method="POST" name="form">

		<input type="hidden" name="id" value="<?php echo $method_id; ?>">
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.metohd.name") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="textbox" name="method_name" value="<?php echo $method_name; ?>" autofocus>
				<input type="hidden" name="method_name_old" value="<?php echo $method_name; ?>">
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.metohd.type") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="textbox" name="type" value="<?php echo $method_type; ?>" readonly>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.metohd.line") ?></label>
			<div class="col-md-9">
				<textarea class="form-control" rows="5" name="method_line" scrolling="no"><?php echo $method_line; ?></textarea>
			</div>
		</div>

		<div class="form-group">
			<?php
				if (isset($method_id) && $method_id!=null) {
					echo "<input class='btn btn-primary' class='button' type='submit' name='update' value=".getLabel('action.update').">";
				}
				else {
					echo "<input class='btn btn-primary' class='button' type='submit' name='add' value=".getLabel('action.add').">";
				}
			?>
			<input class="btn btn-default" class="button" type="button" name="back" value="<?php echo getLabel("action.cancel"); ?>" onclick="location.href='index.php?action=methods'">
		</div>
	</form>
</div>

<?php include("../../footer.php"); ?>
