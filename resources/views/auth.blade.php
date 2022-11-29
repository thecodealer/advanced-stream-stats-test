@extends('layout')
@section('content')
    <a href="{{ route('auth.google.redirect') }}" class="btn bg-gray-100">Continue with your Google account</a>
@endsection