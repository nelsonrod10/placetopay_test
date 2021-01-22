<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->required();
            $table->string('number')->unique()->required();
            $table->string('customer_name',80)->required();
            $table->string('customer_email',120)->required();
            $table->string('customer_mobile',40)->required();
            $table->string('process_url')->nullable();
            $table->string('request_id')->nullable();
            $table->enum('status',['CREATED','PAYED','REJECTED'])->default('CREATED')->required();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oders');
    }
}
