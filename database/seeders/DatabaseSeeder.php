<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        SubscriptionPlan::create(['name' => 'Monthly Plan', 'billing_cycle' => 'monthly', 'currency' => 'USD', 'price' => 23.92]);
        SubscriptionPlan::create(['name' => 'Yearly Plan', 'billing_cycle' => 'yearly', 'currency' => 'USD', 'price' => 199.99]);
    }
}
