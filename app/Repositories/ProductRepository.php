<?php

namespace App\Repositories;

use App\Product;

class ProductRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Product();
    }
    
    public function all()
    {
        return $this->model->all();
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function save(Product $product)
    {
        $product->save();
        return $product;
    }
}