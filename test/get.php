<?php
	require_once ('PHPExcel/IOFactory.php');
	include('Net/SSH2.php');
	$ssh = new Net_SSH2('-:2233');
	$ssh->login('php_dostup', '12345678');		
	
	$warning = 0;
	
	$fix_utf = "\xEF\xBB\xBF";
	
	$result = '';
	$token = file_get_contents('https://cloud.mail.ru/api/v2/tokens/download');
	$obj = json_decode($token);
	foreach($obj as $value)
	{
		$result .= print_r($value->{'token'},true);
	}
	
	$dispatcher = file_get_contents("https://cloud.mail.ru/api/v2/dispatcher");
	$result_disp = '';
	$object = json_decode($dispatcher);
	foreach($object as $value1)
	{
			$result_disp .= print_r($value1->{'weblink_get'}[0]->url,true);
	}	
	
	$get_weblink = array();
	$get_size = array();
	$get_time = array();
	$lines = file('https://cloud.mail.ru/public/-/-');
	foreach ($lines as $line_num => $line) 
	{
		$test = htmlspecialchars($line);
		preg_match('~-/-/.*?.docx~SDs', $test, $matches);
		if($matches != NULL)
		{
			foreach($matches as $value2)
			{
				$value3 = preg_replace('/ /', '%20', $value2);
				$value4 = preg_replace('/\(/', '%28', $value3);
				$value5 = preg_replace('/\)/', '%29', $value4);
				$get_weblink[] = $value5;
			}
		}	
	}
	
	$result1 = array_unique($get_weblink);
		
	$filelist = glob("*.docx");
	foreach ($filelist as $filename)
	{
		$ssh->exec("rm /var/www/html/rasp/'$filename'");
	}
	$filelist2 = glob("*.xlsx");
	foreach ($filelist2 as $filename)
	{
		$ssh->exec("rm /var/www/html/rasp/'$filename'");
	}	
	
	$filelist3 = glob("*.html");
	foreach ($filelist3 as $filename)
	{
		$ssh->exec("rm /var/www/html/rasp/'$filename'");
	}	
		
	foreach($result1 as $val)
	{
		//$ssh->exec("wget -P /var/www/html/rasp $result_disp/'$val'?key=$result");
			
		//$new = preg_replace("/-\/-\//","",$val);
		//$ssh->exec("mv /var/www/html/rasp/'$new'?key=$result /var/www/html/rasp/'$new'");
		//echo "'$val'?key=$result | '$new'<br>";
		
		//echo "<a class='lead' href='$val' target='_blank'>$val</a><br>";
		
		//$ssh->exec("rm /var/www/html/rasp/'$val'?key=$result"); //удаляем дубликат, хз откуда он, ну да ладно
		
		$get_chars = strlen($val);
		$tet = substr($val, 15, $get_char-5);
		if(strpos($tet, '%20'))
		{
			$warning = 1;
		}
			
		if($warning == 1)
		{
			//$ssh->exec("wget -P /var/www/html/rasp $result_disp/$val?key=$result");
			$ssh->exec("wget -P /var/www/html/rasp $result_disp/'$val'?key=$result");
			
			$tet = preg_replace("/%20/", "\ ", $tet);
			$ssh->exec("mv /var/www/html/rasp/$tet.docx?key=$result /var/www/html/rasp/$tet.docx");			
			$warning = 0;
		}
		else
		{
			$ssh->exec("wget -P /var/www/html/rasp $result_disp/'$val'?key=$result");
			$ssh->exec("mv /var/www/html/rasp/$tet.docx?key=$result /var/www/html/rasp/$tet.docx");	
		}
		echo "<a class='lead' href='$tet.docx' target='_blank'>$tet</a><br>";		
	}
	
	//ниже уже обработчик
	
	$f = fopen('bot.json', 'w');
	fclose($f);
	
	$filelist = glob("*.docx");
	foreach ($filelist as $filename)
	{
		$ssh->exec("soffice --convert-to \"html:XHTML Writer File:UTF8\" /var/www/html/rasp/'$filename'");
	}
	
	$filelist = glob("*.html");
	foreach ($filelist as $filename)
	{
		$not_html = preg_replace("/\.html/","",$filename);
		$inputFileType = 'HTML';
		$inputFileName = "./$filename";
		$outputFileType = 'Excel2007';
		$outputFileName = "./$not_html.xlsx";
		
		$ssh->exec("chmod 666 /var/www/html/rasp/'$filename'"); //устанавливаем файлу права на запись
		
		$file = file_get_contents($filename);
		$get_name_for_file = basename($filename, ".html");
		$file = str_replace("- no title specified", "$get_name_for_file", $file); //заменяем
		file_put_contents($filename, $file);

		$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);

		$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
		$objPHPExcel = $objPHPExcelWriter->save($outputFileName);
	}
	
	$filelist = glob("bot/*.txt");
	foreach ($filelist as $filename)
	{
		$ssh->exec("rm /var/www/html/rasp/'$filename'");
	}	

	$check = false;
	$filelist = glob("*.xlsx");
	foreach ($filelist as $filename)
	{
		$xls = PHPExcel_IOFactory::load("$filename");
		$file_new = preg_replace("/\.xlsx/","",$filename);
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		$check = false;
		$rowIterator = $sheet->getRowIterator();
		foreach ($rowIterator as $row)
		{
			// Получили ячейки текущей строки и обойдем их в цикле
			$cellIterator = $row->getCellIterator();
				
			foreach ($cellIterator as $cell) 
			{
				$get_value = $cell->getCoordinate(); //получаем координаты
				$cellValue = $xls->getActiveSheet()->getCell("$get_value")->getCalculatedValue(); //получаем значение
				$get_row = $xls->getActiveSheet()->getCell("$get_value")->getRow(); //получаем номер строки
				$get_column = $xls->getActiveSheet()->getCell("$get_value")->getColumn(); //получаем столбец
				preg_match("/1ТКС-5/",$cellValue,$arr);
				if(isset($arr[0]))
				{
			
					$name = "Группа: $arr[0]\n";
					$text = "";
					$fp = fopen("bot/$file_new.txt", "a");
					fputs($fp, $fix_utf.$name);
					fclose($fp);
					
					for($i=1; $i<=5; $i++)
					{
						$get_new_row = $get_row + $i;
						$text = $xls->getActiveSheet()->getCell("A$get_new_row")->getCalculatedValue()." ";
						$text1 = $xls->getActiveSheet()->getCell("$get_column$get_new_row")->getCalculatedValue()."\n";
						
						$fp = fopen("bot/$file_new.txt", "a");
						fputs($fp, $fix_utf.$text.$text1);
						fclose($fp);					
					}
					$json_array[] = $file_new.".txt";
				}
			}
		}
	}
	$result4 = array_unique($json_array);
	$fp4 = fopen("bot.json", "a");
	fputs($fp4, json_encode($result4,JSON_UNESCAPED_UNICODE));
	fclose($fp4);
	
?>	