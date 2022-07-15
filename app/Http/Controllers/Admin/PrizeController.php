<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PrizeStoreRequest;
use App\Models\Contest;
use App\Models\Prize;
use App\Models\PrizeDetails;

class PrizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:prize-list|prize-create|prize-edit|prize-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:prize-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:prize-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:prize-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $prizes = Prize::all();
        return view('admin.prizes.index', compact('prizes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $contests = Contest::all();
        return view('admin.prizes.create', compact('contests'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PrizeStoreRequest $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        $objPrize = Prize::create(['name' => $request->input('name'),
                                   'contest_id' => $request->input('contest_id'),
                                   'aff_tools_link' => $request->input('aff_tools_link'),
                                   'contest_type' => $request->input('contest_type'),
                                   'column' => $request->input('column'),
                                   'column_label_1' => $request->input('column_label_1'),
                                   'column_label_2' => $request->input('column_label_2')
                                ]);

        return redirect()->route('admin.prizes.edit', $objPrize->id)
            ->with(
                'flash_success_message',
                trans('global.data_created', ['name' => "$objPrize->name"])
            );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contests = Contest::all();
        $prize = Prize::find($id);
        $rs_pd =  PrizeDetails::where("prize_id", $id)->orderBy('position', 'ASC')->get();
        return view('admin.prizes.edit', compact('prize', 'rs_pd', 'contests'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:3|unique:prize,name,' . $id,
        ]);

        $objPrize = Prize::find($id);
        $objPrize->name = $request->input('name');
        $objPrize->aff_tools_link = $request->input('aff_tools_link');
        $objPrize->contest_id = $request->input('contest_id');
        $objPrize->contest_type = $request->input('contest_type');
        $objPrize->column = $request->input('column');
        $objPrize->column_label_1 = $request->input('column_label_1');
        $objPrize->column_label_2 = $request->input('column_label_2');
        $objPrize->save();
        $cbaccount_name = $request->input('name');

        return redirect()->route('admin.prizes.index')
            ->with(
                'flash_success_message',
                trans('global.data_updated', ['name' => "$cbaccount_name"])
            );
    }

    public function addPrize(Request $request, $id)
    {

        if (
            empty($request->input('prize_category')[0]) ||
            empty($request->input('short_desc')[0]) ||
            empty($request->input('amount')[0]) ||
            $request->input('amount')[0] == 0
        ) {

            $prize = Prize::find($id);
            return redirect()->route('admin.prizes.edit', $id)
                ->with(
                    'flash_warning_message',
                    "Prize details: provide valid details."
                );
        }

        if (count($request->prize_category) > 0) {

            $maxValue = PrizeDetails::where("prize_id", $id)->max('position');
            $positionVal = $maxValue > 0 ? $maxValue : 0;

            for ($i = 0; $i < count($request->prize_category); $i++) {

                if ($request->pz_prize_id > 0) {

                    if (!empty($request->input('prize_category')[$i]) && !empty($request->input('short_desc')[$i]) && !empty($request->input('amount')[$i]) && $request->input('amount')[$i] > 0) {
                        $objPrize = PrizeDetails::find($request->pz_prize_id);
                        $objPrize->prize_type = $request->input('prize_category')[$i];
                        $objPrize->short_desc = $request->input('short_desc')[$i];
                        $objPrize->amount = $request->input('amount')[$i];
                        $objPrize->save();
                    }
                } else {
                    $position = ++$positionVal;
                    $objPrizeDetails = PrizeDetails::create([
                        'prize_id' => $id,
                        'prize_type' => $request->input('prize_category')[$i],
                        'short_desc' => $request->input('short_desc')[$i],
                        'amount' => $request->input('amount')[$i],
                        'position' => $position
                    ]);
                }
            }
        }

        $prize = Prize::find($id);
        return redirect()->route('admin.prizes.edit', $id)
            ->with(
                'flash_success_message',
                trans('global.data_updated', ['name' => "Prize"])
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
        $objPrize = Prize::whereId($id)->first();
        $objPrize->delete();

        $objPrizeDetails = PrizeDetails::where('prize_id', $id);
        $objPrizeDetails->delete();

        return redirect()->route('admin.prizes.index')
            ->with(
                'flash_warning_message',
                trans('global.data_deleted', ['name' => "$objPrize->name"])
            );
    }

    public function deletePriceDetail(Request $request, $id)
    {
        PrizeDetails::where('prize_id', $request->up_prize_id)
            ->where('id', '>', $id)
            ->decrement('position', 1);

        $objPrize = PrizeDetails::whereId($id)->first();
        $objPrize->delete();

        return redirect()->back()
            ->with(
                'flash_warning_message',
                trans('global.data_deleted', ['name' => "Price detail"])
            );
    }

    public function ajaxUpdateOrder(Request $request)
    {

        $position = $request->position;
        $i = 1;
        foreach ($position as $k => $v) {
            //$sql = "Update sorting_items SET position_order=".$i." WHERE id=".$v;

            $objPrize = PrizeDetails::find($v);
            $objPrize->position = $i;
            $objPrize->save();

            $i++;
        }
    }

    public static function getContestName($contest_id)
    {
        $contest = Contest::select('name')->where('id', $contest_id)->first()->toArray();
        $contestName = '';
        if (isset($contest['name'])) {
            $contestName = $contest['name'];
        }
        return $contestName;
    }
}
