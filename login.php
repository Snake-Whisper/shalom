<html>
	<head>
		<title>Shalom</title>
		<link rel="stylesheet" href="css/index.css" type="text/css" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<?php
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				
				echo "Please implement login: " . $config["mysql"]["username"];
			} else { ?>
		<div class="container">
			<div class='login'>
				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<label for="distributor">Email Verteiler:</label>
					<input type="text" name="distributor"  required/><br>
					<label for="passowrd">Password:</label>
					<input type="password" name="password" required/><br>
					<input type="submit" value="Öffnen"/>
				</form>
			</div>
		</div> <?php } ?>
	</body>
</html>