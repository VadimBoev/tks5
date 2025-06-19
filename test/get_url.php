<?php
	
	$get_minute = date("i");
	if($get_minute == "0" or $get_minute == "15" or $get_minute == "30" or $get_minute == "45")
	{
		echo "Упс время не то";
	}
	else
	{
		echo "хорошее время";
	}
?>