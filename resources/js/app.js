require('./bootstrap');
const braintreeDropin = require('braintree-web-drop-in');

const subscriptionConfig = {
    activePlan: null,
};

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function showGlobalLoader() {
    document.querySelector('.global-progress-loader').classList.add('visible');
}

function hideGlobalLoader() {
    document.querySelector('.global-progress-loader').classList.remove('visible');
}

function showPaymentBox() {
    document.querySelector('.payment-wrapper').classList.remove('hidden');
}

function hidePaymentBox() {
    document.querySelector('.payment-wrapper').classList.add('hidden');
}

const subscriptionButtons = document.querySelectorAll('[data-subscription-button]');
if (subscriptionButtons.length) {
    for (subscriptionButton of subscriptionButtons) {
        subscriptionButton.onclick = function(e) {
            subscriptionConfig.activePlan = e.target.getAttribute('data-plan-id');
            initSubscriptionPlanPayment();
        }
    }
}

const cancelButton = document.querySelector('#cancel-subscription-button');
if (cancelButton) {
    cancelButton.onclick = function() {
        const response = confirm('Are you sure you want to cancel subscription?');
        if (response) {
            cancelSubscription();
        }
    }
}

async function initSubscriptionPlanPayment() {
    const token = await fetchSubscriptionPaymentToken();
    if (token) {
        createBraintreeUI(token);
    }
}

async function fetchSubscriptionPaymentToken() {
    showGlobalLoader();
    const response = await fetch(`/subscription/payment-token`);
    if (response.ok) {
        const json = await response.json();
        return json.token;
    }
    else {
        alert('There was an error initializing payment');
    }
    hideGlobalLoader();
}

function createBraintreeUI(token) {
    showGlobalLoader();
    showPaymentBox();
    const options = {authorization: token, selector: '#braintree-ui'};
    braintreeDropin.create(options, onBraintreeUICreated);
}

function onBraintreeUICreated(error, instance) {
    hideGlobalLoader();
    document.querySelector('#payment-method-request-button').onclick = function() {
        instance.requestPaymentMethod(function (error, payload) {
            if (payload) {
                submitSubscriptionPaymentMethod(payload);
            }
        });
    }
}

async function submitSubscriptionPaymentMethod(paymentMethod) {
    showGlobalLoader();
    const response = await fetch(`/subscription/pay`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            planId: subscriptionConfig.activePlan,
            paymentMethod: paymentMethod,
        })
    });
    if (response.ok) {
        alert('Subscription completed successfully');
        window.location.reload();
    }
    else {
        alert('There was an error making your payment');
    }
    hideGlobalLoader();
}

async function cancelSubscription() {
    showGlobalLoader();
    const response = await fetch(`/subscription/cancel`);
    if (response.ok) {
        alert('Active subscription canceled successfully');
        window.location.reload();
    }
    else {
        alert('There was an error canceling your subscription');
    }
    hideGlobalLoader();
}