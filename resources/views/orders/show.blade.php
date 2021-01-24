@extends('layouts.app')

@section('content')

    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="w-full md:w-2/4 px-2 py-4">
                <div class="flex flex-col break-words bg-white border-2 rounded shadow-md">

                    <div class="font-semibold bg-gray-200 text-gray-700 py-3 px-6 mb-0">
                        Resumen orden de compra
                    </div>
                    <div class="w-full p-6">
                        <div class="flex flex-col mb-6 text-gray-700">
                            <div class="flex-1">
                                <div class="font-semibold text-lg">Producto: </div> 
                                <div class="overflow-hidden font-light text-xl mt-2">{{$order->product->name}}</div>
                            </div>
                            <div class="flex-1 mt-4">
                                <div class="font-semibold text-lg">Precio total: </div> 
                                <div class="overflow-hidden font-light text-xl mt-2">$ {{$order->product->price}} {{$order->product->currency}}</div>
                            </div>
                            <div class="flex-1 mt-6 text-center">
                                <div class="font-semibold text-xl">Datos del comprador </div> 
                            </div>
                        </div>
    
                        <div class="pb-6">
                            <div class="flex-1 text-gray-700 text-xl">
                                <span class="font-semibold"> Nombre: </span>
                                <span>{{$order->customer_name}}</span>
                            </div>
                        </div>
    
                        <div class="pb-6">
                            <div class="flex-1 text-gray-700 text-xl">
                                <span class="font-semibold"> Email: </span>
                                <span>{{$order->customer_email}}</span>
                            </div>
                        </div>
    
                        <div class="pb-6">
                            <div class="flex-1 text-gray-700 text-xl">
                                <span class="font-semibold"> Celular: </span>
                                <span>{{$order->customer_mobile}}</span>
                            </div>
                        </div>
    
                        <div class="flex flex-wrap items-center text-center">
                            <a href="{{url($payment['process_url'])}}" class="w-full bg-green-400 hover:bg-green-600 text-white font-semibold text-2xl py-6 px-4 rounded focus:outline-none focus:shadow-outline">
                                Proceder al pago
                            </a>
                        </div>
                    </div>    
                    
                </div>
            </div>
        </div>
    </div>
@endsection