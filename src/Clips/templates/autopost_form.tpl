<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>{{title}}</title>
	</head>
	<body onload="document.getElementById('form').submit()">
		<h1>{{title}}</h1>
		<form id="form" action="{{url}}" method="post">
			{{#fields}}
			<input type="hidden" name="{{name}}" value="{{value}}">
			{{/fields}}
		</form>
	</body>
</html>
