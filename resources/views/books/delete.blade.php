@extends('layouts.master')

@section('title')
    Delete Book
@stop


@section('content')

    <h1>Delete Book</h1>

    <p>
        Are you sure you want to delete <em>{{$book->title}}</em>?
    </p>

    <p>
        <a href='/books/delete/{{$book->id}}'>Yes...</a>
    </p>

@stop
