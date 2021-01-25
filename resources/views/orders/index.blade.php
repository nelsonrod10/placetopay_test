@extends('layouts.app')

@section('content')
    <div class="flex items-center">
        <div class="w-full px-2 md:mx-auto">
            <div class="flex flex-col break-words bg-white border-2 rounded shadow-md">

                <div class="font-semibold bg-gray-200 text-gray-700 py-3 px-6 mb-0">
                    Ordenes generadas
                </div>
                <div class="flex flex-col md:flex-row md:text-center content-center border-gray-400 border-b-2 py-4 ">
                    <div class="flex-1 "><b>Número</b></div>
                    <div class="flex-1"><b>Producto</b></div>
                    <div class="flex-1"><b>Precio</b></div>
                    <div class="flex-1"><b>Fecha Orden</b></div>
                    <div class="flex-1 "><b>Estado</b></div>
                    <div class="flex-1 text-justify"><b>Datos Cliente</b></div>
                    <div class="flex-1"><b>Respuesta Pasarela</b></div>
                </div>
                <div class="w-full p-6">
                    @foreach ($orders as $order)
                    <?php
                        $status = "null";
                        if ($order->gateway) {
                            $gateway = json_decode($order->gateway->payment_data,true);
                            $status = $gateway['status'];
                        }
                    ?>
                    <div class="flex flex-col md:flex-row md:text-center content-center border-gray-400 border-b-2 py-4 ">
                        <div class="flex-1 "><p>{{$order->number}}</p></div>
                        <div class="flex-1"><p>{{$order->product->name}}</p></div>
                        <div class="flex-1"><p>$ {{$order->product->price}} {{$order->product->currency}}</p></div>
                        <div class="flex-1"><p>{{$order->created_at}}</p></div>
                        <div class="flex-1 "><p>{{$order->status}}</p></div>
                        <div class="flex-1 text-justify">
                            <p class="pb-2">{{$order->customer_name}}</p>
                            <p class="pb-2">{{$order->customer_email}}</p>
                            <p>{{$order->customer_mobile}}</p>
                        </div>
                        <div class="flex-1"><p>{{$status}}</p></div>
                    </div>    
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection