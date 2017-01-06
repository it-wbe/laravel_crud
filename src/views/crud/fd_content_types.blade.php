@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    <style>
        /*.table-fdcontenttypes td {
            cursor: pointer;
        }*/
        .table-fdcontenttypes tr td:last-child {
            width:1%;
            white-space:nowrap;
        }

        a.content_type_row_link {
            padding: 5px 20px;
            display: block;
        }

        .table-fdcontenttypes td:hover {
            background-color: #E8E8E8;
        }

    </style>
    <script>
        /*$(function () {
            $('a.content_type_delete').click(function () {
                if (!confirm('Дійсно видалити?'))
                    return false;
                if (!confirm('Буде видалено модель, тип контенту та всі його поля. Продовжити?'))
                    return false;
            });
        });*/
    </script>

    <form method="POST" action="">
        <table class="table table-condensed table-fdcontenttypes table-hover">
            <thead>
            <tr>
                <th>Назва</th>
                <th>Таблиця</th>
                <th>Модель</th>
                <th>Записів</th>
                <th>Описано полів</th>
                <th>Дії</th>
            </tr>
            </thead>
            <tbody>
            @foreach($content_types as $ct)
                <?php
                $link_list = url('admin/crud/grid/' . $ct->id);
                $link_insert = url('admin/crud/edit/' . $ct->id . '?insert=1');
                $link_fields = url('admin/fields_descriptor/content/' . $ct->id);
                $link_edit_ct = url('admin/crud/edit/1?modify=' . $ct->id . '&to=' . urlencode(url()->full()));
                ?>
                <tr>
                    <td><a class="content_type_row_link" href="{{ $link_list }}"><b>{{ $ct->name }}</b></a></td>
                    <td><a class="content_type_row_link" href="{{ $link_edit_ct }}">{{ $ct->table }}</a></td>
                    <td><a class="content_type_row_link" href="{{ $link_edit_ct }}">{{ $ct->model }}</a></td>
                    <td><a class="content_type_row_link" href="{{ $link_list }}">{!! $ct->records_count !!}</a></td>
                    <td><a class="content_type_row_link" href="{{ $link_fields }}">{{ $ct->descripted_fileds }}</a></td>
                    <td>
                        <a href="{{ $link_insert }}" class="btn btn-default btn-sm" title="">
                            <span class="glyphicon glyphicon-plus"></span>
                        </a>
                        <a href="{{ $link_list }}" class="btn btn-default btn-sm" title="">
                            <span class="glyphicon glyphicon-edit"></span>
                            Дані
                        </a>
                        <a href="{{ $link_fields }}" class="btn btn-primary btn-sm" title="">
                            <span class="glyphicon glyphicon-th-list"></span>
                            Поля
                        </a>
                        <a href="{{ $link_edit_ct }}" class="btn btn-warning btn-sm" title="Редагувати тип контенту">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <form method="POST" action="{{ url('admin/crud/delete/') }}" style="display: inline-block;" class="content_type_delete">
                            {{ csrf_field() }}
                            <input type="hidden" name="content_id" value="{{ $ct->id }}">
                            <button type="submit" class="btn btn-danger btn-sm" title="Видалити">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                        </form>
                    </td>
                </tr>
            </tbody>
            @endforeach
        </table>
    </form>
@endsection