<?php
	require(__DIR__.'/inc/website.inc.php');

	use fearricepudding\website as website;


	if(isset($_GET['database'])){
		if(website::DBfirstTimeSetup()){
			header('location: setup.php');
			exit();
		}else{
			echo '<br>'.wesite::$error.'<br>';
		};

	};
	if(website::getDB()){
		if(website::checkDatabaseSetup()){
			?>
			<p>*Database is setup and ready*</p>
			<p><b>Next step:</b> Setup your email templates!</p>
			
			<?php
		}else{
			echo website::$error;
			?>
			<div class="container">
				<p>*Database not setup*</p>
				<a href="?database">Setup Database</a>
			</div>

			<?php
		};
	}else{
		?>
		<div class="container">
			<p>Couldn't connect to database, please check your config!</p>
		</div>
		<?php
	};
?>


