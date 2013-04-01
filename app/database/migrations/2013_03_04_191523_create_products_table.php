<?php

use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function($table){
			$table->increments('id');
			$table->string('name');
			$table->string('sku');
			$table->integer('type_id');
			$table->string('type_name');
			$table->integer('species_id')->nullable();
			$table->string('shortname');
			$table->binary('barcode');
			$table->string('barcode_path');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}