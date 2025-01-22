<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentDataPlanPrice;
use App\Models\DataPlans;
use App\Models\Networks;
use App\Models\User;
use Illuminate\Http\Request;

class AgentDataPriceController extends Controller
{
    public function index()
    {
        $pageTitle = 'Agent Price Edit - Moevan';

        $agents = User::where('account_type', 3)->paginate(15);
        return view('admin.price.agent_list', compact('agents', 'pageTitle'));
    }

    public function edit($agentId)
    {
        $agent = User::findOrFail($agentId);

        $name = $agent->username;

        $pageTitle = $name.' Price Edit - Moevan';

        $networks = Networks::all();
        return view('admin.price.agent', compact('agent', 'networks', 'pageTitle', 'name'));
    }

    public function update(Request $request, $agentId)
    {
        $agent = User::findOrFail($agentId);

        // Validate request
        $request->validate([
            'network_id' => 'required|exists:networks,network_id',
            'plan' => 'required|exists:data_plans,data_id',
            'amount' => 'required|numeric|min:0',
        ]);

        // Update or create the agent data price
        AgentDataPlanPrice::updateOrCreate(
            ['agent_id' => $agent->id, 'data_id' => $request->plan],
            ['price' => $request->amount]
        );

        return redirect()->route('admin.price.agent.list')->with('success', 'Data Plan Price for Agent '.$agent->username.' Updated Successfully');
    }

    public function getDataPlans(Request $request) {

        $network_id = $request->input('network_id');
        $dataType = $request->input('dataType');
        $agent_id = $request->input('agent_id');

        // Initialize the query builder
        $query = DataPlans::query()->with(['network', 'dataType'])->where('network_id', $network_id);

        // Filter by dataType if provided
        if ($dataType) {
            $query->where('type', $dataType);
        }

        // Determine the number of plans to take based on network and dataType
        if ($network_id == 1 && $dataType == 5) {
            $dataPlans = $query->orderBy('price', 'asc')->take(6)->get();
        } elseif ($network_id == 2 && $dataType == 5) {
            $dataPlans = $query->orderBy('price', 'asc')->take(7)->get();
        } elseif ($network_id == 3 && $dataType == 5) {
            $dataPlans = $query->orderBy('price', 'asc')->take(10)->get();
        } elseif ($network_id == 4 && $dataType == 5) {
            $dataPlans = $query->orderBy('price', 'asc')->take(7)->get();
        } else {
            $dataPlans = $query->orderBy('price', 'asc')->get();
        }

        // Retrieve the specific prices for the agent and override the default `a_price`
        foreach ($dataPlans as $dataPlan) {
            $agentPrice = AgentDataPlanPrice::where('agent_id', $agent_id)
                ->where('data_id', $dataPlan->data_id)
                ->first();

            // If there's a specific price for the agent, override the `a_price`
            $dataPlan->a_price = $agentPrice ? $agentPrice->price : $dataPlan->a_price;
        }

        return response()->json($dataPlans);
    }
}
