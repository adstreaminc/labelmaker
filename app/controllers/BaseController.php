<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
	
	public function syncProducts(){

		if( Product::all() ) $old_products = Product::all()->toArray();
		
		$products = file_get_contents('http://www.carved.com/carved-api/wholesale-gen.php');
		$products = json_decode($products, true);
		
		$wordlist = array('Skin', 'Clear Case', '4/4S', '5', 'iPhone', '-', 'Wood');
		
		foreach( $products as $key => $data ){
			if( $old_products && $old_products[$key]['sku'] == $data['sku']){
				$typename = strpos($data['sku'],'i5') !== false ? substr_replace( $data['type_name'], '5 ', 7, 0 ) : substr_replace( $data['type_name'], '4/4S ', 7, 0 );
				$product = Product::where('sku', '=', $data['sku'])->first();
				$product->name = $data['name'];
				$product->sku = $data['sku'];
				$product->type_id = $data['type'];
				$product->type_name = $typename;
				$product->species_id = $data['species_id'];
				$product->shortname = trim(str_replace($wordlist, '', $data['name']));
				$product->save();
			}
			else{
				$product = new Product;
				$typename = strpos($data['sku'],'i5') !== false ? substr_replace( $data['type_name'], '5 ', 7, 0 ) : substr_replace( $data['type_name'], '4/4S ', 7, 0 );
				$product->name = $data['name'];
				$product->sku = $data['sku'];
				$product->type_id = $data['type'];
				$product->type_name = $typename;
				$product->species_id = $data['species_id'];
				$product->shortname = trim(str_replace($wordlist, '', $data['name']));
				$product->save();
			}
		}
		
		return Redirect::back();
	}
}