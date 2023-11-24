<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class Donationcontroller extends Controller
{

    /**
     * @LRDparam project_id required|integer
     * @LRDparam amount required|string|min:2|max:100
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function CreateDonation(Request $request){

        // Validate Input
        try {

            $validator = Validator::make($request->all(), [

                'project_id' => 'required|integer',
                'amount' => 'required|integer|min:1',
                
            ]);

            $donation = DB::select("SELECT * FROM donations WHERE id = ?", [$request->id]);
            $project = DB::select("SELECT * FROM projects WHERE id = ?", [$request->project_id])[0];

            $targetAmount =$project->target_amount;
            $currentAmount = $project->current_amount;
            $newAmount = $request->amount;
            $totalAmount = $currentAmount + $newAmount;
            $surPlus = $totalAmount - $targetAmount;
            $deficiteAmount = $targetAmount - $totalAmount;
            
            if(empty($donation)){

                if($currentAmount > $targetAmount){
                    $status = 422;
                    $responses = [
                        'respond'=> 'Can not recieve funds again, target amount completed '.$targetAmount.' Thanks.'
                    ];
                } else if($totalAmount >= $targetAmount || $totalAmount < $targetAmount){

                    $userId = json_decode($request->user)->id;
                    $donation = Donation::create([

                        'user_id' => $userId,
                        'project_id' => $request->project_id,
                        'amount' => $request->amount,

                    ]);

                    $project = DB::select("UPDATE projects SET current_amount = $totalAmount WHERE id = ?", [$request->project_id]);
                    
                    if($totalAmount == $targetAmount){

                        $project = DB::select("UPDATE projects SET status='complete' WHERE id = ?", [$userId]);
                        $status = 201;
                        $responses = [
                            'success' => 'Donation successfully given and it is complete to the target amount is '.$targetAmount.' Thanks',
                            'donation' => $donation
                        ];
                    } else if($totalAmount < $targetAmount){
                    
                        $status = 201;
                        $responses = [
                            'success' => 'Donation successfully given,target amount is '.$targetAmount.' remaining '.$deficiteAmount.' Thanks',
                            'donation' => $donation
                        ];
                    } else {
                        
                        $project = DB::select("UPDATE projects SET status='complete' WHERE id = ?", [$userId]);
                        $status = 201;
                        $responses = [
                            'success' => 'Donation successfully given,target amount is complete '.$targetAmount.' with a surplus of '.$surPlus.' Thanks',
                            'donation' => $donation
                        ];
                    }
                }
            } else{
        
                $responses = ['error' => 'User already exist'];
            }

            return response()->json($responses, $status);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => 'Donation not created ' . $exception->getMessage()
            ], 422);
        }
    }

            // List of donations
    public function AllDonations(){

        $donations =  DB::select("SELECT * FROM donations");
        if($donations){
            return response()->json($donations);
        } else {
            $message = 'No result';
            return response()->json(['message'=>$message]);
        }
    }

            // Search a donation
    public function SearchDonation(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            $project = DB::select("SELECT * FROM donations WHERE id = ?", [$request->id]);
            if ($project) {
                $status = 201;
                $responses = ['daonation' => $project];
            } else {
                $status = 422;
                $responses = ['error' => 'Project not found'];
            }
            return response()->json($responses, $status);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => 'Project not found ' . $exception->getMessage()
            ], 422);
        }
                
        
    }

}
