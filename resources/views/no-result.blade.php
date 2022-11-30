@extends('layout')
@section('content')
    @isset($title)
        <h3>{{ $title }}</h3>
    @endisset
    <h4>No results</h4>
@endsection