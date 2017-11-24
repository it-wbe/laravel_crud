@extends('crud::layout')
@section('title', 'CRUD - roles')

@section('content')
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <h4>roles</h4>
    <div class="col-md-12 row">
        <a href="{{route('role.add')}}" class="btn btn-success pull-left" style="margin: 20px;">Add Role</a>
    </div>
        <div class="col-md-12 row">
            <ul class="sidebar-menu">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <div class="col-md-3 col-sx-4">
                            <ul class="nav nav-pills nav-stacked">
                                @foreach($roles as $role)
                                    <li class="@if($loop->first){{"active"}}@endif" style="border: none;"><a style="width: 90%; padding: 10px 5px; float: left;" data-toggle="tab" href="#{{$role->id}}">{{$role->name}}</a><div class="pull-right role-del" style="width: 10%; padding: 10px 5px; float: left;"><span style="display: none;">{{$role->id}}</span> <i class="fa fa-fw fa-remove"></i></div></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content">
                                @include('crud::roles.permissions',[$roles])
                            </div>
                        </div>
                    </ul>
                </div>
            </ul>
        </div>
    <script type="text/javascript">
    $.ready()
    {
        //// delete role
        $(".role-del").on('click',function(){
            var del = this;
            $.ajax({
                type:'POST',
                url:'/admin/additional/roles/edit/del',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data:{
                    role_id: $(this).find('span')[0].innerText
                },
                success:function(data){
                    $(del.parentNode).remove();
                    console.log(data);
                },
                error:function (data) {
                    console.log(data);
                }
            });
        });
    }
    </script>
@endsection