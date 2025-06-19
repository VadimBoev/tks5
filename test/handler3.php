<?php
	//обработчик расписания, последняя, третья версия
	//План:
	//[2] обработка файлов по очереди
	// как-то ловить название групп
	
	require_once ('PHPExcel/IOFactory.php'); //подключаем библиотеку PHPExcel для чтения
	require_once ('PHPExcel.php'); //подключаем библиотеку
	
	include ("DocX2HtmlParser.php");
	$docx = new DocX2HtmlParser();
	
	$pos = 0;

	include('Net/SSH2.php');
	$ssh = new Net_SSH2('-:22');
	if (!$ssh->login('root', '-')) 
	{
		h_log('Login Failed');
	}
	else
	{
		h_log('Login success');
	}
	
	$connect = mysqli_connect("localhost", "schedule", "-", "rasp"); //коннект к базе
	mysqli_query($connect, 'SET CHARACTER SET "utf8"'); //настроим кодировку
	mysqli_query($connect, 'SET NAMES "utf8"');
	
	$groups = array('Г - 82','Г-83\/84','НЧ-43','НЧ-44\/45','Э-87','Э-88\/89','СТ-91','ОТК-11\/12','ТЭ-16','ТКС-9','ТГ-10','ТА-19','ТА-20','ТА-21',
	'Г-80', 'Г-81','НЧ-41','НЧ-42','Э-85','Э-86','СТ-90','ОТК-10','Р-49','ТЭ-15','ТА-17','ТА-18',
	'ТКС-7','ТГ-9','ТКС-8','ТЭ-12','ТА-12','ТКС-2','НЧ-35','Г-78','Г-79','НЧ-39','НЧ-40','Э-83',
	'Э-84','ОТК-9','СТ-89','Р-48','ТКС-5','ТА-15','ТА-16','ТЭ-14','ТГ-8','ТЭ-13','ТА-13',
	'ТА-14','ТКС-3','ТГ-7','Э-81','Э-82','Г-77','НЧ-37','НЧ-38'); //массив групп
	
	//$groups = array('Г-80', 'Г-81','НЧ-41','НЧ-42','Э-85','Э-86','СТ-90','ОТК-10','Р-49','ТЭ-15','ТА-17',
	//'ТКС-7','ТГ-9','ТКС-8','ТЭ-12','ТА-12','ТКС-2','НЧ-35','Г-78','Г-79','НЧ-39','НЧ-40','Э-83',
	//'Э-84','ОТК-9','СТ-89','Р-48','ТКС-5','ТА-15','ТА-16','ТЭ-14','ТГ-8','ТЭ-13','ТА-13',
	//'ТА-14','ТКС-3','ТКС-4','ТГ-7','Э-81','Э-82','Г-77','НЧ-37','НЧ-38','СТ-88'); //массив групп
	//т.к. его достать автоматически никак, придётся сделать это в ручную.
	
	//================================================================
	/*
		Система действий с расписанием:
		1 - Регистрация файла в системе
		2 - Обновление файла
	
	
	*/
	//================================================================
	
								//я   и другие
	$phone_numbers = array("+79-","+79-","+79-","+79-","+79-","+79-","+79-");
				
	$sms_new = array(array("--","79-"), //данил
				array("--","79-"), //лёха
				array("--","79-"), //макака
				array("--","79-"), //краска
				array("--","79-"), //дима
				array("--","79-")); 				
	
	//убрать все пробелы из названий (до сих пор не сделано)
	
	h_log("Скрипт запущен");
	
	$arr_files = array(); //состояние файлов
	
	//удаляем старые файлы
	$get_old_time = time()-172800; //48 часов
	$get_old_files = mysqli_query($connect, "SELECT * FROM `data` WHERE `time` < '$get_old_time'");
	$old_files_num_rows = mysqli_num_rows($get_old_files);
	if($old_files_num_rows > 0)
	{
		//удаляем, если ответ есть
		h_log("Удаление расписания с системы");
		while ($row = mysqli_fetch_assoc($get_old_files)) 
		{
			$get_name = $row['name'];
			$get_time = $row['time'];
			$get_md5 = $row['md5'];
			
			//$new_filename = str_replace(".docx","",$get_name);

			mysqli_query($connect, "DELETE FROM `data` WHERE `name` = '$get_name'"); //удаляем с data
			mysqli_query($connect, "DROP TABLE `$get_name`"); //удаляем таблицу с расписанием
			
			mysqli_query($connect, "INSERT INTO `black_list`(`name`, `md5`) VALUES ('$get_name','$get_md5')"); //добавляем в черный список
			
			//$ssh->exec("rm /var/www/tks/test/files/'".$new_filename.".docx'"); //удаляем
			//h_log("*Удаление: $new_filename.docx");
			//$ssh->exec("rm /var/www/tks/test/files/'".$new_filename.".html'"); //удаляем
			//h_log("*Удаление: $new_filename.html");
			//$ssh->exec("rm /var/www/tks/test/files/'".$new_filename.".xlsx'"); //удаляем
			//h_log("*Удаление: $new_filename.xlsx");
			
			$ssh->exec("mv /var/www/html/test/files/'".$get_name.".docx' /var/www/html/backup"); //перемещаем в backup
			h_log("Перемещение: $get_name.docx");
			$ssh->exec("mv /var/www/html/test/files/'".$get_name.".html' /var/www/html/backup"); //перемещаем в backup
			h_log("Перемещение: $get_name.html");
			$ssh->exec("mv /var/www/html/test/files/'".$get_name.".xlsx' /var/www/html/backup"); //перемещаем в backup
			h_log("Перемещение: $get_name.xlsx");			
		}
	}
	
	$weblink = ""; //ссылка для скачивания
	$token = ""; //токен
	
	$file_day = "";
	$file_month = "";
	
	//отключаем обязанность использования OpenSSL
	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);  

	$html = file_get_contents("https://cloud.mail.ru/public/-/-", false, stream_context_create($arrContextOptions));

	//$html = file_get_contents('https://cloud.mail.ru/public/-/-');
	h_log("Парсинг страницы cloud.mail.ru...");	
	preg_match('~<script>window\.cloudSettings *= *\{(.+?)\};<\/script>~isu', $html, $matches); 
	$matches = preg_replace("/.ITEM_NAME_INVALID_CHARACTERS.: .(.*?).,/", "", $matches);
	$matches = preg_replace("/.ITEM_PATH_INVALID_CHARACTERS.: .(.*?).,/", "", $matches);
	$matches = preg_replace("/.PROMO_COUNTDOWN.: \{(.*?)\},/s", "", $matches); //вырезаем рекламу которая появляется неожиданно / thx mail ru
	$matches = preg_replace("/.PORTAL_MENU_PROMO.: \{(.*?)\},/s", "", $matches); //еще одна реклама
	$array = json_decode('{'.$matches[1].'}', true);
	
	foreach($array['folders']['folder']['list'] as $value)
	{
		foreach($array['dispatcher']['weblink_get'] as $weblink_get)
		{
			h_log("Ссылка для скачивания: ".$weblink_get['url']);
			$weblink = $weblink_get['url'];
		}
		foreach($array['params']['tokens'] as $token_get)
		{
			h_log("Токен: ".$token_get);
			$token = $token_get;
		}		
		
		h_log("Время: ".$value['mtime']." (Time: ".date("d.m.Y H:i:s",$value['mtime']).") | Название: ".$value['name']." | Вес файла: ".$value['size']." | Хеш-сумма(MD5): ".$value['hash']);
		
		$value['name'] = str_replace(".docx","",$value['name']);
		$new_name = $value['name'];
		
		//надо убедиться что если это не имеет отношение к расписанию то контюн
		if(!is_numeric($new_name[0]))
		{
			continue;
		}
		
		preg_match_all('/\d+/', $new_name, $list_name);
		foreach($list_name as $new_value)
		{
			$file_day = $new_value[0]; //день
			$file_month = $new_value[1]; //месяц
		}
		
		$get_today_day_two = date("d");
		$get_today_day_one = date("j");
		
		$get_today_month_two = date("m");
		$get_today_month_one = date("n");
		
		if($get_today_day_one > $file_day or $get_today_day_two > $file_day)
		{
			//нам не нужны старые даты, потому пропускаем
			
			//
			//if($get_today_month_one < $file_month or $get_today_month_two < $file_month)
			//	continue;
		
			if($get_today_month_one == $file_month or $get_today_month_two == $file_month)
			{
				continue;
			}
		}
		
		if($file_month == "")
		{
			$file_month = "99";
			continue;
		}
		
		$pos = strpos($new_name, "_");
		
		$file_cut = "";
		
		if($pos > 0)
		{
			$file_cut = $file_day."_".$file_month; //фикс против косяков каво
		}
		else
		{
			$file_cut = $file_day.".".$file_month;
		}
		
		$get_return = mysqli_query($connect, "SELECT * FROM `data` WHERE `name` LIKE '%$file_cut%'");
		$files_num_rows = mysqli_num_rows($get_return);
		if($files_num_rows > 0)
		{
			//update если файл с облака новее
			h_log("С базы пришел ответ");
			while ($row = mysqli_fetch_assoc($get_return)) 
			{
				$get_name = $row['name'];
				$get_time = $row['time'];
				$get_md5 = $row['md5'];
				
				h_log("Проверяем строку. Имя: ".$get_name." | Время: ".$get_time." | Хеш: ".$get_md5);
				
				//проверим, совпадает ли хэш
				$get_hash_mysql = mysqli_query($connect, "SELECT * FROM `data` WHERE `md5` = '".$value['hash']."' AND `name` = '".$get_name."'");
				$get_hash_rows = mysqli_num_rows($get_hash_mysql);
				if($get_hash_rows == 0) //если хэш не совпал, обновляем файл
				{
					//пометим в массив arr_files что файл upd
					$arr_files[$value['name']] = "upd";
					
					//старый файл в бэкап
					$ssh->exec("mv /var/www/html/test/files/'".$get_name.".docx' /var/www/html/backup"); //перемещаем в backup
					h_log("Перемещение: $get_name.docx");
					$ssh->exec("mv /var/www/html/test/files/'".$get_name.".html' /var/www/html/backup"); //перемещаем в backup
					h_log("Перемещение: $get_name.html");
					$ssh->exec("mv /var/www/html/test/files/'".$get_name.".xlsx' /var/www/html/backup"); //перемещаем в backup
					h_log("Перемещение: $get_name.xlsx");					
					
					mysqli_query($connect, "UPDATE `data` SET `name`='".$value['name']."',`md5`='".$value['hash']."' WHERE `name` = '".$get_name."'");
					h_log("Хэш-сумма не совпала, обновляем. Имя: ".$value['name']." | Хеш: ".$value['hash']);
					
					mysqli_query($connect, "DROP TABLE `$get_name`"); //удаляем таблицу с расписанием
					
					//$get_last_update_time = time();
					$get_last_update_time = $value['mtime'];
					
					//заносим в журнал действий (обновление)
					mysqli_query($connect, "INSERT INTO `last_action`(`unix_last_time`, `action`, `name`) VALUES ('".$get_last_update_time."','2','".$get_name.".docx')");
			
					$ssh->exec("wget -P /var/www/html/test/files $weblink/-/-/'".$value['name'].".docx'?key=$token --restrict-file-names=nocontrol");
					h_log("Качаю файл $weblink/-/-/'".$value['name'].".docx'?key=$token");
					$ssh->exec("mv /var/www/html/test/files/'".$value['name'].".docx'?key=$token /var/www/html/test/files/'".$value['name'].".docx'");	
					h_log("Переименовываю файл /var/www/html/test/files/'".$value['name'].".docx'?key=$token /var/www/html/test/files/".$value['name'].".docx");			
				}
			}
		}
		else //иначе если 0, регистрируем файл в системе
		{
			//проверяем наличием файла в черном списке...
			$get_black_list = mysqli_query($connect, "SELECT * FROM `black_list` WHERE `md5` = '".$value['hash']."' AND `name` = '".$value['name']."'");
			$get_black_list_rows = mysqli_num_rows($get_black_list);
			if($get_black_list_rows == 0)
			{
				$arr_files[$value['name']] = "reg";
				
				//задаём время исходя из названия файла
				//нужно будет додумать начало и конец месяца...
				$get_year = date("Y",time());
				
				$time_new = 0;
				
				if($pos > 0)
				{
					//заменить _ на точку
					$string = str_replace("_", ".", $file_cut); //заменяем косяки азаровой
					$time_new = strtotime($string.".".$get_year);
				}
				else
				{
					$time_new = strtotime($file_cut.".".$get_year);
				}
				
				h_log("С базы ответ не пришел");
				mysqli_query($connect, "INSERT INTO `data`(`name`, `time`, `md5`) VALUES ('".$value['name']."','".$time_new."','".$value['hash']."')");
				h_log("Создаём строку в БД, с параметрами. Имя: ".$value['name']." | Время: ".$time_new." | Хеш: ".$value['hash']);
				
				//$get_last_reg_time = time();
				$get_last_reg_time = $value['mtime'];
					
				//заносим в журнал действий (регистрация)
				mysqli_query($connect, "INSERT INTO `last_action`(`unix_last_time`, `action`, `name`) VALUES ('".$get_last_reg_time."','1','".$value['name'].".docx')");				
			
				//качаем файл и переименовываем в нормальный
				$ssh->exec("wget -P /var/www/html/test/files $weblink/-/-/'".$value['name'].".docx'?key=$token --restrict-file-names=nocontrol");
				h_log("Качаю файл $weblink/-/-/'".$value['name'].".docx'?key=$token");
				$ssh->exec("mv /var/www/html/test/files/'".$value['name'].".docx'?key=$token /var/www/html/test/files/'".$value['name'].".docx'");	
				h_log("Переименовываю файл /var/www/html/test/files/'".$value['name'].".docx'?key=$token /var/www/html/test/files/".$value['name'].".docx");	
			
				//потом, если файл обновляем, и появилась база или скачался файл экселя - удаляем старую базу и всё такое
			}
			else
			{
				h_log("Файл ".$value['name']." с md5: ".$value['hash']." занесён в чёрный список, действий с ним проводиться не будет");
			}
		}	
	}
	
	$filelist = glob("/var/www/html/test/files/*.docx"); //конвертим из DOCX в HTML
	foreach ($filelist as $filename)
	{
		$new_filename = str_replace("/var/www/html/test/files/","",$filename);
		$new_filename = str_replace(".docx","",$new_filename);
		
		if(array_key_exists($new_filename, $arr_files))
		{
			//$ssh->exec("cd /var/www/tks/test/files/ && soffice --convert-to \"html:XHTML Writer File:UTF8\" '$filename'");
			$docx->setFile("$filename");
			$html = "";
			$html .= '<html><head><meta charset="utf-8"></head><body>';
			$html .= $docx->toHtml();
			$html .= '</body></html>';
			
			$fp = fopen("/var/www/html/test/files/".$new_filename.".html", "a");
			fputs($fp, $html);
			fclose($fp);
			
			h_log("[UPD] Конвертирую файл DOCX->HTML '$filename'");
		}	
	}
	
	$filelist = glob("/var/www/html/test/files/*.html"); //конвертим из HTML в XLSX
	foreach ($filelist as $filename)
	{
		$new_filename = str_replace("/var/www/html/test/files/","",$filename);
		$new_filename = str_replace(".html","",$new_filename);
		
		if(array_key_exists($new_filename, $arr_files))
		{
			$not_html = preg_replace("/\.html/","",$filename);
					
			$inputFileType = 'HTML';
			$inputFileName = "$filename";
			$outputFileType = 'Excel2007';
			$outputFileName = "$not_html.xlsx";
			
			$ssh->exec("chmod 666 '$filename'"); //устанавливаем файлу права на запись
			h_log("Устанавливаю права 666 (чтение и запись) файлу $filename");
			
			$file = file_get_contents($filename);
			$get_name_for_file = basename($filename, ".html");
			$file = str_replace("- no title specified", "$get_name_for_file", $file); //заменяем
			file_put_contents($filename, $file);

			$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objPHPExcelReader->load($inputFileName);

			$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
			$objPHPExcel = $objPHPExcelWriter->save($outputFileName);
			h_log("[REG] Сохраняю файл в XLSX");
		}	
	}	

	$filelist = glob("/var/www/html/test/files/*.xlsx");
	foreach ($filelist as $filename)
	{
		$new_filename = str_replace("/var/www/html/test/files/","",$filename);
		$new_filename = str_replace(".xlsx","",$new_filename);
		
		if(array_key_exists($new_filename, $arr_files))
		{		
			$ssh->exec("chmod 666 '$filename'"); //устанавливаем файлу права на запись
			h_log("[REG] Устанавливаю права 666 (чтение и запись) файлу $filename");		
		}	
	}
	
	$filelist = glob("/var/www/html/test/files/*.xlsx");
	foreach ($filelist as $filename)
	{	
		$new_filename = str_replace("/var/www/html/test/files/","",$filename);
		$new_filename = str_replace(".xlsx","",$new_filename);
		
		if(array_key_exists($new_filename, $arr_files))
		{	
			$excel = PHPExcel_IOFactory::createReader('Excel2007');
			$excel = $excel->load($filename); // загрузка файла
			$excel->setActiveSheetIndex(0); //установим первую активную таблицу
			$sheet = $excel->getActiveSheet(); //получаем активную таблицу
			$rowIterator = $sheet->getRowIterator(); //получить текущий ряд (?) 
			$all_merged_cells = $excel->getActiveSheet()->getMergeCells(); //получаем объединённые ячейки
			foreach ($rowIterator as $row) //перебираем текущий ряд (?) (строку)
			{
				$cellIterator = $row->getCellIterator();
						
				foreach ($cellIterator as $cell) //перебираем ячейки
				{
					$get_value = $cell->getCoordinate(); //получаем координаты
					$cellValue = $excel->getActiveSheet()->getCell("$get_value")->getCalculatedValue(); //получаем значение
					$get_row = $excel->getActiveSheet()->getCell("$get_value")->getRow(); //получаем номер строки
					$get_column = $excel->getActiveSheet()->getCell("$get_value")->getColumn(); //получаем столбец
					
					preg_match("/пара/",$cellValue,$arr);
					if(isset($arr[0]))
					{
						$get_number_row = $get_row;
							
						for($i=1; $i<=15; $i++)
						{
							$get_value_for_new = $excel->getActiveSheet()->getCellByColumnAndRow($i, $get_row)->getValue();
							$get_value_for_new = preg_replace('/\s/', '', $get_value_for_new);
							$excel->getActiveSheet()->setCellValueByColumnAndRow($i, $get_number_row, $get_value_for_new);
						}
					}				
				}			
			}
			$objExcelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$objWriter = $objExcelWriter->save($filename);
			h_log("Пробелы из $filename удалены, сохраняю файл XLSX");
		}
	}
	
	$filelist = glob("/var/www/html/test/files/*.xlsx");
	foreach ($filelist as $filename)
	{
		//тута удаляем лишнее, оставляем только название файла без формата
		$new_filename = str_replace("/var/www/html/test/files/","",$filename);
		$new_filename = str_replace(".xlsx","",$new_filename);
		//и тут же создаём таблицу в БД с названием и т.д.
		//если имя такое есть в массиве регистрации файлов, то создаём таблицу
		
		//цикл вытаскивает каждый файл из папки, мы ищем это в регистрации или в обновлении
		//где нашли там и делаем действия
		//обновление - вытаскиваем каждую ячейку из бд и сверяем с той, что в экселе (не никуя)
		//регистрация - создаём новую таблицу, и заносим новые данные как это уже сделано ниже		
		
		if(array_key_exists($new_filename, $arr_files))
		{
			mysqli_query($connect, "CREATE TABLE `rasp`.`$new_filename` ( `id` INT NOT NULL AUTO_INCREMENT , `group_name` VARCHAR(32) NOT NULL , `para1` TEXT NOT NULL , `para2` TEXT NOT NULL , `para3` TEXT NOT NULL , `para4` TEXT NOT NULL , `para5` TEXT NOT NULL , `para6` TEXT NOT NULL , `para7` TEXT NOT NULL , `para8` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
			h_log("Отправляю запрос на создание таблицы $new_filename в БД");		
			
			$xls = PHPExcel_IOFactory::load($filename);
			$xls->setActiveSheetIndex(0); //устанавливаем активную таблицу
			$sheet = $xls->getActiveSheet(); //получаем активную таблицу
			$rowIterator = $sheet->getRowIterator(); //получить текущий ряд (?) 
			$all_merged_cells = $xls->getActiveSheet()->getMergeCells(); //получаем объединённые ячейки
			foreach ($rowIterator as $row) //перебираем текущий ряд (?) (строку)
			{
				// Получили ячейки текущей строки и обойдем их в цикле
				$cellIterator = $row->getCellIterator();
						
				foreach ($cellIterator as $cell) //перебираем ячейки
				{
					$get_value = $cell->getCoordinate(); //получаем координаты
					$cellValue = $xls->getActiveSheet()->getCell("$get_value")->getCalculatedValue(); //получаем значение
					$get_row = $xls->getActiveSheet()->getCell("$get_value")->getRow(); //получаем номер строки
					$get_column = $xls->getActiveSheet()->getCell("$get_value")->getColumn(); //получаем столбец
					
					foreach($groups as $group)
					{
						preg_match("/$group/",$cellValue,$arr);
						if(isset($arr[0]))
						{
							//echo $cellValue."<br>";
							//Ниже цикл от 1 до 8, в котором мы ловим
							//номер строки, и переходим на столбец A:номерстроки+номер цикла
							//и получаем значение, если это число - получаем расписание группы по номеру строки
							//если это имя ломаем цикл
							$send_message = ""; //переменная для отправки данных
							
							mysqli_query($connect, "INSERT INTO `$new_filename`(`group_name`) VALUES ('$cellValue')");
							//h_log("Отправляю запрос на добавление строки в БД, где имя группы: $cellValue");
							$send_message .= "$new_filename %0a";
							$update_string = "UPDATE `$new_filename` SET ";
							
							//$name_column = "(`group_name`,";
							//$name_value = "('$cellValue',";
							
							for($i=1; $i<=8; $i++)
							{
								$get_new_row = $get_row + $i;
								$get_number = $xls->getActiveSheet()->getCell("A$get_new_row")->getValue(); //получаем номер строки исходя из кол-ва пар
								$text = $xls->getActiveSheet()->getCell("$get_column$get_new_row")->getCalculatedValue()."\n"; //возвращаемся
								//к нашему "счётчику" ячеек который берёт значение, и продолжаем

								$get_number = preg_replace('/\s+/', '', $get_number); //чтобы скрипт не ругался что мол
								//пробелы это string, и не мешали определить что число - это число
								
								$merged_value = null;
								
								if(is_numeric($get_number))
								{
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
									//$value = strlen($merged_value) == 0 ? $cell->getValue() : $merged_value;	
									$value = strlen($merged_value) == 0 ? $text : $merged_value;					
										
									//echo $get_number.". ".$value."<br>"; //4 раза выводит ткс-5 т.к. $cell находится в цикле ну понятно что это не координаты
									
									//mysqli_query($connect, "UPDATE `$new_filename` SET `para$i` = '$value' WHERE `group_name` = '$cellValue'");
									$update_string .= "`para$i` = '$value', ";
									//$name_column .= "`para$i`,";
									//$name_value .= "'$value',";
									
									//h_log("Отправляю запрос на обновление строки в БД, где имя группы: $cellValue, обновляем `para$i` = '$value'"); //жрёт много логов
									//h_log("Отправляю запрос на обновление строки в БД, где имя группы: $cellValue, обновляем `para$i`");
									$send_message .= $get_number.". ".$value."%0a";
								}
								else
								{
									break;
								}
							}
							//отправляем подготовленный запрос в бд
							$update_string .= "WHERE `group_name` = '$cellValue'";
							$get_pos = strpos($update_string, 'W',0);
							$update_string = substr_replace($update_string, '', $get_pos-2,1);
							
							mysqli_query($connect, "$update_string");
							
							//====================================================================================
							//что такое
							
							//h_log("$update_string");
							
							//$name_column .= ")";
							//$name_value .= ")";	
							
							//$get_pos_column = strpos($name_column, ')',0);
							//$name_column = substr_replace($name_column, '', $get_pos_column-1,1);
							//$get_pos_value = strpos($name_value, ')',0);
							//$name_value = substr_replace($name_value, '', $get_pos_value-1,1);
							
							//mysqli_query($connect, "INSERT INTO `$cellValue` $name_column VALUES $name_value");
							//h_log("INSERT INTO `$new_filename` $name_column VALUES $name_value");
							//=====================================================================================
							
							if($group == "ТКС-5")
							{
								$send_message = str_replace("<br>","",$send_message);
								//$send_message = str_replace(array("r","n"),"",$send_message);
								$send_message = str_replace(array("\r","\n"),"",$send_message);
								$send_message = urldecode(urlencode($send_message));
				
								//остальное
								foreach($sms_new as $new)
								{
									$str = translit($send_message);
									$str = str_replace("  ", " ", $str);//много пробелов в один
									$str = str_replace(" ", "+", $str); //пробелы на плюсы (стандарт смс)
									$str = substr($str, 0, 155); //обрезаем
									
									//отключаем обязанность использования OpenSSL
									$arrContextOptions1=array(
										"ssl"=>array(
											"verify_peer"=>false,
											"verify_peer_name"=>false,
										),
									);  

									$body = file_get_contents("https://sms.ru/sms/send?api_id=".$new[0]."&to=".$new[1]."&msg=$str&json=1", false, stream_context_create($arrContextOptions1));									
									
									//$body = file_get_contents("https://sms.ru/sms/send?api_id=".$new[0]."&to=".$new[1]."&msg=$str&json=1");
									$json = json_decode($body);
									h_log("Ссылка: https://sms.ru/sms/send?api_id=".$new[0]."&to=".$new[1]."&msg=$str&json=1");
									//h_log($json);
								}
								
								$send_message = preg_replace('/%0a/', '<br>', $send_message);
								
								$curl = curl_init();
								curl_setopt_array($curl, array(
									CURLOPT_USERAGENT => 'KateMobileAndroid/48.2 lite-433 (Android 8.1.0; SDK 27; arm64-v8a; Google Pixel 2 XL; en)',
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_SSL_VERIFYPEER => false,
									CURLOPT_POST => true,
									CURLOPT_POSTFIELDS => array("peer_id" => "2000000003", "message" => $send_message, "access_token" => "-", "v" => "5.78"),
									CURLOPT_URL => "https://api.vk.com/method/messages.send"
								));
								$response = (array)json_decode(curl_exec($curl), true);
								curl_close($curl);

								h_log("Отправляем сообщение в беседу ТКС-5");	
							}							
							
							//====================================================================================================
							if($group == "НЧ-38")
							{
								$send_message = str_replace("<br>","",$send_message);
								//$send_message = str_replace(array("r","n"),"",$send_message); 
								$send_message = str_replace(array("\r","\n"),"",$send_message);
								$send_message = urldecode(urlencode($send_message));					
								
								$send_message = preg_replace('/%0a/', '<br>', $send_message);
								
								/*$curl = curl_init();
								curl_setopt_array($curl, array(
									CURLOPT_USERAGENT => 'KateMobileAndroid/48.2 lite-433 (Android 8.1.0; SDK 27; arm64-v8a; Google Pixel 2 XL; en)',
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_SSL_VERIFYPEER => false,
									CURLOPT_POST => true,
									CURLOPT_POSTFIELDS => array("peer_id" => "2000000373", "message" => $send_message, "access_token" => "-", "v" => "5.78"),
									CURLOPT_URL => "https://api.vk.com/method/messages.send"
								));
								$response = (array)json_decode(curl_exec($curl), true);
								curl_close($curl);*/

								//h_log("Отправляем сообщение в беседу");	
							}
						}	
					}				
				}
			}
		}
	}
	
	h_log("Скрипт завершил свою работу");
	h_log("---------------------------");
	
	
	function h_log($text) 
	{
		$date = date("d.m.Y H:i:s",time());
		$fp = fopen("log.txt", "a");
		fputs($fp, "[$date] ".$text."\n");
		fclose($fp);	
    }
	
	function translit($stre) 
	{
		$rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'J', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'j', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
		return str_replace($rus, $lat, $stre);
	}
	
	function phone_number($number)
	{
		$a = substr_replace($number, "%28", 2, 0);
		$a = substr_replace($a, "%29+", 8, 0);
		$a = substr_replace($a, "-", 15, 0);
		$a = substr_replace($a, "%2B", 0, 1);
		return $a;
	}
	
?>