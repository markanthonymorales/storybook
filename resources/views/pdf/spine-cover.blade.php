<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{{ $title }}</title>
	<meta name="author" content="{{ $author }}"/>
	<meta name="subject" content=""/>
	<meta name="keywords" content=""/>
	<meta name="date" content=""/>
    <style type="text/css">
    	@page {
		  	size: A4;  
		  	margin: 0pt;
			text-align: center;
		}
		div.cover img{
			width: auto;
		  	height: 11.69in;
		  	margin: 0 auto;
		  	page-break-inside: avoid;
		}
    </style>
</head>
<body>
	<div class="cover">
		<img src="{{ $cover }}" />
	</div>
</body>
</html>