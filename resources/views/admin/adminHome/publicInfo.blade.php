@extends("admin.main")
@section("content")

    <div class="page-content">


        <div class="page-header">
            <h1>
                {{isset($info->id)?'详情':'添加'}}
            </h1>

        </div><!-- /.page-header -->

        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <form class="form-horizontal" role="form" method="POST" action="publicInfo">
                    {{csrf_field()}}
                    @if(isset($info->id))
                        <input type="hidden" name="id" value="{{$info->id}}"/>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right"> 登录名称 </label>
                        <div class="col-sm-9">
                            <input type="text" name="name" value="{{$info->name or ''}}"
                                   {{isset($info->id)?'disabled':''}} class="col-xs-10 col-sm-8">
                        </div>
                    </div>
                    @if(!isset($info->id))
                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"> 密码 </label>
                            <div class="col-sm-9">
                                <input type="password" name="password" value=""
                                       placeholder="{{isset($info->id)?'不修改密码请保持空':''}}" class="col-xs-10 col-sm-8">
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right"> 手机号 </label>
                        <div class="col-sm-9">
                            <input type="text" name="mobile" value="{{$info->mobile or ''}}" class="col-xs-10 col-sm-8">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right"> 真实姓名 </label>
                        <div class="col-sm-9">
                            <input type="text" name="realname" value="{{$info->realname or ''}}" class="col-xs-10 col-sm-8">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right"> 邮箱 </label>
                        <div class="col-sm-9">
                            <input type="text" name="email" value="{{$info->email or ''}}" class="col-xs-10 col-sm-8">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right"> 接收通知 </label>
                        <div class="col-sm-9">
                            {{From::checkbox(M('Site')->alert_type_arr,$info['setting']['alert_type'],' name="setting[alert_type][]"','')}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right"> 关注微信 </label>
                        <div class="col-sm-9" style="padding-top:8px">
                                <img src="data:image/png;base64,{!! base64_encode(QrCode::format('png')->size(200)->generate(url('/'.$_m.'/'.$_c.'/publicBindWeixin'))) !!} ">
                        </div>
                    </div>


                    @if (count($errors) > 0)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="clearfix form-actions">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn btn-info" type="submit">
                                <i class="ace-icon fa fa-check bigger-110"></i>
                                提交
                            </button>
                            <button class="btn" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>


                </form>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>

    <!-- Modal START -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
        <!--modal,弹出层父级,fade使弹出层有一个运动过程-->
        <div class="modal-dialog" style="width:1000px">
            <!--modal-dialog,弹出层-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">详情</h4>
                </div>
                <div class="modal-body" id="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" style="line-height: 13px;" data-dismiss="modal">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>
    <script>
        //重加载
        $("#myModal").on("hidden.bs.modal", function () {
            $(this).removeData("bs.modal");
            $(".modal-body").empty();
        });
    </script>
    <!-- Modal END -->
@endsection
