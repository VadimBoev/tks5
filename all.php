<!doctype html>
<html lang="ru">
<?php
	include("db.php");
	include("Mpdf/mpdf.php");
?>
	<head>
		<meta charset="utf-8">	
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!--Bootstrap-->
		<link rel="stylesheet" href="css/studsovet.css">
		<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
		<!-- (Optional) Latest compiled and minified JavaScript translation files -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/i18n/defaults-ru_RU.min.js"></script>
		<title>Расписание</title>
	</head>
	<body>
		<!--Навбар-->
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<a class="navbar-brand" href="index.php">
				<img class="logo-custom center-block" style="margin-top: -5px" width="70%" src="favicon.ico" alt="">
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			  <span class="navbar-toggler-icon"></span>
			</button>			
			<div class="collapse navbar-collapse" id="navbarCollapse">
			  <ul class="navbar-nav mr-auto">
				<li class="nav-item active">
				  <a class="nav-link" href="index.php">Главная</a>
				</li>
			  </ul>
			</div>					
		</nav>
		
		<div class="container mt-3">
				<form method="POST" class="form-horizontal" role="form">
				  <div class="card-deck mb-3">
					<div class="card mb-4">
							<select name = 'date_select' id="error" class="selectpicker form-control" data-title="Дата" data-live-search="true" data-size="5" data-live-search-placeholder="Дата">
								<?php
									if($_POST['date_select'] != "")
									{
										echo "<option selected > {$_POST['date_select']} </option>";
										$sql = "SHOW TABLES FROM `rasp`";
										$result_select = mysqli_query($connect, $sql);
										while($object = mysqli_fetch_row($result_select))
										{
											if($object[0] != $_POST['date_select'])
											{
												if($object[0] != "black_list" and $object[0] != "data" and $object[0] != "last_action" and $object[0] != "activity")
												{	
													echo "<option value = '$object[0]' > {$object[0]} </option>";
												}
												//echo "<option value = '{$object[0]}' > {$object[0]} </option>";
											}	
										}
									}
									else
									{
										$sql = "SHOW TABLES FROM `rasp`";
										$result_select = mysqli_query($connect, $sql);
										while($object = mysqli_fetch_row($result_select))
										{
											if($object[0] != "black_list" and $object[0] != "data" and $object[0] != "last_action" and $object[0] != "activity")
											{	
												echo "<option value = '$object[0]' > {$object[0]} </option>";
											}
										}
									}	
								?>  
							</select>
						</div>	
						<div class="card mb-4">
							<input class="btn btn-primary" type="submit" name="enter" value='Показать'/>
						</div>
					</div>
				</form>
			</div>		
	
			<!--<h4><div class="text-center alert alert-warning" role="alert">Расписание может измениться в любое время</div></h4>-->
			<!--<h4><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center alert alert-info" role="alert">Общее расписание на стадии разработки</div></h4>-->

				<!--<h5><div class="text-center alert alert-danger" role="alert">Выберите дату</div></h5>-->

			<!-- <div class="container col-lg-12" style="overflow-x: auto"> -->

		<?php

		if(isset($_POST['enter'])) 
		{
			//надпись была тут
			$selectium1 = $_POST['date_select'];
			$new_filename = $selectium1;

			$check_status = mysqli_query($connect, "SELECT table_name,Create_time,Update_time FROM information_schema.tables WHERE table_schema = 'rasp'");
			$check_status_object = mysqli_fetch_object($check_status);

			$get_size = filesize("test/files/$new_filename.docx");
			$filename = "test/files/$new_filename.docx";
			$result_get_size = $get_size/1024;
			$mega_get_size = round($result_get_size,1);

			if( $selectium1 != '' ) //существует $query ???
			{
				echo "<div class='container text-center mt-3'>";
				  echo "<div class='jumbotron mt-3'>";
						echo "<div class='card-deck mb-3'>";
							echo "<div class='card mb-4'>";
								echo "<a href='$new_filename.html' role='button' target='_blank' role='button' class='btn btn-success btn-lg'>Онлайн просмотр</a>";
							echo "</div>";
							echo "<div class='card mb-4'>";
								echo "<a href='$filename' class='btn btn-primary btn-lg' role='button' target='_blank'>Скачать ($mega_get_size Кб)</a>";
							echo "</div>";
							echo "<div class='card mb-4'>";
								echo "<a href='https://cloud.mail.ru/public/-/$new_filename.docx' role='button' target='_blank' class='btn btn-primary btn-lg'>cloud.mail.ru</a>";
							echo "</div>";
						echo "</div>";	
				  echo "</div>";	
				echo "</div>";
			}
			else
			{
				echo "<div class='container'>";
				echo '<h5><div class="text-center alert alert-danger" role="alert">Выберите дату</div></h5>';
				echo "</div>";
			}

			/*<div class="text-center">
				<div class="btn-group">
					<a href=1 class="btn btn-small">1</a>
					<a href=1 class="btn btn-small">2</a>
					<a href=1 class="btn btn-small">3</a>
				</div>
			</div>*/
			/*echo "<tbody>";
			echo "<tr>";
			echo "<td><b><h4>$new_filename</h4></b></td>";
			echo "<td><a href='https://tks5.ru/rasp/view.php?name=../test/files/$new_filename.docx' target='_blank' class='btn btn-primary' style='background-color: #0b4a89'>GOOGLE VIEWER</a><br><br><a href='https://tks5.ru/test/files/$new_filename.html' target='_blank' class='btn btn-success'>PAGE HTML ✔</a></td>";
			echo "<td><a href='$filename' class='btn btn-danger' target='_blank'>Скачать ($mega_get_size Кб)</a><br><br><a href='https://cloud.mail.ru/public/-/$new_filename.docx' target='_blank' class='btn btn-primary' style='background-color: #3e80c1'>cloud.mail.ru</a></td>";
			echo "</tr>";*/

			/*if($rows > 0)
			{
				echo '<div class="container col-xs-12 col-sm-12 col-md-12 col-lg-12"
					<div class="row">
						<button type="button" name="download" id="download" class="col-xs-12 btn btn-primary">Скачать в PDF</button>
					</div>
				</div>';
			}
			/*else
			{
				echo '<div class="row-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="button" disabled="disable" class="col-xs-12 btn btn-primary">Скачать в PDF</button>
				</div>';		
			}*/			
		}
		//кнопка скачать была тут
		
		?>
	
    </body>
</html>  