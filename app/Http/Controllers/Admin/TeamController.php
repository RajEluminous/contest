<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Requests\TeamStoreRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Models\AffiliateMaster;
use App\Models\Team;
use App\Models\TeamAffiliate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:team-list|team-create|team-edit|team-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:team-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:team-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:team-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $teams = Team::all();
        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //call method from Affiliate controller
        $affiliate_controller = new AffiliateController;
        list($affiliate_list, $partArry) = $affiliate_controller->getCBMasterName();
        return view('admin.teams.create', compact('affiliate_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TeamStoreRequest $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        if ($request->input('affiliate_select') != null) {
            $team = Team::create([
                'name' => $request->input('name'),
                'created_by' => Auth::id(),
                'created_at' => Carbon::now()
            ]);

            if ($team->id > 0) {
                foreach ($request->input('affiliate_select') as $affiliate_id) {
                    $objTeamAffiliate = new TeamAffiliate([
                        'team_id' =>  $team->id,
                        'affiliate_id' => $affiliate_id,
                    ]);

                    $objTeamAffiliate->save();
                }
            }

            return redirect()->route('admin.teams.index')
                ->with(
                    'flash_success_message',
                    trans('global.data_created', ['name' => "$team->name"])
                );
        } else {
            return redirect()->route('admin.teams.create')
                ->with(
                    'flash_error_message',
                    trans('global.affiliate_id_required')
                );
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //call method from Affiliate controller
        $affiliate_controller = new AffiliateController;
        list($affiliate_list, $partArry) = $affiliate_controller->getCBMasterName();

        $team = Team::find($id);
        $teamAffiliates = TeamAffiliate::where("team_id", $id)->select('affiliate_id')->get();
        $teamAff = array();
        foreach ($teamAffiliates as $tAff) {
            $teamAff[] = $tAff->affiliate_id;
        }

        return view('admin.teams.edit', compact('team', 'affiliate_list', 'teamAff'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TeamUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TeamUpdateRequest $request, Team $team)
    {
        // Retrieve the validated input data...
        $team->update($request->validated());

        // Update team name
        $objTeam = Team::find($team->id);
        $objTeam->name = $request->get('name');
        $objTeam->updated_by = Auth::id();
        $objTeam->updated_at = Carbon::now();
        $objTeam->save();

        // Update the team affiliates
        $post_affs = $request->input('affiliate_select');

        if (count($post_affs) > 0) {
            //delete all affiliates of the team id
            TeamAffiliate::where('team_id', $team->id)->delete();

            foreach ($request->input('affiliate_select') as $affiliate_id) {
                $objTeamAffiliate = new TeamAffiliate([
                    'team_id' =>  $team->id,
                    'affiliate_id' => $affiliate_id,
                ]);
                $objTeamAffiliate->save();
            }
        }

        return redirect()->route('admin.teams.index')
            ->with(
                'flash_success_message',
                trans('global.data_updated', ['name' => "$team->name"])
            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objTeam = Team::whereId($id)->first();
        $objTeam->delete();
        TeamAffiliate::where('team_id', $id)->delete();

        return redirect()->route('admin.teams.index')
            ->with(
                'flash_warning_message',
                trans('global.data_deleted', ['name' => "$objTeam->name"])
            );
    }

    public static function getTeamAffiliates($team_id)
    {
        $teamAff = array();
        $affRecordValues = array();

        $teamAffiliates = TeamAffiliate::where("team_id", $team_id)->select('affiliate_id')->get();
        foreach ($teamAffiliates as $teamAffiliate) {
            $teamAff[] = $teamAffiliate->affiliate_id;
        }

        $affRecords = AffiliateMaster::select('name')->whereIn('id', $teamAff)->orderByRaw('name ASC')->get();
        foreach ($affRecords as $affRecord) {
            $affRecordValues[] = $affRecord->name;
        }

        return $affRecordValues;
    }
}
