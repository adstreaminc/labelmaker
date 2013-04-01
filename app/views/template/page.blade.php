<!DOCTYPE html>
<html>
	<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Carved Label Maker</title>
        <meta name="viewport" content="width=device-width">
		
		<link type="text/css" rel="stylesheet" href="{{ asset('css/enso.css') }}" />
		<link type="text/css" rel="stylesheet" href="{{ asset('css/styles.css') }}" />
		@yield('stylesheets')
		
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		@yield('topscripts')
	</head>
	<body>
		<div class="row">
			<div class="grid3">
				<div id="sidebar">
					@yield('sidebar')
				</div>
			</div>
			<div class="grid9">
				<div class="section-header">
					<h1 class="left no-vertical-margin">@yield('title')</h1>
					<a class="button right icon-repeat" href="{{ URL::to('sync') }}" title="Sync Products"></a>
					@if( !Request::is('admin') )
					<a class="button print-button right icon-print" href="#" title="Print Labels"></a>	
					@endif
					<a class="button barcode-button right icon-barcode" href="{{ URL::to('admin') }}" title="Manage Labels"></a>
					<a class="button home-button right icon-house" href="{{ URL::to('/') }}" title="Home"></a>	
				</div>
				@yield('content')
			</div>
		</div>
		
		@yield('bottomscripts')
	</body>
</html>