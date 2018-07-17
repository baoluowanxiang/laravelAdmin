@extends("admin.main")
@section("content")
    <style>
        .select-input {
            font-size: 12px;
        }
    </style>
    <div class="page-content">
        <div class="page-header">
            <h1>
                {{$menu_info->name or ''}}
                <span class="btn btn-sm btn-primary pull-right" onclick="javascript:window.location.href = 'info'">
            添加
            </h1>
        </div>

        <div class="operate panel panel-default">
            <div class="panel-body ">
                <form name="myform" method="GET" class="form-inline">
                    <div class="form-group select-input">

                        <input type="hidden" name="house_id" value="{{request('house_id')}}">
                        <input type="hidden" name="to_member_id" value="{{request('to_member_id')}}">
                        <input type="hidden" name="from_member_id" value="{{request('from_member_id')}}">

                        <div class="input-group">
                            <div class="input-group-addon">创建时间</div>
                            <input type="text" class="layui-input" id="start_time" placeholder="" name="start_time"  value="{{request('start_time')}}">
                        </div>

                        <div class="input-group" style="margin-left: 0;">
                            <div class="input-group-addon"> 至</div>
                            <input type="text" class="layui-input" id="end_time" placeholder="" name="end_time" value="{{request('end_time')}}">
                        </div>


                        <div class="input-group">
                            <input type="submit" value="搜索" class="btn btn-danger btn-sm">
                            <span class="btn btn-info btn-sm" onclick="window.location.href = '?'">重置</span>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <div class="row" id="follow-up">
                    <div class="col-xs-12">
                        <table id="simple-table" class="table  table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>时间</th>
                                <th>发言人</th>
                                <th>内容</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($lists as $info)
                                <tr>
                                    <td>{{$info->id}}</td>
                                    <td>{{$info->created_at}}</td>
                                    @if($info->from_member_id==$info->btHouse->member_id)
                                    <td class="bg-danger">房东:{{$info->btFromMember->realname}}</td>
                                    @else
                                        <td>买家:{{$info->btFromMember->realname}}</td>
                                    @endif
                                    <td>{{$info->content}}</td>
                                    <td>{{M('ChatLog')->status_arr[$info->status]}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/{{$_m}}/chatLog/del?id={{$info->id}}" onclick="return confirm('确认操作吗？');return false;">删除</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10">
                                        总数：{{$lists->total()}}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="page">{{$lists->appends(request()->all())->links()}}</div>
                    </div><!-- /.span -->

                </div><!-- /.row -->
                <!-- PAGE CONTENT ENDS -->
            </div><!-- /.col -->
        </div>
    </div>

    <!-- Modal END -->
@endsection