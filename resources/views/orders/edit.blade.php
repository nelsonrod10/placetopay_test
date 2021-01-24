@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="w-full md:w-2/4 px-2 py-4">
                <div class="flex flex-col break-words bg-white border-2 rounded shadow-md">

                    <div class="font-semibold bg-gray-200 text-gray-700 py-3 px-6 mb-0">
                        Nueva orden de compra
                    </div>
                    <form class="w-full p-6" method="POST" action="{{ route('orders.update',$order) }}">
                        @csrf
                        @method('PUT')
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
                                <div class="font-semibold text-lg">Por favor diligencie la siguiente información </div> 
                            </div>
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre completo:
                            </label>

                            <input id="name" type="text" class="form-input w-full @error('customer_name') border-red-500 @enderror" name="customer_name" value="{{ $order->customer_name }}" autofocus>

                            @error('customer_name')
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                Email:
                            </label>

                            <input id="email" type="email" class="form-input w-full @error('customer_email') border-red-500 @enderror" name="customer_email" value="{{ $order->customer_email }}" autocomplete="email" autofocus>

                            @error('customer_email')
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="mobile" class="block text-gray-700 text-sm font-bold mb-2">
                                Celular:
                            </label>

                            <input id="mobile" type="number" maxlength="10" class="form-input w-full @error('customer_mobile') border-red-500 @enderror" name="customer_mobile" value="{{ $order->customer_mobile }}" >

                            @error('customer_mobile')
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap items-center">
                            <button type="submit" class="w-full bg-green-400 hover:bg-green-600 text-white font-semibold text-2xl py-6 px-4 rounded focus:outline-none focus:shadow-outline">
                                Confirmar compra
                            </button>
                            @include('orders.delete')
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection