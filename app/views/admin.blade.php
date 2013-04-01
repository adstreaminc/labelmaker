@extends('template.page')

@section('title', 'Edit Barcodes')

@section('sidebar')
	<div class="search-bar">
		<span class="search-button button icon-search"></span><input class="search no-vertical-margin" type="text" placeholder="search..."/>
	</div>
	<img class="ajax-load" src="{{ asset('images/loading.gif') }}" />
	<ul class="nav-vertical product-list">
		@if( !$products )
			<p>No data found, please sync products.</p>
		@else
			@foreach( $products as $key => $product )
				<li>
					<a data-sku="{{ $product['sku'] }}" href="#">{{ $product['name'] }}</a>
					@if( $product['barcode_path'] )
						<span class="yes right"></span>
					@else
						<span class="no right"></span>
					@endif
				</li>
			@endforeach
		@endif
	</ul>
@stop


@section('content')
	<div class="row">
		<div id="product-info">
			<div class="grid3">
				<form method="POST" action="{{ URL::action('HomeController@postAdmin') }}" enctype="multipart/form-data">
					<h3 id="name" class="product-name"></h3>
					<input type="file" id="file" name="file" />
					<input type="hidden" name="sku" id="sku"/>
					<input type="hidden" name="id" id="id"/>
					<input type="submit" class="button" value="Add Barcode"/>
				</form>
			</div>
			<div class="grid9">
			<img id="preview-image" width="200" />
			</div>
		</div>
	</div>
@stop

@section('bottomscripts')
	<script>
	(function($){
			
		$('#sidebar').height($(window).height());
		$('.search').width($('#sidebar').width() - $('.search-button').outerWidth());
		$('.product-list').width($('#sidebar').width() + 17);
		
		$('.product-list a').on('click', function(){
			displayData($(this));
		});	
		
		$('.search').on('keyup', function(){
			$('.product-list').empty();
			$('.ajax-load').show();
			$.ajax({
				type: 'POST',
				url: '<?= URL::action('HomeController@postIndex') ?>',
				data: { query: $('.search').val() },
				dataType: 'json',
				success: function(msg, status){
					$('.ajax-load').hide();
					$('.product-list').empty();
					if( typeof msg !== 'string' ){

						$html = '';
						$.each(msg, function(){
							$html += '<li><a data-sku="' + this.sku + '" href="#">' + this.name + '</a>'
							$html += this.barcode_path ? '<span class="yes"></span>' : '<span class="no"></span>';
							$html += '</li>';
						});
						$('.product-list').append($html);
			
					}
					else{
						
						$html = '<li><a href="#">' + msg + '</a></li>';
						$('.product-list').append($html);
					}
					
					$('.product-list a').on('click', function(){
						displayData($(this));
					});	
				}
			});
		});
		
		
		function displayData($object){
			$('#preview-image').hide();
			$('#product-info').fadeOut('fast');
			$el = $object;
			$.ajax({
				type: 'POST',
				url: '<?= URL::action('HomeController@postAdmin') ?>',
				data: { sku: $el.attr('data-sku') },
				dataType: 'json',
				success: function(msg, status){
					$('#product-info').fadeIn('fast');
					$('#name').text(msg.name);
					$('#sku').val(msg.sku);
					$('#id').val(msg.id);
					$('#preview-image').show().attr('src', msg.barcode_path);
				}
			});
		}
		
		
	})(jQuery);
	</script>
@stop