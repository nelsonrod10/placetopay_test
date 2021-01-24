@extends('layouts.app')
<?php

    switch ($status) {
        case 'REJECTED':
            $btnText = "Intertar nuevamente";
            $bgColor = "bg-red-500";
            $textColor = "text-red-500 ";
            $url = route('orders.edit',$order);
            $cancelOrder = true;
            break;
        case 'PENDING':
            $btnText = "Realizar pago";
            $bgColor = "bg-yellow-300";
            $textColor = "text-yellow-500 ";
            $url = url($gateway['process_url']);
            $cancelOrder = true;
            break;
        default:
            $btnText = "Finalizar";
            $bgColor = "bg-green-500";
            $textColor = "text-green-600 ";
            $url = url('/');
            $cancelOrder = false;
            break;
    }
?>
@section('content')

    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="w-full md:w-2/4 px-2 py-4">
                <div class="flex flex-col break-words bg-white border-2 rounded shadow-md">
                    <div class="text-gray-600 h-10 text-center mx-5 pt-5 pb-40">
                        <div class="h-3 w-full mb-6 {{$bgColor}}"></div>
                        <div class="text-xl">
                            {{__('translations.'.strtolower($status))}}
                        </div>
                        <div class="md:mt-10 mt-6">
                            <a href="{{$url}}" class="bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded focus:outline-none focus:shadow-outline
                            {{$textColor}}
                            ">
                                {{$btnText}}
                            </a>
                        </div>
                        @if($cancelOrder)
                            @include('orders.delete')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection