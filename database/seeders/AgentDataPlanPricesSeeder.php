<?php

namespace Database\Seeders;

use App\Models\AgentDataPlanPrice;
use App\Models\DataPlans;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgentDataPlanPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all agents
        $agents = User::where('account_type', 3)->get();

        // Get all data plans
        $dataPlans = DataPlans::all();

        // Populate the agent_data_plan_prices table
        foreach ($agents as $agent) {
            foreach ($dataPlans as $dataPlan) {
                AgentDataPlanPrice::updateOrCreate(
                    ['agent_id' => $agent->id, 'data_id' => $dataPlan->data_id],
                    ['price' => $dataPlan->a_price]
                );
            }
        }

    }
}
