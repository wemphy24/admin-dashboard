<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // $teamQuery = Team::withCount('employee');
        $teamQuery = Team::query();

        // get single data
        if($id) {
            $team = $teamQuery->find($id);

            if($team) {
                return ResponseFormatter::success($team, 'Team found');
            }

            return ResponseFormatter::error('Team not found', 404);
        }

        // get multiple data
        $teams = $teamQuery->where('company_id', $request->company_id);

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit), 'Team found'
        );
    }

    public function create(CreateTeamRequest $request)
    {
        try {
            // upload icon
            if($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // create team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if(!$team) {
                throw new Exception('Team not created');
            }

            return ResponseFormatter::success($team, 'Team created');
        } catch (Exception $e) {
            return ResponseFormatter::error('Failed create team', 500);
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            // find team id
            $team = Team::find($id);

            if(!$team) {
                throw new Exception('Team not found');
            }

            // upload icon
            if($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // update team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($team, 'Team updated');
        } catch (Exception $e) {
            return ResponseFormatter::error('Failed update team', 500);
        }
    }

    public function destroy($id)
    {
        try {
            // get team
            $team = Team::find($id);

            if(!$team) {
                throw New Exception('Team not found');
            }

            // delete team
            $team->delete();

            return ResponseFormatter::success('Team deleted');
        }
        catch (Exception $e) {
            return ResponseFormatter::error('Failed delete team', 500);
        }
    }
}
