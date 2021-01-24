@extends('layouts.app')

@section('content')
    <div class="flex items-center">
        <div class="w-full px-1 md:mx-auto">
            <div class="flex flex-col break-words bg-white rounded" style="min-height: 680px">
                <section class="bg-grey-demo w-full content-center pb-20">
                    <div class="text-center text-gray-900 text-2xl md:text-3xl px-2 pt-10 md:pt-16 pb-4">Nuestros productos</div>
                    <div class="flex flex-col md:flex-row text-center content-center">
                        @foreach ($products as $product)
                            <div class="flex-1 h-96 px-2 py-2">
                                <div class="rounded-md h-full md:px-6 py-0 px-2">
                                    <div class="h-full bg-cover bg-center rounded" style="background-image: url({{asset('image/product1.jpg')}})">
                                        <div class="" ></div>
                                    </div>    
                                    
                                    <div class="flex flex-col flex-1 text-gray-800 text-xl font-light shadow-lg rounded-md py-2">
                                        <div class="flex-1">
                                            <div class="overflow-hidden h-6">{{$product->name}}</div>
                                            
                                        </div>
                                        
                                        <div class="flex-1">
                                            <div class="text-grey-600 font-semibold text-xs pt-2 overflow-hidden h-12">{{$product->description}}</div>
                                        </div>
                                        <div class="flex-1 flex text-center pt-4">
                                            <div class="flex-1 text-2xl font-semibold py-4">$ {{$product->price}} {{$product->currency}}</div>
                                            <div class="flex-1 cursor-pointer text-white font-semibold bg-green-500 hover:bg-green-800 py-4 mr-2 rounded-md">Comprar</div>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection