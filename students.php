<!doctype html>
<html lang="ru">
	<head>
		<meta charset="utf-8">	
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="css/studsovet.css">
		<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
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
		  <div class="card-deck mb-3">
			<div class="card mb-4">
				<select id="date_select" name="date_select" class="selectpicker form-control" data-title="Дата"></select>
			</div>
			<div class="card mb-4">
				<select id="group" name="group" class="selectpicker form-control" data-title="Группа" data-live-search="true" data-size="5" data-live-search-placeholder="Группа"></select>
			</div>
			<div class="card mb-4">
				<button class="btn btn-primary form-control" name="enter" id="enter">Показать</button>	
			</div>
		  </div>
		</div>  
		
		<div id="result"></div>			
		
	</body>  
</html>  

<script>
var _0x5439=["\x50\x4F\x53\x54","\x67\x65\x74\x5F\x64\x61\x74\x65\x2E\x70\x68\x70","","\x72\x65\x66\x72\x65\x73\x68","\x73\x65\x6C\x65\x63\x74\x70\x69\x63\x6B\x65\x72","\x68\x74\x6D\x6C","\x23\x64\x61\x74\x65\x5F\x73\x65\x6C\x65\x63\x74","\x61\x6A\x61\x78","\x76\x61\x6C","\x66\x65\x74\x63\x68\x2E\x70\x68\x70","\x23\x67\x72\x6F\x75\x70","\x63\x68\x61\x6E\x67\x65","\x73\x75\x62\x6D\x69\x74\x2E\x70\x68\x70","\x23\x72\x65\x73\x75\x6C\x74","\x63\x6C\x69\x63\x6B","\x23\x65\x6E\x74\x65\x72","\x72\x65\x61\x64\x79"];$(document)[_0x5439[16]](function(){$[_0x5439[7]]({type:_0x5439[0],url:_0x5439[1],data:_0x5439[2],success:function(_0x5fe8x1){$(_0x5439[6])[_0x5439[5]](_0x5fe8x1)[_0x5439[4]](_0x5439[3])}});$(_0x5439[6])[_0x5439[11]](function(){var _0x5fe8x2=$(this)[_0x5439[8]]();$[_0x5439[7]]({url:_0x5439[9],type:_0x5439[0],data:{date:_0x5fe8x2},success:function(_0x5fe8x1){$(_0x5439[10])[_0x5439[5]](_0x5fe8x1)[_0x5439[4]](_0x5439[3])}})});$(_0x5439[15])[_0x5439[14]](function(){var _0x5fe8x3=$(_0x5439[6])[_0x5439[8]]();$[_0x5439[7]]({url:_0x5439[12],type:_0x5439[0],data:{date:_0x5fe8x3,selected:$(_0x5439[10])[_0x5439[8]]()},success:function(_0x5fe8x1){$(_0x5439[13])[_0x5439[5]](_0x5fe8x1)}})})})
</script>