<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<title>优鲜拼拼乐</title>
<link href="/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<img src="/images/title.png" class="titlepic"/>
<div class="cont01">
	<img src="/images/bg02.png" >
</div>
<div class="cont01 cont02" >
	<div class="cont02pic"><img src="/images/5.png" ></div>
	<div class="cont02wechat">
		<div class="">
			@if(isset($info) && !empty($info))
				<img src="/goods_images/{{$info['qrcode_url']}}" />
			@else
				<img src="/images/wechat.png" />
			@endif
		</div>
	</div>
</div>
<img src="/images/miss-fresh.png" class="bottomsign" />
</body>
</html>