<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BraintreeCustomer extends Model {
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function plan() {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
