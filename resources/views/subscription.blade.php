@extends('layout')
@section('content')
    <h3>Subscription</h3>
    <div class="plans-wrapper">
    @isset($plan->id)
        <h4>Active subscription</h4>
        <table>
            <tr><td>Name:</td><td>{{ $plan->name }}</td></tr>
            <tr><td>Price:</td><td>{{ $plan->currency }} {{ $plan->price }}</td></tr>
            <tr><td colspan=2><a id="cancel-subscription-button" class="btn mt-4 w-f text-center bg-gray-100" href="javascript:;">Cancel subscription</a></td></tr>
        </table>
    @else
        @foreach($plans as $plan)
            <a href="javascript:;" class="btn" data-subscription-button data-plan-id="{{ $plan->id }}">Buy {{ $plan->name }} ({{ $plan->currency }} {{ $plan->price }})</a>
        @endforeach
    @endisset
    </div>

    <div class="payment-wrapper hidden">
        <div id="braintree-ui"></div>
        <a id="payment-method-request-button" class="btn mt-4 w-f text-center bg-gray-100" href="javascript:;">Complete Payment</a>
    </div>
@endsection