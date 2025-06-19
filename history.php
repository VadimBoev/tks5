<?php
	include("db.php");
?>
<!doctype html>
<html lang="ru">
	<head>
		<meta charset="utf-8">	
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!--Bootstrap-->
		<link rel="stylesheet" href="css/studsovet.css">
		<!-- <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script> -->
		<script type="text/javascript" src="js/jquery-3.1.1.js"></script>
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script> -->
		<script type="text/javascript" src="js/popper.min.js"></script>
		<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script> -->
		<script type="text/javascript" src="js/boostrap.min.4_2_1.js"></script>
		<!-- Latest compiled and minified CSS -->
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> -->
		<link rel="stylesheet" href="css/bootstrap-select.css">
		<!-- Latest compiled and minified JavaScript -->
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> -->
		<script type="text/javascript" src="js/bootstrap-select.min.js"></script>
		<!-- (Optional) Latest compiled and minified JavaScript translation files -->
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/i18n/defaults-ru_RU.min.js"></script> -->
		<script type="text/javascript" src="js/bootstrap-select.ru.js"></script>
		
		<title>История изменений</title>
		<style>
		html { height: 95%; }
		body {
		  min-height:95%; 
		  position:relative; 
		  padding-bottom: 70px;
		}
		.footer { 
		  position: absolute; 
		  left: 0;
		  right: 0;
		  bottom: 0; 
		  height: 50px; 
		}
		.button_test {
		  border-radius: 5px;
	    }	
		.jumbotron { min-height: 220px; }	
		</style>
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
		
		
		<div class="container">
			
			<h2 align="center"><b>История изменений расписания:</b></h2>
			<h3 align="center">(Оптимизация в процессе)</h3>

			<table class="table table-striped">
			  <thead>
				<tr>
				  <th scope="col">Время</th>
				  <th scope="col">Действие</th>
				  <th scope="col">Название</th>
				</tr>
			  </thead>
			  <tbody>
				<?php
				
				$get_list = mysqli_query($connect, "SELECT * FROM `last_action` ORDER BY `id` DESC LIMIT 250");
				$get_list_num_rows = mysqli_num_rows($get_list);
				if($get_list_num_rows > 0)
				{
					while ($row = mysqli_fetch_assoc($get_list)) 
					{
						$get_name = $row['name'];
						$get_time = $row['unix_last_time'];
						$get_action = $row['action'];
						
						echo '<tr>';
						echo '<td>'.date('[d.m.y H:i:s]', $get_time).'</td>';
						if($get_action == 1)
						{
							echo '<td>Добавление</td>';
						}
						else
						{
							echo '<td>Обновление</td>';
						}
						echo '<td>'.$get_name.'</td>';
						echo '</tr>';
					}
				}
				
				?>
			  </tbody>
			</table>				
					
			</h4>			
		</div>	

		<footer class="footer">
			<h4><p class="text-muted" align="center"><a href="https://vk.com/boevx"><b>© Вадим Боев, 2017-2021</b></a></p></h4>
		</footer>			

	</body>
</html>