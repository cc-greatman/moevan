<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentDataPlanPrice;
use App\Models\DataPlans;
use App\Models\Networks;
use App\Models\DataType;
use App\Models\Providers;
use App\Models\DataControls;
use App\Models\GeneralSwitch;
use App\Models\Settings;
use App\Models\ProvidersSubscription;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function switchOnDataStatus($id) {

        $switch = DataControls::where('id', $id)->first();

        $network = Networks::where('network_id', $switch->network)->first();
        $network_name = $network->name;

        $dataType = DataType::where('id', $switch->data_type)->first();
        $type_name = $dataType->name??null;

        $switch->status = !$switch->status;
        $switch->save();

        $message = "Manual Disburement for ". $network_name ." ". $type_name ." Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchOnDataActive($id) {

        $switch = DataControls::where('id', $id)->first();

        $network = Networks::where('network_id', $switch->network)->first();
        $network_name = $network->name;

        $dataType = DataType::where('id', $switch->data_type)->first();
        $type_name = $dataType->name??null;

        $switch->active = !$switch->active;
        $switch->save();

        $message = "Main Switch for ". $network_name ." ". $type_name ." Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchOffDataStatus($id) {

        $switch = DataControls::where('id', $id)->first();

        $network = Networks::where('network_id', $switch->network)->first();
        $network_name = $network->name;

        $dataType = DataType::where('id', $switch->data_type)->first();
        $type_name = $dataType->name??null;

        $switch->status = !$switch->status;
        $switch->save();

        $message = "Manual Disburement for ". $network_name ." ". $type_name ." Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchOffDataActive($id) {

        $switch = DataControls::where('id', $id)->first();

        $network = Networks::where('network_id', $switch->network)->first();
        $network_name = $network->name;

        $dataType = DataType::where('id', $switch->data_type)->first();
        $type_name = $dataType->name??null;

        $switch->active = !$switch->active;
        $switch->save();

        $message = "Main Switch for ". $network_name ." ". $type_name ." Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchOffResellerData() {

        $dataType = DataType::where('id', 24)->first();
        $type_name = $dataType->name??null;

        $dataType->status = !$dataType->status;
        $dataType->save();

        $message = "Reseller ". $type_name ." Turned Off Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchOnResellerData() {

        $dataType = DataType::where('id', 24)->first();
        $type_name = $dataType->name??null;

        $dataType->status = !$dataType->status;
        $dataType->save();

        $message = "Reseller ". $type_name ." Turned On Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchOffAgentData() {

        $dataType = DataType::where('id', 25)->first();
        $type_name = $dataType->name??null;

        $dataType->active = !$dataType->active;
        $dataType->save();

        $message = "Agent ". $type_name ." Turned Off Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchOnAgentData() {

        $dataType = DataType::where('id', 25)->first();
        $type_name = $dataType->name??null;

        $dataType->active = !$dataType->active;
        $dataType->save();

        $message = "Agent ". $type_name ." Turned On Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchAirtimeManual() {

        $general = GeneralSwitch::where('id', 1)->first();
        $type_name = $general->name??null;

        $general->status = !$general->status;
        $general->save();

        $message = "Manual ". $type_name ." switched Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchAirtimeMain() {

        $general = GeneralSwitch::where('id', 1)->first();
        $type_name = $general->name??null;

        $general->active = !$general->active;
        $general->save();

        $message = "Main ". $type_name ." switched Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchCableManual() {

        $general = GeneralSwitch::where('id', 2)->first();
        $type_name = $general->name??null;

        $general->status = !$general->status;
        $general->save();

        $message = "Manual ". $type_name ." switched Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchCableMain() {

        $general = GeneralSwitch::where('id', 2)->first();
        $type_name = $general->name??null;

        $general->active = !$general->active;
        $general->save();

        $message = "Main ". $type_name ." switched Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchElectricityManual() {

        $general = GeneralSwitch::where('id', 3)->first();
        $type_name = $general->name??null;

        $general->status = !$general->status;
        $general->save();

        $message = "Manual ". $type_name ." switched Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchElectricityMain() {

        $general = GeneralSwitch::where('id', 3)->first();
        $type_name = $general->name??null;

        $general->active = !$general->active;
        $general->save();

        $message = "Main ". $type_name ." switched Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function switchPopupMain() {

        $general = GeneralSwitch::where('id', 4)->first();
        $type_name = $general->name??null;

        $general->active = !$general->active;
        $general->save();

        $message = "Main ". $type_name ." switched Successfully";

        return redirect()->back()->with('success', $message);
    }

    public function messageEdit (Request $request, $id) {

        // Validate the form data
        $validatedData = $request->validate([
            'message' => 'required',
        ]);

        $settings = Settings::findOrfail($id);
        $settings->rolling_message = $validatedData['message'];
        $settings->save();

        $message = "Message Edited Sucessfully";

        return redirect()->back()->with('success', $message);
    }

    public function editData(Request $request) {

        // Validate the form data
        $validatedData = $request->validate([
            'plan' => 'required|numeric',
            'network_id' => 'required|numeric',
            'amount' => 'required|numeric'
        ], [
            'amount.required' => 'Amount can only contain (0-9)'
        ]);

        $plan_id = $validatedData['plan'];
        $network_id = $validatedData['network_id'];
        $amount =  $validatedData['amount'];

        $planDetails = DataPlans::where('data_id', $plan_id)->with('dataType')->first();
        $networkName = Networks::findOrFail($planDetails->network_id);

        $planDetails->price = $amount;
        $planDetails->save();

        $message = $networkName->name. ' ' .$planDetails->size.' '. $planDetails->dataType->name.' Price edited successfully';

        return redirect()->back()->with('success', $message);
    }

    public function editResellerData(Request $request) {

        // Validate the form data
        $validatedData = $request->validate([
            'plan' => 'required|numeric',
            'network_id' => 'required|numeric',
            'amount' => 'required|numeric'
        ], [
            'amount.required' => 'Amount can only contain (0-9)'
        ]);

        $plan_id = $validatedData['plan'];
        $network_id = $validatedData['network_id'];
        $amount =  $validatedData['amount'];

        $planDetails = DataPlans::where('data_id', $plan_id)->with('dataType')->first();
        $networkName = Networks::findOrFail($planDetails->network_id);

        $planDetails->r_price = $amount;
        $planDetails->save();

        $message = $networkName->name. ' ' .$planDetails->size.' '. $planDetails->dataType->name.' Price edited successfully';

        return redirect()->back()->with('success', $message);
    }

    public function editAgentData(Request $request) {

        // Validate the form data
        $validatedData = $request->validate([
            'plan' => 'required|numeric',
            'network_id' => 'required|numeric',
            'amount' => 'required|numeric'
        ], [
            'amount.required' => 'Amount can only contain (0-9)'
        ]);

        $plan_id = $validatedData['plan'];
        $network_id = $validatedData['network_id'];
        $amount =  $validatedData['amount'];

        $planDetails = DataPlans::where('data_id', $plan_id)->with('dataType')->first();
        $networkName = Networks::findOrFail($planDetails->network_id);

        AgentDataPlanPrice::where('data_id', $plan_id)->update(['price' => $amount]);

        $planDetails->a_price = $amount;
        $planDetails->save();

        $message = $networkName->name. ' ' .$planDetails->size.' '. $planDetails->dataType->name.' Price edited successfully';

        return redirect()->back()->with('success', $message);
    }

    public function editCable(Request $request) {

        $validatedData = $request->validate([
            'cableName' => 'required',
            'amount' => 'required|numeric',
            'variation_code' => 'required'
        ]);

        // Get form data
        $price =  $validatedData['amount'];
        $serviceID = $validatedData['cableName'];
        $variation_code = $validatedData['variation_code'];

        $package = ProvidersSubscription::where('variation_code', $variation_code)->first();

        $service_id = Providers::where('name', $serviceID)->first();

        $package->amount = $price;
        $package->save();

        $message = $service_id->name. ' '.$package->name.' Price edited successfully';

        return redirect()->back()->with('success', $message);
    }
}
