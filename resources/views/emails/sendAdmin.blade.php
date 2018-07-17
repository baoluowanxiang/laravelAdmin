<p>以报警,请处理!</p>
@foreach($lists as $k=>$v)
    <p>剩余库存:{{$v->total}}</p>
@endforeach