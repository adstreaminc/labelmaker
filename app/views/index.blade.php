@extends('template.page')

@section('title', 'Print Labels')

@section('topscripts')
	<script src = "http://labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.latest.js" type="text/javascript" charset="UTF-8"></script>
	<script src="{{ asset('js/jquery-ui.js') }}"></script>
@stop

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
				<li><a data-sku="{{ $product['sku'] }}" data-barcode="{{ $product['barcode'] }}" data-type="{{ $product['type_name'] }}" data-shortname="{{ $product['shortname'] }}" href="#">{{ $product['name'] }}</a></li>
			@endforeach
		@endif
	</ul>
@stop

@section('content')
	<div id="drop-zone">
		<table class="dropzone-nav">
			<thead>
				<tr>
					<td>Name</td>
					<td>Sku</td>
					<td>Quantity</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
			
			</tbody>
		</table>
	</div>
@stop

@section('bottomscripts')
	<script>
		
		var splices = {
			'Reconstituted' : 13,
			'World Map' : 9,
			'Ebony World' : 11,
		}
		
		String.prototype.splice = function( idx, rem, s ) {
			return (this.slice(0,idx) + s + this.slice(idx + Math.abs(rem)));
		};
		
		var dymo = dymo.label.framework;
		var printers = dymo.getLabelWriterPrinters();
		if( printers.length == 0 ) alert("No DYMO printers are installed. Install DYMO printers."); 
		var barcodeLabel;
			
		function loadXml(){
			$.get('barcode.label', function(labelXml){
				barcodeLabel = dymo.openLabelXml(labelXml);
			}, 'text');
		}
			
		function printLabels($object){
			
			var $el = $object;
			var spliceCount;
			
			$.each( splices, function(k, v){
				if( $el.attr('data-name').indexOf(k) !== -1 ){
					spliceCount = v;
				}
			});
			
			if( spliceCount ){
				var newstring = $el.attr('data-name').splice(spliceCount, 0, "\n");
				$el.attr('data-name', newstring);
			}
				
			
			
			
			try
			{	
				barcodeLabel.setObjectText('BARCODE', $el.attr('data-barcode'));
				barcodeLabel.setObjectText('NAME', $el.attr('data-name'));
				barcodeLabel.setObjectText('TYPE', $el.attr('data-type'));
				barcodeLabel.setObjectText('SKU',  $el.attr('data-sku'));
				barcodeLabel.print(printers[0].name);
			}
			catch(e)
			{
				console.log(e.stack);
			}
	
		}
				
		$(window).on('load', function(){
			loadXml();
		});	
		
		(function($){
			
			$('#sidebar').height($(window).height());
			$('#drop-zone').height($(window).height() - 50);
			$('.search').width($('#sidebar').width() - $('.search-button').outerWidth());
			$('.product-list').width($('#sidebar').width() + 17);
			
			$('.search').on('keyup', function(){
				$('.product-list').empty();
				$('.ajax-load').show();
				$.ajax({
					type: 'POST',
					url: document.URL,
					data: { query: $('.search').val() },
					dataType: 'json',
					success: function(msg, status){
						$('.ajax-load').hide();
						$('.product-list').empty();
						if( typeof msg !== 'string' ){

							$html = '';
							$.each(msg, function(){
								$html += '<li><a data-sku="' + this.sku + '" data-shortname="' + this.shortname + '" data-type="' + this.type_name + '" data-barcode="' + this.barcode + '" href="#">' + this.name + '</a></li>';
							});
							$('.product-list').append($html);
							
							enableDragging();
						}
						else{
							
							$html = '<li><a href="#">' + msg + '</a></li>';
							$('.product-list').append($html);
						}
					}
				});
			});
			
			function enableDragging(){
			
				$('.product-list li').draggable({
					addClasses: false,
					revert: 'invalid',
					helper: 'clone',
					containment: 'document',
					appendTo: '.dropzone-nav',
					start: function(event, ui){
						$('#drop-zone').fadeIn().addClass('hover');
					},
					stop: function(event, ui){
						$('#drop-zone').removeClass('hover');
					}
				});
				
			}
			
			enableDragging();
			
			$('#drop-zone').droppable({
				accept: '.product-list li',
				drop: function(event, ui){
					$el = ui.draggable.find('a');
					$('.dropzone-nav tbody',this).append('<tr><td class="data-row" data-barcode="' + $el.attr('data-barcode') + '" data-type="' + $el.attr('data-type') + '" data-name="' + $el.attr('data-shortname') + '" data-sku="' + $el.attr('data-sku') + '" data-qty="">' + $el.text() + '</td><td>' + $el.attr('data-sku') + '</td><td><input class="no-vertical-margin qty" type="text" /></td><td width="86"><a href="#" tabIndex="-1" class="right icon-trash remove-button"></a></td></td></tr>');
					
					$('.remove-button').on('click', function(){
						$(this).parent().parent().remove();
					});
					
					$('.dropzone-nav tr').hover(function(){
						$('.remove-button', this).show();
					}, function(){
						$('.remove-button', this).hide();
					});
					
					$('.qty').on('keyup', function(){
						$('.data-row', $(this).parent().parent()).attr('data-qty', $(this).val());
					});
				}
			});
			
			$('.print-button').click(function(){
				$.each($('.data-row'), function(){
					$el = $(this)
					for( $i = 0; $i < $el.attr('data-qty'); $i++ ){
					
					
						printLabels($el);
					}
				});
			});
					
		})(jQuery);
	</script>
@stop