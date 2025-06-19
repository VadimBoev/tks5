<?php
	while(true)
	{
		$get_date = date("G",time());
		if($get_date >= 3 and $get_date <= 23)
		{
			exec('php -f handler3.php > /dev/null 2>&1 &');
			//sleep(300); //5 минут
			//sleep(120); //2 минуты
			sleep(60); //1 минутa
		}
		else //иначе спит 20 минут
		{
			sleep(1200); //20 минут
			h_log("Время по МСК: ".$get_date);
		}
	}
	function h_log($text) 
	{
		$date = date("d.m.Y H:i:s",time());
		$fp = fopen("cycle.txt", "a");
		fputs($fp, "[$date] ".$text."\n");
		fclose($fp);	
    }	
	
?>