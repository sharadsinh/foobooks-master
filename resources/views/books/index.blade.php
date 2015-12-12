@extends('layouts.master')

@section('title')
    All Books
@stop

@section('content')

    <h1>All Books</h1>

    @if(sizeof($books) == 0)
        You have not added any books.
    @else
        @foreach($books as $book)
            <div>
                <h2>{{ $book->title }}</h2>
                <a href='/books/edit/{{$book->id}}'>Edit</a> | 
                <a href='/books/confirm-delete/{{$book->id}}'>Delete</a><br>
                <img src='{{ $book->cover }}'>
            </div>
        @endforeach
    @endif

@stop
