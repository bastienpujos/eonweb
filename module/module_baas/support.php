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
include("/srv/eyesofnetwork/vconso-baas/include/config.php");
include("/srv/eyesofnetwork/vconso-baas/include/function.php");
?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel('menu.link.support'); ?></h1>
		</div>
	</div>

	<?php
		// Check if all fields are sent
		if(isset($_POST["action"])) {
			extract($_POST);
			if(empty($customer) or empty($firstname) or empty($lastname) or empty($phone) or empty($mail) or empty($problem)) {
				message(7," : All fields are necessary","warning");
			} else {
				sendMail(
					$vconso_email_support,
					getLabel("label.product.name")." <".$mail.">",
					getLabel("label.module_baas.incident")." : ".$customer."",
					"Date : ".date($dateformat) .
					"\r\n\nInterlocuteur : ".$firstname." ".$lastname .
					"\r\n\nTéléphone : ".$phone .
					"\r\n\nDescription incident : ".$problem
				);
				message(7," : Email sent","ok");
			}
		}
	?>
		
	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo getLabel("label.module_baas.support.title"); ?>
		</div>
		
		<div class="panel-body">
			<form name="support" action="support.php" method="post">
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.module_baas.customer"); ?></label>
				<div class="col-md-9">
					<input type="text" name="customer" class="form-control"
					<?php if(isset($customer)) { echo "value=\"$customer\""; } ?>>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.module_baas.firstname"); ?></label>
				<div class="col-md-9">
					<input type="text" name="firstname" class="form-control"
					<?php if(isset($firstname)) { echo "value=\"$firstname\""; } ?>>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.module_baas.lastname"); ?></label>
				<div class="col-md-9">
					<input type="text" name="lastname" class="form-control"
					<?php if(isset($lastname)) { echo "value=\"$lastname\""; } ?>>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.module_baas.phone"); ?></label>
				<div class="col-md-9">
					<input type="text" name="phone" class="form-control"
					<?php if(isset($phone)) { echo "value=\"$phone\""; } ?>>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.module_baas.mail"); ?></label>
				<div class="col-md-9">
					<input type="text" name="mail" class="form-control"
					<?php if(isset($mail)) { echo "value=\"$mail\""; } ?>>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.module_baas.problem"); ?></label>
				<div class="col-md-9">
					<textarea name="problem" class="form-control textarea"><?php if(isset($problem)) { echo $problem; } ?></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
				<button class="btn btn-primary" type="submit" name="action" value="action">
					<?php echo getLabel("action.submit"); ?>
				</button>
				<button class="btn btn-default" type="button" name="back" value="back" onclick="location.href='support.php'">
					<?php echo getLabel("action.cancel"); ?>
				</button>
				</div>
			</div>
			</form>
		</div>
	</div>
	
</div>

<?php include('../../footer.php'); ?>
