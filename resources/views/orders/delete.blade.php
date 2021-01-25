<div> 
    <form action="{{route('orders.destroy',$order)}}" method="POST">
        @csrf
        @method('DELETE')
        <button type="text" class="text-gray-500 underline" >Cancelar compra</button>
    </form>
</div>