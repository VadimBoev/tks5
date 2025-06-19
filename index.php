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
		
		<title>Расписание</title>
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
		</nav>	
		
		<!--Jumbotron-->
		<div class="container-fluid text-center mt-3">
			<div class="jumbotron mb-3">
				<h2>Новая версия расписания <span class="badge badge-primary">RELEASE</span></h2>
				<h5>&#10084; <b>Рассказывайте о сайте</b> &#10084;</h5>
				<h4>Создан на замену cloud.mail.ru</h4>
				<!-- <h4><b>Для студентов</b></h4> -->
				<!-- <h5><font color="red"><b>123</b></font></h5> -->
				<h6><b>Сайт tks5.ru существует последний учебный год. Идея сделать данный сайт на замену облака (mail.ru, куда нам заливают расписание) была весьма успешной.</br>
				Мне хотелось передать сайт кому-то кто остаётся в колледже, но некому. Спасибо всем кто заходил сюда и пользовался данной системой. Очень приятно.</br>
				Здесь никогда не было рекламы, сайт существовал на простой идее специально для всех людей, кому не хотелось скачивать постоянно документ Word.</br>
				К сожалению, 1 июля в 2021 году сайт прекратит своё существование раз и навсегда.</b></h6>
			</div>
		</div>
		
		<br/>
		
		
		
		<div class="container">
			<div class='btn-group-vertical col-12 px-0' role='group' aria-label='Basic example'>
			  <a class="btn btn-success btn-lg button_test shadow p-2 rounded" href="students.php"><p class="h3">Студентам</p></a>
			  <br/>
			  <!-- <button type="button" class="btn btn-danger btn-lg button_test shadow p-2 rounded" disabled><p class="h4">Преподавателям<br/>(в процессе)</p></button>
			  <br/>		 --> 
			  <a class="btn btn-info btn-lg button_test shadow p-2 rounded" href="all.php"><p class="h3">Общее</p></a>
			  <br/>
			  <a class="btn btn-warning btn-lg button_test shadow p-2 rounded" href="/backup/"><p class="h3">Backup расписания</p></a>
			</div>
			
			<br/>
			<br/>
			<br/>	
			
			<h2 align="center"><b>История изменений расписания:</b></h2>

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
				
				$get_list = mysqli_query($connect, "SELECT * FROM `last_action` ORDER BY `id` DESC LIMIT 5");
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
			<h5 align="center"><b><a href="history.php">Полная история изменений (Не оптимизировано)</a></b></h5>
			
			<br/>
			
			<h2 align="center"><b>Активность групп за сегодня:</b></h2>
			
			<table class="table table-striped">
			  <thead>
				<tr>
				  <th scope="col">Позиция</th>
				  <th scope="col">День расписания</th>
				  <th scope="col">Группа</th>
				</tr>
			  </thead>
			  <tbody>
				<?php
				
				$get_i = 0;
				
				$db_day = date("j");
				$db_month = date("m");
				$db_year = date("Y");
				
				$get_list = mysqli_query($connect, "SELECT COUNT(*) AS `repetitions`, `date`, `name` FROM `activity` WHERE `day` = '$db_day' and `month` = '$db_month' and `year` = '$db_year' GROUP BY `name` HAVING `repetitions` > 1 ORDER BY `repetitions` DESC LIMIT 5");
				$get_list_num_rows = mysqli_num_rows($get_list);
				if($get_list_num_rows > 0)
				{
					while ($row = mysqli_fetch_assoc($get_list)) 
					{
						$get_name = $row['name'];
						$get_date = $row['date'];
						
						$get_i++;
						
						if($get_i == 1)
						{
							echo '<tr>';
							echo '<th scope="row">ТОП-1</th>';
							echo '<td>'.$get_date.'</td>';
							echo '<td><b>'.$get_name.' &#10084;</b></td>';
							echo '</tr>';
						}
						else
						{
							echo '<tr>';
							echo '<td>'.$get_i.'</td>';
							echo '<td>'.$get_date.'</td>';
							echo '<td>'.$get_name.'</td>';
							echo '</tr>';
						}	
					}
				}
				else
				{
				    echo '<tr>';
					echo '<th scope="row">-</th>';
					echo '<th scope="row">-</th>';
					echo '<th scope="row">-</th>';
					echo '<th scope="row">-</th>';
					echo '</tr>';
				}
				
				?>
			  </tbody>
			</table>	

			<br/>
			<br/>	
			
			<div class="card">
			  <div class="card-header">
				Лог изменений:
			  </div>
			  <div class="card-body">
				<div class="container">
				  <div class="row">
					<div class="col text-center">
						<p>
						  <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Открыть список</a>
						</p>
					</div>
				  </div>
				</div>

				<div class="collapse" id="collapseExample">	
					<p class="card-text">
						<b>18.12.2019</b><br/>
						&bull; Оптимизиция работы обработчика расписания<br/>
						<hr>					
						<b>12.12.2019</b><br/>
						&bull; Исправлена установка времени изменения/добавления расписания<br/>
						(Теперь оно напрямую устанавливается в зависимости добавления файла на облаке, а не в зависимости от текущего времени)
						<hr>
						<b>11.12.2019</b><br/>
						&bull; Добавлена активность групп за 'Сегодня'<br/>
						&bull; Добавлена полная история изменений расписания
					</p>
				</div>					
			  </div>
			</div>	
			
			<br/>

			<h4 align="center">
			<?php
				echo "Последнее изменение обработчика расписания: " . date ("d.m.Y H:i:s", filemtime("/var/www/html/test/handler3.php"));
				echo "<br/>";
				echo "Последнее изменение этой страницы: " . date ("d.m.Y H:i:s", filemtime("/var/www/html/index.php"));
			?>
					
			</h4>			
		</div>	

		<footer class="footer">
			<h4><p class="text-muted" align="center"><a href="https://vk.com/boevx"><b>© Вадим Боев, 2017-2021</b></a></p></h4>
		</footer>			

	</body>
</html>