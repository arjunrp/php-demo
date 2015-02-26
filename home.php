<?php

session_start();
if(empty($_SESSION['account'])){
	die(header('location:index.php'));
}
$name = $_SESSION['name'];
$accounts = $_SESSION['types'];
$balanceStr = '';
$selectStr = '';
foreach($accounts as $item){
	if($item==='S'){
		$balanceStr .= "<button class='btn btn-primary' id='balance-savings'>Savings</button>";
		$selectStr .= "<option value='S'>Savings</option>";
	}
	if($item==='F'){
		$balanceStr .= "<button class='btn btn-primary' id='balance-fixed'>Fixed</button>";
		$selectStr .= "<option value='F'>Fixed</option>";
	}
	if($item==='C'){
		$balanceStr .= "<button class='btn btn-primary' id='balance-current'>Current</button>";
		$selectStr .= "<option value='C'>Current</option>";
	}
}

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
			#statment-modal table{
				margin-top:15px;
			}
			.popover-content button:nth-child(2),.popover-content button:nth-child(3){
				margin-left:10px;
			}
			.loading{
				width:100%;
				height:100%;
				z-index:5000;
				opacity:.5;
				position:absolute;
				top:0px;
				left:0px;
				background-color:rgba(255,255,255,1);
				background-image:url('www/img/loader.gif');
				background-repeat:no-repeat;
				background-position: 50% 40%;
			}

		</style>
	</head>
	<body>
	<div class="loading"></div>
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
								<option value="-1">--Select account type--</option>
								<?php echo $selectStr; ?>
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
							<input type="password" class="form-control" id="pin-new">
						</div>
						<div class="col-md-6">
							<label class="">Confirm New Pin</label>
							<input type="password" class="form-control" id="pin-confirm">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 form-group">
							<label class="">Old Pin</label>
							<input type="password" class="form-control" id="pin-old">
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
			data-content="<?php echo $balanceStr; ?>" title="Select account type" class="btn btn-primary  home-btn">Check Balance</button> </div>
			<div class="col-xs-4"></div>
			<div class="col-xs-4"> <button id="statment" class="btn btn-primary right home-btn" >Account Statement</button> </div>
		</div>

		<div class="row">
			<div class="col-xs-4"></div>
			<div class="col-xs-4"></div>
			<div class="col-xs-4"><button id="exit-home" class="btn btn-primary right  home-btn">Exit</button></div>
		</div>
	</div>

	<script type="text/javascript" src="www/js/jquery.min.js"></script>
	<script type="text/javascript" src="www/js/bootstrap.min.js"></script>
		<script>
			window.alert = function(message){
					$("#alert").modal('hide');
					$("#alert-message").text(message.toString());
					$('#alert').modal({"show":true,"keyboard":true});
			}
			function loadTransactions(count){
				$.ajax({
					url:'ajax.php',
					type:'POST',
					data:'id=2&count='+count,
					success:function(data){
						try{
							data=JSON.parse(data);
							$('#statement-count').val("");
							if(data.success===true){
								$('#statement-list').empty();

								if(data.message.length===0){
									alert("No transaction records found");
									$('#statment-modal').modal('hide');
									return;
								}
								for(i in data.message){
									$('#statement-list').append('<tr>\
										<td>'+data.message[i].id+'</td>\
										<td>'+data.message[i].moment+'</td>\
										<td>'+data.message[i].atm+'</td>\
										<td>'+data.message[i].description+'</td>\
									</tr>');
								}
							}
							else{
								alert(data.message);
							}
						}
						catch(e){
							alert("Invalid Response From Server");
						}
					},
					error:function(){
						alert("Cannot Connect To Server");
					}
				});
			}
			function getBalance(type){
				$.ajax({
					url:'ajax.php',
					type:'POST',
					data:'id=4&type='+type,
					success:function(data){
						try{
							data=JSON.parse(data);
							if(data.success===true){
								message=''
								if(type==='C'){
									message="Your 'Current Account' balance is: Rs.";
								}
								if(type==='S'){
									message="Your 'Savings Account' balance is: Rs.";
								}
								if(type==='F'){
									message="Your 'Fixed Account' balance is: Rs.";
								}
								message+=data.message;
								alert(message);

							}
							else{
								alert(data.message);
							}
							$('#balance-home').popover('hide');
						}
						catch(e){
							alert("Invalid Response From Server");
						}
					},
					error:function(){
						alert("Cannot Connect To Server");
					}
				});
			}

			$(document).ready(function(){
				$('#balance-home').popover({'placement':'bottom','html':true,'template':'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content">sadsad</div></div>'});

				$('.loading').css('display','none');

				$(document).ajaxStart(function(){
					$('.loading').css('display','block');
				})
				$(document).ajaxStop(function(){
					$('.loading').css('display','none');
				})

				$('#exit-home').click(function(){
					$.ajax({
					url:'ajax.php',
					type:'POST',
					data:'id=6',
					success:function(data){
						try{
							data = JSON.parse(data);
							if(data.success===true){
								window.location='home.php';
								return;
							}
							else{
								alert(data.message);
							}
						}
						catch(e){
							alert("Invalid response from server");
						}
					},
					error:function(){
							alert("Cannot Connect To Server");
						}
					});
				});

				$('#withdraw-submit').click(function(){
					var account = $('#withdraw-account').val(),
						amount = parseFloat($('#withdraw-amount').val());
					if(amount<=0){
						alert("Amount should be greater than zero");
						return;
					}
					$.ajax({
					url:'ajax.php',
					type:'POST',
					data:'id=5&account='+account+"&amount="+amount,
					success:function(data){
						try{
							data=JSON.parse(data);
							if(data.success===true){
								$('#withdraw-modal').modal('hide');
								$('#withdraw-account').val("-1");
								$('#withdraw-amount').val("-1");
							}
							alert(data.message);
						}
						catch(e){
							alert("Invalid Response From Server");
						}
					},
					error:function(){
						alert("Cannot Connect To Server");
					}
				});

				});
				$(document).on('click','#balance-current',function(){
					getBalance('C');
				})
				.on('click','#balance-fixed',function(){
					getBalance('F');
				})
				.on('click','#balance-savings',function(){
					getBalance('S');
				});
				$('#pin-submit').click(function(){
					var newPin = $("#pin-new").val(),
						cnfrmPin = $("#pin-confirm").val(),
						oldPin = $("#pin-old").val();
					if(newPin!==cnfrmPin){
						alert("PIN numbers not matching");
						return;
					}
					if(newPin.search(/^\d{4}$/)===-1){
						alert("New Pin should be a 4 digit no");
						return;
					}
					if(oldPin.search(/^\d{4}$/)===-1){
						alert("Old Pin should be a 4 digit no");
						return;
					}
					$.ajax({
					url:'ajax.php',
					type:'POST',
					data:'id=3&oldPin='+oldPin+'&newPin='+newPin,
					success:function(data){
							try{
								data=JSON.parse(data);
								if(data.success===true){
									$('#pin-modal').modal('hide');
								}
								alert(data.message);
								$("#pin-new").val("");
								$("#pin-confirm").val("");
								$("#pin-old").val("");
							}
							catch(e){
								alert("Invalid Response From Server");
							}
						},
						error:function(){
							alert("Cannot Connect To Server");
						}
					});
				});
				$('#statment').click(function(){
					$('#statment-modal').modal({'show':true});
					loadTransactions(5);
				});

				$('#statement-submit').click(function(){
					count = parseInt($('#statement-count').val());
					if(count<=0 || count >100 || isNaN(count)===true){
						alert("Count value should be between 1-100");
						return;
					}
					loadTransactions(count);
				});
			});
		</script>
	</body>
</html>
