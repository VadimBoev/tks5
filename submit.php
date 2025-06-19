<?php
	include("db.php");
	//$output = '';
	if(isset($_POST))
	{
		$date = $_POST['date'];
		$group = $_POST['selected'];
		
		$db_day = date("j");
		$db_month = date("m");
		$db_year = date("Y");
		
		$db_hour = date("H");
		$db_minute = date("i");
		$db_second = date("s");
		
		if($group != NULL or $group != "")
		{
			mysqli_query($connect, "INSERT INTO `activity`(`day`, `month`, `year`, `hour`, `minute`, `second`, `name`, `date`) VALUES ('$db_day','$db_month','$db_year','$db_hour','$db_minute','$db_second','$group','$date')");
		}
		
		$query = mysqli_query($connect, "SELECT `para1`,`para2`,`para3`,`para4`,`para5`,`para6`,`para7`,`para8` FROM `$date` WHERE `group_name`='$group'");
		$object1 = mysqli_fetch_object($query);				

		echo '<div class="container" style="overflow-x: auto;">';	
		echo '<table class="table table-hover">';
		
		$count = mysqli_num_rows($query);
		if( $date != '' ) //существует $query ???
		{					
			if($group != "" and $count > 0)
			{
				if (mb_stripos($date, 'суб') !== false)
				{
					echo "<tr>
					<th> № </th>
					<th> Время </th>
					<th> Дисциплина </th>
					<tbody>
					<td> 1 </td>
					<td> 08:30-09:50 </td>
					<td> $object1->para1 </td>	
					</tbody>
					<tbody>
					<td> 2 </td>
					<td> 10:00-11:20 </td>
					<td> $object1->para2 </td>
					</tbody>
					<tbody>
					<td> 3 </td>
					<td> 11:30-12:50 </td>
					<td> $object1->para3 </td>
					</tbody>
					<tbody>
					<td> 4 </td>
					<td> 13:00-14:20 </td>
					<td> $object1->para4 </td>
					</tbody>
					<tbody>
					<td> 5 </td>
					<td> 14:30-15:50 </td>
					<td> $object1->para5 </td>
					</tbody>
					<tbody>
					<td> 6 </td>
					<td> 16:00-17:20 </td>
					<td> $object1->para6 </td>
					</tbody>
					<tbody>
					<td> 7 </td>
					<td> 17:30-18:50 </td>
					<td> $object1->para7 </td>
					</tbody>
					<tbody>
					<td> 8 </td>
					<td> 19:00-20:20 </td>
					<td> $object1->para8 </td>
					</tbody>									
					</tr>";
				}
				else
				{
					echo "<tr>
					<th> № </th>
					<th> Время </th>
					<th> Дисциплина </th>
					<tbody>
					<td> 1 </td>
					<td> 08:30-10:00 </td>
					<td> $object1->para1 </td>	
					</tbody>
					<tbody>
					<td> 2 </td>
					<td> 10:10-11:40 </td>
					<td> $object1->para2 </td>
					</tbody>
					<tbody>
					<td> 3 </td>
					<td> 12:00-13:30 </td>
					<td> $object1->para3 </td>
					</tbody>
					<tbody>
					<td> 4 </td>
					<td> 13:40-15:10 </td>
					<td> $object1->para4 </td>
					</tbody>
					<tbody>
					<td> 5 </td>
					<td> 15:20-16:50 </td>
					<td> $object1->para5 </td>
					</tbody>
					<tbody>
					<td> 6 </td>
					<td> 17:00-18:30 </td>
					<td> $object1->para6 </td>
					</tbody>
					<tbody>
					<td> 7 </td>
					<td> 18:40-20:10 </td>
					<td> $object1->para7 </td>
					</tbody>
					<tbody>
					<td> 8 </td>
					<td> 20:20-21:50 </td>
					<td> $object1->para8 </td>
					</tbody>									
					</tr>";
				}
			}
			else
			{
				echo '<h5><div class="text-center alert alert-danger" role="alert">Выберите группу</div></h5>';
			}
		}
		else
		{
			echo '<h5><div class="text-center alert alert-danger" role="alert">Выберите дату</div></h5>';
		}
		echo '</table>';		
	
	}
?>