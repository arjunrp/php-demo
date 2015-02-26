<?php
	session_start();
$name = 'arjun';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>ATM - Home</title>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
		<link rel="stylesheet" type="text/css" media="screen" href="www/css/bootstrap.min.css">
		<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
		<style>
			@media(max-width:600px){
				h2{
					font-size:20px;
				}
			}
			body{
				font-family: 'Lato', sans-serif;
			}
			input,button,select{
				border-radius:2px !important ;
			}


			#message{
				margin-left:10px;
				color:#ED5F54;
			}
			.right{
				float:right;
			}
			.container>.row{
				margin-top:5%;
				text-align:center;
			}
			.home-btn{
				width:140px;

			}
			.modal-body>row{
				padding-left:10px;
				padding-right:10px;

			}
			#alert{
				z-index:5000;
			}
		</style>
	</head>
	<body>

	<div class="modal fade" id="alert">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 id="alert-message">Modal body</h3>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="withdraw-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Withdraw Amount</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 form-group">
							<label class="">Account</label>
							<select id="withdraw-account" class=" form-control">
								<option>Savings</option>
								<option>FIXED</option>
							</select>
						</div>
						<div class="col-md-6">
							<label class="">Amount</label>
							<input type="text" class="form-control" id="withdraw-amount">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="withdraw-submit" class="btn btn-primary">Withdraw</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="pin-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Change PIN Number</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 form-group">
							<label class="">New Pin</label>
							<input type="text" class="form-control" id="pin-new">
						</div>
						<div class="col-md-6">
							<label class="">Confirm New Pin</label>
							<input type="text" class="form-control" id="pin-confirm">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 form-group">
							<label class="">Old Pin</label>
							<input type="text" class="form-control" id="pin-old">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="pin-submit" class="btn btn-primary">Change PIN</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="statment-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Transactions</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4"></div>
						<div class="col-md-4">
							<div class="input-group">
								<input type="text" class="form-control" placeholder="No of transactions" id="statement-count">
								<span class="input-group-btn">
									<button id='statement-submit' class="btn btn-primary">Load</button>
								</span>
							</div>
						</div>
						<div class="col-md-4"></div>
					</div>
					<div>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>TID</th>
									<th>Time</th>
									<th>ATM Details</th>
									<th>Description</th>
								</tr>
							</thead>
							<tbody id="statement-list">

							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class=""><h2>Hi <?php echo $name; ?>,<br/>Welcome to ATM Services</h2></div>
		</div>
		<div class="row">
			<div class="col-xs-4"> <button data-toggle="modal" data-target="#withdraw-modal" class="btn btn-primary home-btn">Withdraw</button> </div>
			<div class="col-xs-4"></div>
			<div class="col-xs-4"> <button data-toggle="modal" data-target="#pin-modal" class="btn btn-primary home-btn right" >Change PIN</button> </div>
		</div>

		<div class="row">
			<div class="col-xs-4"> <button id="balance-home" data-toggle="popover"
			data-content="<button class='btn btn-primary'>Fixed</button>" title="Select account type" class="btn btn-primary  home-btn">Check Balance</button> </div>
			<div class="col-xs-4"></div>
			<div class="col-xs-4"> <button data-toggle="modal" data-target="#statment-modal" class="btn btn-primary right home-btn" >Account Statement</button> </div>
		</div>
	</div>




	<script type="text/javascript" src="www/js/jquery.min.js"></script>
	<script type="text/javascript" src="www/js/bootstrap.min.js"></script>
		<script>
			window.alert = function(message){
					$("#alert-message").text(message.toString());
					$('#alert').modal({"show":true,"keyboard":true});
			}
			$(document).ready(function(){
				$('#balance-home').popover({'placement':'bottom','html':true,'template':'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content">sadsad</div></div>'});

				$('#pin-submit').click(function(){
						alert('Pin changed !!');
				});
				//$('#balance-home').popover({'placement':'left',template:''});
			});

		</script>
	</body>
</html>
