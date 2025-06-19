<?php
	include("db.php");
	$output = '';
	if(isset($_POST))
	{
		$result = mysqli_query($connect, "SHOW TABLES FROM `rasp`");
		$count_result = mysqli_num_rows($result);
		if ($count_result > 0)
		{
			while($object = mysqli_fetch_row($result))
			{
				if($object[0] != "black_list" and $object[0] != "data" and $object[0] != "last_action" and $object[0] != "activity")
				{	
					$output .= "<option value = '$object[0]' > {$object[0]} </option>";
				}
			}
		}
		echo $output;
	}
?>