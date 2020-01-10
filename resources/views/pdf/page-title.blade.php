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
		  	margin: 70pt 60pt 70pt;
		  	font-family: "Nunito", sans-serif;
		  	line-height: 1.6;
		}
		h1, h2, h3, h4, h5, h6, p{ display: block; margin: 0 !important;}
		h1 { font-size: 26px; }
		h2 { font-size: 24px; }
		h3 { font-size: 22px; }
		h4 { font-size: 20px; }
		h5 { font-size: 18px; }
		h6 { font-size: 16px; }
		p { 
			font-size: 1.08em; 
			max-width: 100%;
			height: auto;
			word-wrap: break-word;
		}
		h1.chapter{ 
			width: 100%; 
			line-height: 16;
			padding-bottom: 10px;
			text-align: center;
		}
		.text-tiny { font-size: 12px; }
		.text-small { font-size: 16px; }
		.text-big { font-size: 22px; }
		.text-huge { font-size: 26px; }
		div.contents > h1 {
			width: 100%;
			min-width: 100%;
			max-width: 100%;
			display: block;
			text-align: center;
			font-size: 20px;
			margin-bottom: 55px !important;
		}
		div.contents > h2 {
			width: 100%;
			min-width: 100%;
			max-width: 100%;
			display: block;
			text-align: center;
			margin-bottom: 25px !important;
		}
		div.contents, div.contents ul.toc{
			width: 100%;
		}
		ul.toc{
			list-style: none;
		}
		ul.toc li {
			width: 100%;
			min-width: 100%;
			max-width: 100%;
			display: block;
			margin-bottom: 10px !important;
		}
		ul.toc a{
			text-decoration: none;
			color: #171717;
			font-size: 16px;
			display: flex;
			text-align: left;
		}
		ul.toc a::after {
		  	text-align: right;
			display: flex;
			margin-top: 5px;
		}
		.contents {
		    page-break-after: always;
		}
		.page-break {
		    page-break-after: always;
		}
		div.image > img {
			max-width: 100%;
			margin: 1em 0;
		}
		div.image.image-style-align-left > img {
			max-width: 50%;
			margin-right: 21px;
			float: left;
		}
		div.image.image-style-align-right > img {
			max-width: 50%;
			margin-left: 21px;
			float: right;
		}
    </style>
</head>
<body>
	<h1 class="chapter">{{ $page_title }}</h1>
</body>
</html>