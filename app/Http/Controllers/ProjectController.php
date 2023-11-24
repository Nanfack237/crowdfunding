<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;



class ProjectController extends Controller
{

    /**
     * @LRDparam title required|string|between:2,100
     * @LRDparam description required|string
     * @LRDparam category required|string|between:2,100
     * @LRDparam target_amount required|integer
     * @LRDparam start_date required|date
     * @LRDparam end_date required|date
     *
     * @param Request $request
     * @return JsonResponse
     */
            //   Create a Project
    public function CreateProject(Request $request){

        // Validate Input
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|between:2,100',
                'description' => 'required|string',
                'category' => 'required|string|between:2,100',
                'target_amount' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $userId = json_decode($request->user)->id;
            $project = DB::select("SELECT * FROM projects WHERE id = ?", [$request->id]);
            if(empty($project)){

                $project = Project::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'category' => $request->category,
                    'user_id' => $userId,
                    'target_amount' => $request->target_amount,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'current_amount' => 0,
                    'status' => 'pending',
                ]);

            } else{
                $status = 422;
                $responses = ['Project already exist'];
            }
            $status = 201;
            $responses = [
                'success' => 'Project successfully created and Request for funds',
                'user' => $project
            ];

            return response()->json($responses, $status);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => 'Project not created ' . $exception->getMessage()
            ], 422);
        }
    }

        // Get All project
    public function AllProjects(){

        $project = DB::select("SELECT * FROM projects");
        if($project){
            return response()->json($project);
        } else {
            $responses = 'No result';
            return response()->json(['respond'=>$responses]);
        }
    }

    /**
     * @LRDparam id required|integer
     *
     * @param Request $request
     * @return JsonResponse
     */
                // Search a Project
    public function SearchProject(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            $project = DB::select("SELECT * FROM projects WHERE id = ?", [$request->id]);
            if ($project) {
                $status = 201;
                $responses = ['project' => $project];
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

    /**
     * @LRDparam id required|integer
     *
     * @param Request $request
     * @return JsonResponse
     */
            // Delete a Project
    public function DeleteProject(Request $request){

        try {
            // Validate input
            $projectCheck= DB::select("SELECT * FROM projects WHERE id = ?", [$request->id]);
    
            if($projectCheck){
                $project=DB::delete("DELETE FROM projects WHERE id = ?", [$request->id]);
                if($project){
                    $status = 201;
                    $responses = ['success'=>'Project successfully deleted!'];
                } else{
                    $status = 422;
                    $responses = ['error'=>'Project not deleted!'];
                }
            } else {
                $status = 422;
                $responses = ['error'=>'Project not found'];
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

