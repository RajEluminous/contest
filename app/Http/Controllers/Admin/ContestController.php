<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ClickbankAccount;
use App\Models\ContestCbProducts;
use App\Models\ClickbankProduct;
use App\Models\Transaction;
use App\Models\TransactionRaw;
use App\Models\LeadCount;
use App\Models\LeadCountRaw;
use App\Models\AffiliateMaster;
use App\Models\Prize;
use App\Models\PrizeDetails;
use Carbon\Carbon;

class ContestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:contest-list|contest-create|contest-edit|contest-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:contest-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:contest-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:contest-delete', ['only' => ['destroy']]);
        $this->middleware('permission:contest-view-results', ['only' => ['view']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $contests = Contest::all();
        return view('admin.contests.index', compact('contests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cbaccounts = ClickbankAccount::orderBy('id', 'DESC')->get();
        return view('admin.contests.create', compact('cbaccounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
         $this->validate($request, [
            'name' => 'required|unique:contests,name',
            'contest_result_places' => 'required',
            'action_after_countdown_expire' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

        // Convert 12 hr datetime to 24 hr datetime Carbon::createFromFormat('Y-m-d H:i:s', '2021-08-27 03:00:00')->setTimezone('US/Mountain');
        $request['start_date'] = new Carbon($request->start_date);
        $request['end_date'] = new Carbon($request->end_date);
        $request['contest_type'] = 'top_sales_count';
        $request['is_display_counter_timer'] = ($request->input('is_display_counter_timer')) == 1 ? 1 : 0;
        $request['is_display_revenue'] = ($request->input('is_display_revenue')) == 1 ? 1 : 0;
        $request['is_display_total_sale'] = ($request->input('is_display_total_sale')) == 1 ? 1 : 0;
        $request['display_sales_result'] = 1;
        $request['display_leads_result'] = 1;
        $request['status'] = 'PAUSED';
        $request['start_date_curl'] = Carbon::createFromFormat('Y-m-d H:i:s', $request->start_date)->setTimezone('US/Mountain');
        $request['end_date_curl'] = Carbon::createFromFormat('Y-m-d H:i:s', $request->end_date)->setTimezone('US/Mountain');

        $input = $request->all();
        $contest = Contest::create($input);

        return redirect()->route('admin.contests.edit', $contest->id)
            ->with(
                'flash_success_message',
                trans('global.data_created', ['name' => "$contest->name"])
            );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $contest = Contest::find($id);

        $stDate = new Carbon($contest->start_date);
        $contest->cal_start_date = $stDate->format('Y-m-d h:i A');

        $edDate = new Carbon($contest->end_date);
        $contest->cal_end_date = $edDate->format('Y-m-d h:i A');

        $cbaccounts = ClickbankAccount::orderBy('id', 'DESC')->get();

        $contestcbprods = ContestCbProducts::where("contest_id", $id)->get();

        return view('admin.contests.edit', compact('contest', 'cbaccounts', 'contestcbprods'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $id)
    {
        $contest = Contest::find($id);
        $contest_status = $contest->status;
        //print_r($contest);
        if($contest->status == 'CLOSED'){ // Fetch from Consolidate Table
            $transactions = Transaction::where("contest_id", $id)->get();
            $cbleads = LeadCount::select('cb_account', 'affiliate_id', 'counts')
                ->where('contest_id', $id)
                ->orderBy('counts', 'DESC')
                ->get();
        } else {                          // Fetch from Raw Table
            $transactions = $this->getConsolidateAffiliatesWithRanking($id,$contest->contest_type);
            $cbleads = $this->getConsolidatedLeadData($id);
        }
        //dd($transactions);
        return view('admin.contests.view', compact('transactions', 'cbleads', 'contest_status'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function updateViewStatus(Request $request)
    {

        $contest = Contest::find($request->contest_id);
        if ($request->type == 'sales')
            $contest->display_sales_view = $request->val;

        if ($request->type == 'leads')
            $contest->display_leads_view = $request->val;

        $contest->save();

        return redirect()->route('admin.contests.index')
            ->with(
                'flash_success_message',
                trans('global.data_updated', ['name' => "Contest"])
            );
    }

    public function updateContestStatus(Request $request){

        $arrstatus = array('false' => 0, 'true' => 1);

        if($request->contest_id >0 && is_int($arrstatus[$request->checked_status])) {

            $contest = Contest::find($request->contest_id);
            $contest->display_contest = $arrstatus[$request->checked_status];
            $contest->save();

        }

    }

    public function updateDisplayStatus(Request $request){

        $arrstatus = array('false' => 0, 'true' => 1);
        $arr_msg = array('display_sales_view' => 'Sale view', 'display_leads_view' => 'Lead view', 'display_contest' => 'Contest', 'display_affiliate_image' => 'Affiliate image', 'display_prizes' => 'Prize');

        if($request->contest_id >0 && is_int($arrstatus[$request->checked_status])) {

            $contest = Contest::find($request->contest_id);

            if($request->update_field =='display_sales_view')
                $contest->display_sales_view = $arrstatus[$request->checked_status];

            if($request->update_field =='display_leads_view')
                $contest->display_leads_view = $arrstatus[$request->checked_status];

            if($request->update_field =='display_contest')
                $contest->display_contest = $arrstatus[$request->checked_status];

            if($request->update_field =='display_affiliate_image')
                $contest->display_affiliate_image = $arrstatus[$request->checked_status];

            if($request->update_field =='display_prizes')
                $contest->display_prizes = $arrstatus[$request->checked_status];

            $contest->save();
        }
        return response()->json([
            "message" => $arr_msg[$request->update_field]." status has been successfully updated"
        ], 201);
    }
    public function ajxGetProducts(Request $request)
    {

        $cities = ClickbankAccount::where('name', 'LIKE', '%' . $request->input('term', '') . '%')
            ->get(['id', 'name as text']);
        return ['results' => $cities];
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
            'name' => 'required'
        ]);

        // Convert 12 hr datetime to 24 hr datetime
        $request['start_date'] = new Carbon($request->start_date);
        $request['end_date'] = new Carbon($request->end_date);

        // Update Contest info
        $contest = Contest::find($id);
        $contest->name = $request->input('name');
        $contest->start_date =  new Carbon($request->start_date);
        $contest->end_date =  new Carbon($request->end_date);
        $contest->contest_result_places = $request->input('contest_result_places');
        $contest->action_after_countdown_expire = $request->input('action_after_countdown_expire');
        $contest->action_after_countdown_expire_value = $request->input('action_after_countdown_expire_value');
        $contest->is_display_counter_timer = ($request->input('is_display_counter_timer')) == null ? 0 : 1;
        // $contest->display_sales_result = ($request->input('display_sales_result')) == null ? 0 : 1;
        $contest->status = $request->input('status');
        $contest->start_date_curl = Carbon::createFromFormat('Y-m-d H:i:s', $request->start_date)->setTimezone('US/Mountain');
        $contest->end_date_curl = Carbon::createFromFormat('Y-m-d H:i:s', $request->end_date)->setTimezone('US/Mountain');
        $contest->save();

        // If status=CLOSED save consolidate Transaction and Lead result
        if ($request->input('status') == 'CLOSED') {
            $this->saveConsolidatedAffiliateRanking($id);
            $this->saveConsolidatedLeadData($id);
        }

        return redirect()->route('admin.contests.index')
            ->with(
                'flash_success_message',
                trans('global.data_updated', ['name' => "$contest->name"])
            );
    }

    // To save the consolidate Lead data to lead_count table
    public function saveConsolidatedLeadData($contest_id)
    {
        $arr_leads = [];
        $cb_prods = ContestCbProducts::select('clickbank_account')
            ->where('contest_id', $contest_id)
            ->where('flag_contest_type', 'LEAD')
            ->get();

        $arr_blocked_affs = $this->getBlockedAffiliates();

        foreach ($cb_prods as $rsCB) {

            //Get clickbank account name
            $cb_acc_name = $this->getClickbankAccountName($rsCB->clickbank_account);

            $cbleads = LeadCountRaw::select('cb_account', 'affiliate_id', DB::raw('count(email) as counts'))
                ->where('contest_id', $contest_id)
                ->where('cb_account', $cb_acc_name)
                ->groupBy('affiliate_id')
                ->orderByRaw('count(email) DESC')
                ->get();

            //LeadCount::where('contest_id', $contest_id)->delete();

            foreach ($cbleads as $cb_leads) {

                if (!empty($cb_leads['cb_account']) && $cb_leads['counts'] > 0 && (!in_array(strtolower($cb_leads['affiliate_id']), $arr_blocked_affs))) {

                    $affiliate_name = $cb_leads['affiliate_id'];

                    LeadCount::insert([
                        'contest_id' => $contest_id,
                        'cb_account' => $cb_leads['cb_account'],
                        'affiliate_id' => $affiliate_name,
                        'counts' => $cb_leads['counts'],
                        'updated_at' =>  Carbon::now(),
                        'created_at' =>  Carbon::now()
                    ]);
                }
            }
            // Delete all records from the RAW Lead Count table
            LeadCountRaw::where('contest_id', $contest_id)->delete();
        }
    }

     // To save the consolidate Lead data to lead_count table
     public function getConsolidatedLeadData($contest_id)
     {
         $arr_leads = [];
         $cb_prods = ContestCbProducts::select('clickbank_account')
             ->where('contest_id', $contest_id)
             ->where('flag_contest_type', 'LEAD')
             ->get();

         $arr_blocked_affs = $this->getBlockedAffiliates();
         $arr_leads = [];
         foreach ($cb_prods as $rsCB) {

             //Get clickbank account name
             $cb_acc_name = $this->getClickbankAccountName($rsCB->clickbank_account);

             $cbleads = LeadCountRaw::select('cb_account', 'affiliate_id', DB::raw('count(email) as counts'))
                 ->where('contest_id', $contest_id)
                 ->where('cb_account', $cb_acc_name)
                 ->groupBy('affiliate_id')
                 ->orderByRaw('count(email) DESC')
                 ->get();

             //LeadCount::where('contest_id', $contest_id)->delete();

             foreach ($cbleads as $cb_leads) {

                 if (!empty($cb_leads['cb_account']) && $cb_leads['counts'] > 0 && (!in_array(strtolower($cb_leads['affiliate_id']), $arr_blocked_affs))) {

                     $affiliate_name = $cb_leads['affiliate_id'];
                     $arr_leads[] = array('cb_account' => $cb_leads['cb_account'], 'affiliate_id' => $affiliate_name, 'counts' => $cb_leads['counts']);

                 }
             }

         }
         array_multisort(array_column($arr_leads, 'counts'), SORT_DESC, $arr_leads);
         return $arr_leads;
     }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objContest = Contest::whereId($id)->first();
        $objContest->delete();
        ContestCbProducts::where("contest_id", $id)->delete();

        // delete prizes as well
        $sqlPrize = Prize::select('id')
        ->where('contest_id', $id)
        ->first();

        if($sqlPrize!=null){
            $objPrize = Prize::whereId($sqlPrize->id)->first();
            $objPrize->delete();

            $objPrizeDetails = PrizeDetails::where('prize_id', $sqlPrize->id);
            $objPrizeDetails->delete();
        }

        return redirect()->route('admin.contests.index')
            ->with(
                'flash_warning_message',
                trans('global.data_deleted', ['name' => "$objContest->name"])
            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletecbprods($id)
    {
        ContestCbProducts::where('id', $id)->delete();

        return redirect()->back()
            ->with('success', 'Clickbank product deleted successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected  function savecbprods(Request $request, $id)
    {

        if (isset($request->btnUpdateContestLeadDisplay)) {

            $contest = Contest::find($id);
            $contest->display_leads_result = ($request->input('display_leads_result')) == 1 ? 1 : 0;
            $contest->save();

            $msgType = 'flash_success_message';
            $msg = 'Display lead flag updated successfully';
            return redirect()->route('admin.contests.edit', [$id, 'tab2'])->with($msgType, $msg);
        }

        if (isset($request->cb_product_id) && $request->cb_product_id > 0) {
            $contestcbprod = ContestCbProducts::find($request->cb_product_id);
            $contestcbprod->clickbank_account = $request->input('clickbank_account');
            $contestcbprod->clickbank_product_ids = $request->input('clickbank_product_ids');
            $contestcbprod->include_rebill = ($request->input('include_rebill')) == 1 ? 1 : 0;

            $contestcbprod->save();
            $msgType = 'success';
            $msg = 'Clickbank product updated successfully';
        } else {
            $cbp = ContestCbProducts::select('*')
                ->where('contest_id', $id)
                ->where('flag_contest_type', $request->input('flagContestType'))
                ->where('clickbank_account', $request->input('clickbank_account'))
                ->exists();

            if ($cbp) {
                $msgType = 'failed';
                $msg = 'Clickbank account already exist';
            } else {
                $request['contest_id'] = $id;

                if ($request->input('flagContestType') == 'SALES') {

                    $cb_products = ClickbankProduct::whereIn('id', $request->clickbank_product_ids)->get()->toArray();

                    $cb_product = '';
                    if (count($cb_products) > 0) {
                        foreach ($cb_products as $cb_prod) {
                            $cb_product .= $cb_prod['product_id'] . ',';
                        }
                        $cb_product = substr($cb_product, 0, -1);
                    }

                    $request['include_rebill'] = ($request->input('include_rebill')) == null ? 0 : 1;
                    $request['flag_contest_type'] = $request->input('flagContestType');
                    $request['clickbank_product_ids'] = $cb_product;

                    $input = $request->all();
                    $contestProds = ContestCbProducts::create($input);
                    $msg = 'Clickbank account has been successfully added to sales contest';
                } else {
                    $request['include_rebill'] = 0;
                    $request['clickbank_product_ids'] = '';
                    $request['flag_contest_type'] = $request->input('flagContestType');

                    $input = $request->all();
                    $contestProds = ContestCbProducts::create($input);
                    $msg = 'Clickbank account has been successfully added to leads contest';
                }

                $msgType = 'flash_success_message';
            }
        }

        return redirect()->route('admin.contests.edit', [$id, 'tab2'])->with($msgType, $msg);
    }

    /**
     * Update ContestType and display sales revenue/count flag in Contest table
     *
     * @return \Illuminate\Http\Response
     */

    public function saveClickbankContestType(Request $request, $id)
    {

        if ($id > 0) {
            $contest = Contest::find($id);
            $contest->contest_type = $request->input('contest_type');
            $contest->is_display_revenue = ($request->input('is_display_revenue')) == null ? 0 : 1;
            $contest->is_display_total_sale = ($request->input('is_display_total_sale')) == null ? 0 : 1;
            $contest->display_sales_result = ($request->input('display_sales_result')) == null ? 0 : 1;
            $contest->save();
        }
        $msgType = 'success';
        $msg = 'Contest type information updated successfully';
        return redirect()->route('admin.contests.edit', [$id, 'tab2'])->with($msgType, $msg);
    }

    public static function getContestType($ctype)
    {
        $arrContestType = array(
            'top_sales_count' => 'Top Sales Count',
            'top_revenue' => 'Top Revenue',
            'top_leads' => 'Top Leads'
        );
        $return = '';
        if (!empty($ctype)) {
            $return = $arrContestType[$ctype];
        }
        return $return;
    }

    public static function getClickbankAccountName($id)
    {
        $cba = ClickbankAccount::find($id);
        $return = '-';
        if (!empty($cba->name))
            $return = $cba->name;

        return $return;
    }

    // function to save consolidated Affiliate Ranking
    public function saveConsolidatedAffiliateRanking($contest_id)
    {
        $sqlContest = Contest::select('contest_type')
            ->where('id', $contest_id)
            ->first();

        if ($sqlContest != null) {
            $rs_con =  $sqlContest->toArray();
            $contest_type = $rs_con['contest_type'];

            $rs_affrank = $this->getConsolidateAffiliatesWithRanking($contest_id, $contest_type);

            if (count($rs_affrank) > 0) {
                Transaction::where('contest_id', $contest_id)->delete();
                foreach ($rs_affrank as $rsaff) {

                    Transaction::insert([
                        'contest_id' => $contest_id,
                        'transaction_type' => $rsaff['transaction_type'],
                        'vendor' => $rsaff['vendor'],
                        'affiliate' => $rsaff['affiliate'],
                        'sales_count' => $rsaff['sale_count'],
                        'account_amount' => $rsaff['account_amount'],
                        'customer_amount' => $rsaff['sale_amount'],
                        'updated_at' =>  Carbon::now(),
                        'created_at' =>  Carbon::now()
                    ]);
                }
                // Delete all the values from the Transaction Raw table
                TransactionRaw::where('contest_id', $contest_id)->delete();
            }
        } // if

    } // f

    /**
     * To get consolidated affiliates with ranking
     */
    public function getConsolidateAffiliatesWithRanking($contest_id, $contest_type)
    {
        $cb_prods = ContestCbProducts::select('id', 'clickbank_account', 'include_rebill')
            ->where('contest_id', $contest_id)
            ->where('flag_contest_type', 'SALES')
            ->get();

        $arr_affs = [];
        $arr_blocked_affs = $this->getBlockedAffiliates();

        // Get the start/end date
        $rs_contest = Contest::select('start_date_curl', 'end_date_curl')->where('id', $contest_id)->first()->toArray();
        $start_date_curl = $rs_contest['start_date_curl'];
        $end_date_curl = $rs_contest['end_date_curl'];

        foreach ($cb_prods as $rsCB) {

            $include_rebill = $rsCB->include_rebill;
            $cb_acc_name = $this->getClickbankAccountName($rsCB->clickbank_account);

            $contest_cb_aff = TransactionRaw::select('affiliate', 'transaction_type', DB::raw('count(affiliate) as sale_count'), DB::raw('SUM(customer_amount) as sale_amount'), DB::raw('SUM(account_amount) as account_amount'))
                ->where('contest_id', $contest_id)
                ->where('vendor', $cb_acc_name)
                ->whereBetween('transaction_time', [$start_date_curl, $end_date_curl])
                ->when($include_rebill, function ($query) use ($include_rebill) {
                    if ($include_rebill == 0)
                        return $query->where('transaction_type', 'SALE');
                    else
                        return $query;
                })
                ->groupBy('affiliate')
                ->get();

            foreach ($contest_cb_aff as $cb_affs) {

                if (!in_array(strtolower($cb_affs->affiliate), $arr_blocked_affs)) {

                        $affiliate_name = $cb_affs->affiliate;

                        $arr_affs[] = array(
                            'affiliate' => $affiliate_name,
                            'sale_count' => $cb_affs->sale_count,
                            'sale_amount' => $cb_affs->sale_amount,
                            'vendor' => $cb_acc_name,
                            'transaction_type' => $cb_affs->transaction_type,
                            'account_amount' => $cb_affs->account_amount
                        );

                }
            }
        }

        if ($contest_type == 'top_revenue')
            array_multisort(array_column($arr_affs, 'sale_amount'), SORT_DESC, $arr_affs);
        else
            array_multisort(array_column($arr_affs, 'sale_count'), SORT_DESC, $arr_affs);

        // return $result = array_unique($arr_affs, SORT_REGULAR);
        return $arr_affs;
    }

    // To get the blocked affiliates
    public function getBlockedAffiliates()
    {
        $aff_sql = AffiliateMaster::select('name')->where('status', 'SUSPENDED')->get()->toArray();
        $arr_blocked = [];

        foreach ($aff_sql as $aff) {
            array_push($arr_blocked, $aff['name']);
        }
        return $arr_blocked;
    }
}
