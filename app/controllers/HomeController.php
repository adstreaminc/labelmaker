<?php

class HomeController extends BaseController {

	public function getIndex() {

		$products = Product::where('sku', 'NOT LIKE', '%-FRONTSKIN%')->orderBy('name', 'asc')->get()->toArray();	
		
		return View::make('index')
			->with('products', $products);
	
	}

	public function postIndex() {
		if( Request::ajax() ){
		
			$product = Product::where('sku', 'NOT LIKE', '%-FRONTSKIN%')
				->where(function($query){
					$query->where('sku', 'LIKE', '%' . Input::get('query') . '%')->orWhere('name', 'LIKE', '%' . Input::get('query') . '%');
				})
				->orderBy('name')
				->get()
				->toArray();
			
			$data = $product ? $product : 'No Results Found';
			return Response::json($data);
		}
	}
	
	public function getAdmin() {
		$products = Product::where('sku', 'NOT LIKE', '%-FRONTSKIN%')->orderBy('name', 'asc')->get()->toArray();	
		
		return View::make('admin')
			->with('products', $products);
	}
	
	public function postAdmin() {
		if( Request::ajax() ) {
			if( Input::get('sku') ) {
			
				$product = Product::where('sku', '=', Input::get('sku'))->first()->toArray();
				
				return Response::json($product);
			}
		}
		
		Input::file('file')->move('images/barcodes', Input::get('sku') . '.png');
		$path = asset('images/barcodes/' . Input::get('sku') . '.png');
		
		$base64 = base64_encode(file_get_contents($path));
		
		$product = Product::find(Input::get('id'));
		$product->barcode = $base64;
		$product->barcode_path = $path;
		$product->save();
	
		return Redirect::to('admin');

	}
}