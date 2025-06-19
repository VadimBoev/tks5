<?php
	//$json_array = array();
	//include('Net/SSH2.php');
	//$ssh = new Net_SSH2('185.125.231.170:2233');
	//$ssh->login('php_dostup', '25482548');		
	
	require_once ('PHPExcel/IOFactory.php');
	
	$connect = mysqli_connect("localhost", "schedule", "-", "rasp");
	mysqli_query($connect, 'SET CHARACTER SET "utf8"');
	mysqli_query($connect, 'SET NAMES "utf8"');
	
	$groups = array('Г-80', 'Г-81','НЧ-41','НЧ-42','Э-85','Э-86','СТ-90','ОТК-10','Р-49','ТЭ-15','ТА-17',
	'ТКС-7','ТГ-9','ТКС-8','ТЭ-12','ТА-12','ТКС-2','НЧ-35','Г-78','Г-79','НЧ-39','НЧ-40','Э-83',
	'Э-84','ОТК-9','СТ-89','Р-48','ТКС-5','ТКС-6','ТА-15','ТА-16','ТЭ-14','ТГ-8','ТЭ-13','ТА-13',
	'ТА-14','ТКС-3','ТКС-4','ТГ-7','Э-81','Э-82','Г-77','НЧ-37','НЧ-38','СТ-88');
	
	//$f = fopen('bot.json', 'w');
	//fclose($f);
	
	//$filelist = glob("*.docx");
	//foreach ($filelist as $filename)
	//{
	//	$ssh->exec("soffice --convert-to \"html:XHTML Writer File:UTF8\" /var/www/html/rasp/'$filename'");
	//}
	
	/*$filelist = glob("*.html");
	foreach ($filelist as $filename)
	{
		$not_html = preg_replace("/\.html/","",$filename);
		$inputFileType = 'HTML';
		$inputFileName = "./$filename";
		$outputFileType = 'Excel2007';
		$outputFileName = "./$not_html.xlsx";

		$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);

		$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
		$objPHPExcel = $objPHPExcelWriter->save($outputFileName);
		
		$ssh->exec("rm /var/www/html/rasp/'$filename'");
	}*/
	
	/*$filelist = glob("bot/*.txt");
	foreach ($filelist as $filename)
	{
		$ssh->exec("rm /var/www/html/rasp/'$filename'");
	}*/

	/*$check = false;
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
					$fp = fopen("$file_new.txt", "a");
					fputs($fp, $name);
					fclose($fp);
					
					for($i=1; $i<=5; $i++)
					{
						$get_new_row = $get_row + $i;
						$text = $xls->getActiveSheet()->getCell("A$get_new_row")->getCalculatedValue()." ";
						$text1 = $xls->getActiveSheet()->getCell("$get_column$get_new_row")->getCalculatedValue()."\n";
						
						$fp = fopen("$file_new.txt", "a");
						fputs($fp, $text.$text1);
						fclose($fp);					
					}
					$json_array[] = $file_new.".txt";
				}
			}
		}
	}
	$result = array_unique($json_array);
	$fp = fopen("bot.json", "a");
	fputs($fp, json_encode($result,JSON_UNESCAPED_UNICODE));
	fclose($fp);
	require_once ('PHPExcel/IOFactory.php');*/
	
	$xls = PHPExcel_IOFactory::load("03.09.18.xlsx");
	$xls->setActiveSheetIndex(0);
	$sheet = $xls->getActiveSheet();
	$rowIterator = $sheet->getRowIterator();
	$all_merged_cells = $xls->getActiveSheet()->getMergeCells();
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
			preg_match("/ТКС-3/",$cellValue,$arr);
			
			if(isset($arr[0]))
			{
				$news = "";
				echo $cellValue."<br>";
				for($i=1; $i<=5; $i++)
				{
					$get_new_row = $get_row + $i;
					$text1 = $xls->getActiveSheet()->getCell("$get_column$get_new_row")->getCalculatedValue()."\n";
					$merged_value = null;
					
                    foreach ($all_merged_cells as $merged_cells) 
					{
                        // Если текущая ячейка - объединенная,
                        if ($cell->isInRange($merged_cells)) 
						{
                            // то вычисляем значение первой объединенной ячейки, и используем её в качестве значения текущей ячейки
                            $merged_value = explode(":", $merged_cells);
                            $merged_value = $xls->getActiveSheet()->getCell($merged_value[0])->getValue();
                            break;
                        }
                    }
                    // Проверяем, что ячейка не объединенная: если нет, то берем ее значение, иначе значение первой объединенной ячейки
                    $value = strlen($merged_value) == 0 ? $cell->getValue() : $merged_value;					
					
					echo $value."<br>"; //4 раза выводит ткс-5 т.к. $cell находится в цикле ну понятно что это не координаты
					
					//echo $text1."<br>";
					$news = $news.$text1;
				}
				//mysqli_query($connect, "INSERT INTO `data`(`tks5`) VALUES ('$news')");
			}			
			/*foreach($groups as $value)
			{
				preg_match("/$value/",$cellValue,$arr);
				if(isset($arr[0]))
				{
					echo $cellValue."<br>";
					for($i=1; $i<=8; $i++)
					{
						$get_new_row = $get_row + $i;
						$text = $xls->getActiveSheet()->getCell("A$get_new_row")->getCalculatedValue()." ";
						$text1 = $xls->getActiveSheet()->getCell("$get_column$get_new_row")->getCalculatedValue()."\n";
						foreach($groups as $check_str)
						{
							preg_match("/$check_str/",$text,$arr_check);
							if($arr_check($arr[0]))
							{
								break;
							}
							else
							{
								echo $text.$text1."<br>";
							}
						} //это говно не будет работать нужна подумать
						//если мы словили название группы, ловим номер строки, и смотрим A +1 к нашему номеру строки
						//если это число - получаем значение под столбиком нашей группы
						//ну и обрабатываем как нам надо :)
						//занос данных самостоятелен, запрос подобия 
//CREATE TABLE `rasp`.`data` ( `id` INT NOT NULL AUTO_INCREMENT , `tks5` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
						echo $text.$text1."<br>";
					}
				}				
			}*/
		}
	}

?>