@extends('layout')
@section('content')
    <h3>Stats/Analytics</h3>
    <div class="stats-wrapper">
        @if($hasSubscription)
            <img src="{{ asset('storage/img/stats.png') }}" />
        @else
            <p>You need an active subscription in order to access stats/analytics. <a class="underline" href="{{ route('subscription.index') }}">Click here</a> to subscribe</p>
        @endif
    </div>
@endsection