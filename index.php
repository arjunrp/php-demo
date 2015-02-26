<!DOCTYPE html>
<html>
	<head>
		<title>ATM - Login</title>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
		<link rel="stylesheet" type="text/css" media="screen" href="www/css/bootstrap.min.css">
		<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
		<style>
			body{
				font-family: 'Lato', sans-serif;
			}
			input{
				border-radius:2px !important ;
			}
			.row{
				margin-top:14%;
			}
			#message{
				margin-left:10px;
				color:#ED5F54;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<form id="login">
						<div class="form-group">
							<label for="acno">Account No</label>
							<input autofocus autocomplete="off" class="form-control" type="text" id="acno" placeholder="Account Number">
						</div>
						<div class="form-group">
							<label for="acno">PIN No</label>
							<input class="form-control" type="password" id="pin" placeholder="PIN Number">
						</div>
						<input id="submit-btn" type="submit" class="btn btn-primary" value="Login">
						<span id="message"></span>
					</form>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	<script type="text/javascript" src="www/js/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			$("#login").submit(function(e){
				e.preventDefault();
				$('#message').text("");
				$(this).attr('disabled','true');
				var account = $("#acno").val(),
					pin = $("#pin").val();

				if(account===''){
					$('#message').text("Enter Account No");
					return;
				}

				if(pin.search(/^\d{4}$/)===-1){
					$('#message').text("Invalid Pin");
					return;
				}
				$('#submit-btn').attr('disabled','true');
				$.ajax({
					type:'POST',
					url:'ajax.php',
					data: "id=1&account="+encodeURIComponent(account)+"&pin="+pin,
					success:function(data){
						try{
							data = JSON.parse(data);
							$('#message').text(data.message);
							if(data.success===true){
								window.location='home.php';
							}
						}
						catch(e){
							$('#message').text("Invalid response from server");

						}
						$('#submit-btn').removeAttr('disabled');
					},
					error:function(){
						$('#message').text("Cannot Connect to server");
						$('#submit-btn').removeAttr('disabled');
					}
				})

			});

		});
	</script>
	</body>
</html>
