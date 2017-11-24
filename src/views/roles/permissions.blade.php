@foreach($roles as $role)
    <div id="{{$role->id}}" class="tab-pane role fade in @if($loop->first){{"active"}}@endif">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#content_type-permissions-{{$role->id}}">Content type</a></li>
            <li><a data-toggle="tab" href="#lang-permissions-{{$role->id}}">Lang</a></li>
            <li><a data-toggle="tab" href="#additional-permissions-{{$role->id}}">Additional</a></li>
            <li><a data-toggle="tab" href="#field_descriptor-permissions-{{$role->id}}">Field Descriptor</a></li>
        </ul>
        <div class="tab-content">
            <div id="content_type-permissions-{{$role->id}}" class="tab-pane fade in active">
                {{--<h3>Content Type</h3>--}}
                @empty(!$permissions)
                    <div class="checkbox">
                        <table class="table table-hover">
                            <tr>
                                <th></th>
                                <th style="width: 100px;">read</th>
                                <th style="width: 100px;">write</th>
                                <th style="width: 100px;">delete</th>
                            </tr>
                            @foreach($permissions['content_type'] as $permission)
                                <tr>
                                    <td>
                                        <label class="checkbox-inline">
                                            {{ $permission['name']}}
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="content_type_r" value="{{$permission['id']}}-r" @if($role->HasPermission($permission['href'],'r')){{'checked'}}@endif>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="content_type_w" value="{{$permission['id']}}-w" @if($role->HasPermission($permission['href'],'w')){{'checked'}}@endif>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="content_type_d" value="{{$permission['id']}}-d" @if($role->HasPermission($permission['href'],'d')){{'checked'}}@endif>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endempty
            </div>
            <div id="lang-permissions-{{$role->id}}" class="tab-pane fade">
                {{--<h3>Lang Edit</h3>--}}
                @empty(!$permissions)

                    <div class="checkbox">
                        <table class="table table-hover">
                            <tr>
                                <th></th>
                                <th style="width: 100px;"></th>
                            </tr>
                            @foreach($permissions['files'] as $files)
                                @foreach($files as $file)
                                    <tr>
                                        <td>
                                            <label class="checkbox-inline">
                                                {{ $file['name']}}
                                            </label>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="file" value="{{$file['id']}}" @if($role->HasPermission($file['href'])){{'checked'}}@endif>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </div>
                @endempty
            </div>
            <div id="additional-permissions-{{$role->id}}" class="tab-pane fade">
                {{--<h3>Additional</h3>--}}
                @empty(!$permissions)
                    <div class="checkbox">
                        <table class="table table-hover">
                            <tr>
                                <th></th>
                                <th style="width: 100px;"></th>
                            </tr>
                            @foreach($permissions['additional'] as $permission)
                                <tr>
                                    <td>
                                        <label class="checkbox-inline">
                                            {{ $permission['name']}}
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="additional" value="{{$permission['id']}}" @if($role->HasPermission($permission['href'])){{'checked'}}@endif>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endempty
            </div>
            <div id="field_descriptor-permissions-{{$role->id}}" class="tab-pane fade">
                {{--<h3>Field Descriptor</h3>--}}
                @empty(!$permissions)
                    <div class="checkbox">
                        <table class="table table-hover">
                            <tr>
                                <th></th>
                                <th style="width: 100px;"></th>
                            </tr>
                            @foreach($permissions['fields_descriptor'] as $permission)
                                <tr>
                                    <td>
                                        <label class="checkbox-inline">
                                            {{ $permission['name']}}
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="fields_descriptor" value="{{$permission['id']}}" @if($role->HasPermission($permission['href'])){{'checked'}}@endif>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endempty
            </div>
        </div>
    </div>
@endforeach

<script type="text/javascript">
    $.ready()
    {
        $('#save').on('click',function(){
            $.ajax({
                type:'POST',
                url:'/admin/additional/roles/edit/add',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data:{
                    role_id: $(".role.fade.in.active").attr('id'),
                    role_name: $("#name").val()
                },
                success:function(data){
                    console.log(data);
                },
                error:function (data) {
                    console.log(data);
                }
            });
        });

        /// add remove permission
        $("input").on("ifClicked",function(){
            sendRequest(this);
        });

    }
    function sendRequest(obj){
        $.ajax({
            type:'POST',
            url:'/admin/additional/roles/edit',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data:{
                role_id: $(".role.fade.in.active").attr('id'),
                permission_id: obj.value
            },
            success:function(data){
                console.log(data);
            },
            error:function (data) {
                console.log(data);
            }
        });
    }
</script>