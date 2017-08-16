@extends('crud::layout')
@section('title', 'Account')
@section('header', 'Account')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-6">

                {!! Form::open(['url' => 'account/edit/']) !!}
                    {{  Form::text('name', $value = old('name',  isset($user->name) ? $user->name : null),  ['class' => 'form-control'], $attributes = array()) }}
                    <br/>
                    {{  Form::email('email', $value = $user->email,  ['class' => 'form-control'], $attributes = array()) }}
                    <br/>
                    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
                <br/>
                <a href="{!!route('admin.password.email')!!}" class="btn btn-default pull-right">Reset Password</a>
            </div>
        </div>
    </div>
@endsection
