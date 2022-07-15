<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\ClickbankAccount;
use App\Models\ContestCbProducts;
use App\Models\Transaction;
use App\Models\TransactionRaw;
use App\Models\AffiliateMaster;
use App\Models\LeadCount;
use App\Models\LeadCountRaw;
use App\Models\Prize;
use App\Models\PrizeDetails;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\TeamAffiliate;
use DB;
use DateTime;


class LeaderboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->getSetExpiredContest();
        //$this->getContestAffiliateData();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $arrContestTypeLabel = array(
            'top_sales_count' => 'Top Sales Count',
            'top_revenue' => 'Top Revenue',
            'top_leads' => '-'
        );
        $aff_partner_list = $this->getAllAffiliatePartner();

        $contest_show = Contest::where("display_contest", 1)->whereIn('status', ['RUNNING', 'ENDED'])->orderBy('start_date', 'ASC')->limit(3)->get();
        $current_date = new Carbon;
        $date_params = [];
        $arrContest = [];

        foreach ($contest_show as $key => $rs_contest) {

            if ($current_date < $rs_contest->start_date) {

                $date_params[$key]['date_show_value'] = date('Y-m-d H:i', strtotime($rs_contest->start_date));
                $date_params[$key]['date_value'] = $rs_contest->start_date;
                $date_params[$key]['date_flag'] = 'start';
            } else {

                // if CURRENT Date passed START Date but less the END Date
                $date_params[$key]['date_show_value'] = date('Y-m-d H:i', strtotime($rs_contest->end_date));
                $date_params[$key]['date_value'] = $rs_contest->end_date;
                $date_params[$key]['date_flag'] = 'end';
            }

            $arrContest[$rs_contest->id] = $rs_contest->contest_type;
        }

        $arrAffiliateData = [];
        $arrLeadsData = [];
        $arrPrizeData = [];
        foreach ($arrContest as $kContest => $rsContest) {

            $arrAffiliateData[$kContest] = $this->getAffiliatesWithRanking($kContest, $arrContest[$kContest]);
            // Get the Lead
            $arrLeadsData[$kContest] = $this->getLeadData($kContest);
            // Get the Prize Details
            $arrPrizeData[$kContest] = $this->getPrizeData($kContest);
        }

        return view('leaderboard', compact('contest_show', 'date_params', 'arrAffiliateData', 'arrContestTypeLabel', 'aff_partner_list', 'arrLeadsData', 'arrPrizeData'));
    }

    // Get Prizes details
    public function getPrizeData($contest_id)
    {

        $arr_prize = [];
        $sqlPrize = Prize::select('id', 'aff_tools_link', 'contest_type', 'column', 'column_label_1', 'column_label_2')
            ->where('contest_id', $contest_id)
            ->first();

        if ($sqlPrize != null) {
            $rs_p =  $sqlPrize->toArray();
            $arr_prize['aff_tools_link'] = $rs_p['aff_tools_link'];
            $arr_prize['contest_type'] = $rs_p['contest_type'];
            $arr_prize['column'] = $rs_p['column'];
            $arr_prize['column_label_1'] = $rs_p['column_label_1'];
            $arr_prize['column_label_2'] = $rs_p['column_label_2'];
            $arr_prize['contest_id'] = $contest_id;
            $rs_pd =  PrizeDetails::select('id', 'prize_type', 'short_desc', 'amount')->where("prize_id", $rs_p['id'])->orderBy('position', 'ASC')->get();
            foreach ($rs_pd as $keyPD => $valPD) {

                $arr_prize[$valPD->prize_type][] =  array('id' => $valPD->id, 'prize_type' => $valPD->prize_type, 'short_desc' => $valPD->short_desc, 'amount' => $valPD->amount, 'aff_tools_link' => $rs_p['aff_tools_link']);
            }
        }

        return ($arr_prize);
    }

    // Get the Leads details
    public function getLeadData($contest_id)
    {
        $arr_leads = [];
        $cb_prods = ContestCbProducts::select('clickbank_account')
            ->where('contest_id', $contest_id)
            ->where('flag_contest_type', 'LEAD')
            ->get();

        $arr_blocked_affs = $this->getBlockedAffiliates();
        $arr_teams = $this->getTeams();

        foreach ($cb_prods as $rsCB) {

            //Get clickbank account name
            $cb_acc_name = $this->getClickbankAccountName($rsCB->clickbank_account);

            $cbleads = LeadCountRaw::select('cb_account', 'affiliate_id', DB::raw('count(email) as counts'))
                ->where('contest_id', $contest_id)
                ->where('cb_account', $cb_acc_name)
                ->groupBy('affiliate_id')
                ->orderByRaw('count(email) DESC')
                ->get();

            foreach ($cbleads as $cb_leads) {

                if (!empty($cb_leads['cb_account']) && $cb_leads['counts'] > 0 && (!in_array(strtolower($cb_leads['affiliate_id']), $arr_blocked_affs))) {

                    $affiliate_name = $cb_leads['affiliate_id'];
                    if (isset($arr_teams[strtolower($cb_leads['affiliate_id'])]))
                        $affiliate_name = $arr_teams[strtolower($cb_leads['affiliate_id'])];

                    $arr_leads[] = array('id' => $rsCB->clickbank_account, 'name' => strtoupper($affiliate_name), 'lead_count' => $cb_leads['counts']);
                }
            }
        }

        $arrLeads = [];
        if (count($arr_leads) >= 1) {
            foreach ($arr_leads as $arrLead) {

                $aKey = $this->in_array_r($arrLead['name'], $arrLeads);
                if (is_integer($aKey)) {
                    $arrLeads[$aKey]['lead_count'] += $arrLead['lead_count'];
                } else {
                    $profileimage = $this->getAffiliateProfileImage($arrLead['name']);
                    $arrLeads[] =  array('id' => $arrLead['id'], 'name' => $arrLead['name'], 'lead_count' => $arrLead['lead_count'], 'affiliate_profile_img' => $profileimage);
                }
            }
        }

        array_multisort(array_column($arrLeads, 'lead_count'), SORT_DESC, $arrLeads);
        return $arrLeads;
    }

    public function in_array_r($needle, $haystack)
    {
        foreach ($haystack as $key => $subArr) {
            if (in_array($needle, $subArr)) {
                return $key;
            }
        }
        return false;
    }

    /* New: Get Affiliate Data with Ranking
    */
    public function getAffiliatesWithRanking($contest_id, $contest_type)
    {
        $cb_prods = ContestCbProducts::select('id', 'clickbank_account', 'include_rebill')
            ->where('contest_id', $contest_id)
            ->where('flag_contest_type', 'SALES')
            ->get();

        $arr_affs = [];
        $arr_blocked_affs = $this->getBlockedAffiliates();
        $arr_teams = $this->getTeams();

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
                    if (isset($arr_teams[strtolower($cb_affs->affiliate)]))
                        $affiliate_name = $arr_teams[strtolower($cb_affs->affiliate)];

                    $key = array_search($affiliate_name, array_column($arr_affs, 'affiliate'));

                    if (is_integer($key)) {

                        $arr_affs[$key]['sale_count'] += $cb_affs->sale_count;
                        $arr_affs[$key]['sale_amount'] += $cb_affs->sale_amount;
                    } else {

                        $profileimage = $this->getAffiliateProfileImage($affiliate_name);
                        $arr_affs[] = array(
                            'affiliate' => $affiliate_name,
                            'sale_count' => $cb_affs->sale_count,
                            'sale_amount' => $cb_affs->sale_amount,
                            'vendor' => $cb_acc_name,
                            'transaction_type' => $cb_affs->transaction_type,
                            'account_amount' => $cb_affs->account_amount,
                            'affiliate_profile_img' => $profileimage
                        );
                    }
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

    // To get the Affiliate Profile Image
    public function getAffiliateProfileImage($img_name)
    {
        $aff_profile_img = asset('img/default-user.jpg');

        if (!empty($img_name)) {
            $aff_arr = array(strtolower($img_name), strtoupper($img_name));
            $affRecords = AffiliateMaster::select('image')->whereIn('name', $aff_arr)->first();

            if (isset($affRecords->image) && $affRecords->image != null) {
                $aff_profile_img = asset('/storage/aff_images/' . $affRecords->image);
            }
        }
        return $aff_profile_img;
    }

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
        $arr_teams = $this->getTeams();

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

                    $key = array_search($affiliate_name, array_column($arr_affs, 'affiliate'));

                    if ($key != null) {

                        $arr_affs[$key]['sale_count'] += $cb_affs->sale_count;
                        $arr_affs[$key]['sale_amount'] += $cb_affs->sale_amount;
                    } else {

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
        }

        if ($contest_type == 'top_revenue')
            array_multisort(array_column($arr_affs, 'sale_amount'), SORT_DESC, $arr_affs);
        else
            array_multisort(array_column($arr_affs, 'sale_count'), SORT_DESC, $arr_affs);

        // return $result = array_unique($arr_affs, SORT_REGULAR);
        return $arr_affs;
    }

    // OLD Get Affilaite Data With Ranking
    public function getAffiliatesWithRankingOld($contest_id, $contest_type)
    {
        $cb_prods = ContestCbProducts::select('id', 'clickbank_account', 'include_rebill')
            ->where('contest_id', $contest_id)
            ->where('flag_contest_type', 'SALES')
            ->get();

        $arr_affs = [];
        $arr_blocked_affs = $this->getBlockedAffiliates();
        $arr_teams = $this->getTeams();

        foreach ($cb_prods as $rsCB) {

            $include_rebill = $rsCB->include_rebill;
            $cb_acc_name = $this->getClickbankAccountName($rsCB->clickbank_account);
            $contest_cb_aff = Transaction::select('affiliate', DB::raw('SUM(sales_count) as sale_count'), DB::raw('SUM(customer_amount) as sale_amount'))
                ->where('contest_id', $contest_id)
                ->where('vendor', $cb_acc_name)
                ->where('sales_count', '>', 0)
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
                    if (isset($arr_teams[strtolower($cb_affs->affiliate)]))
                        $affiliate_name = $arr_teams[strtolower($cb_affs->affiliate)];

                    $key = array_search($affiliate_name, array_column($arr_affs, 'affiliate'));

                    if ($key != null) {
                        $arr_affs[$key]['sale_count'] += $cb_affs->sale_count;
                        $arr_affs[$key]['sale_amount'] += $cb_affs->sale_amount;
                    } else {
                        $arr_affs[] = array(
                            'affiliate' => $affiliate_name,
                            'sale_count' => $cb_affs->sale_count,
                            'sale_amount' => $cb_affs->sale_amount
                        );
                    }
                }
            }
        }

        if ($contest_type == 'top_revenue')
            array_multisort(array_column($arr_affs, 'sale_amount'), SORT_DESC, $arr_affs);
        else
            array_multisort(array_column($arr_affs, 'sale_count'), SORT_DESC, $arr_affs);

        return $result = array_unique($arr_affs, SORT_REGULAR);
        return $arr_affs;
    }

    /*  Get all the CB vendors, state=RUNNING
    */
    public static function getContestAffiliateData()
    {
        $todays_datetime = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->setTimezone('US/Mountain');

        $contest =  DB::table('contests')
            ->leftJoin('contest_cb_products', 'contests.id', '=', 'contest_cb_products.contest_id')
            ->leftJoin('clickbank_accounts', 'contest_cb_products.clickbank_account', '=', 'clickbank_accounts.id')
            ->select(
                'contests.id as contest_id',
                'contests.name as contest_name',
                'contests.contest_type as contest_type',
                'contests.start_date as contest_start_date',
                'contests.end_date as contest_end_date',
                'contests.status as contest_status',
                'contest_cb_products.clickbank_account as clickbank_account_id',
                'clickbank_accounts.name as clickbank_account_name',
                'contest_cb_products.clickbank_product_ids as clickbank_product_ids',
                'contest_cb_products.include_rebill as include_rebill'
            )
            ->where('contests.status', 'RUNNING')

            ->where('contests.start_date_curl', '<', $todays_datetime)
            ->where('contests.end_date_curl', '>=', $todays_datetime)
            ->get();

        $orders = array();
        $arrTransType = ["SALE", "BILL"];

        foreach ($contest as $rsconst) {

            $va = $rsconst->clickbank_account_name;
            $start_date = Carbon::parse($rsconst->contest_start_date)->format('Y-m-d');
            $end_date = Carbon::parse($rsconst->contest_end_date)->format('Y-m-d');

            $url = "https://api.clickbank.com/rest/1.3/orders2/list?role=VENDOR&type=SALE&vendor=$va&startDate=$start_date&endDate=$end_date";
            $api_result = LeaderboardController::getOrderAPIData($url);
            if (count($api_result) > 0) {

                foreach ($api_result as $apData) {
                    if (isset($apData['transactionTime'])) {

                        $arrOrder['contest_id'] = $rsconst->contest_id;
                        if (in_array($apData['transactionType'], $arrTransType))
                            $orders[] = $apData;
                    } else {

                        foreach ($apData as $arrOrder) {

                            $arrOrder['contest_id'] = $rsconst->contest_id;
                            $arrOrder['product_ids'] = $rsconst->clickbank_product_ids;
                            if (in_array($arrOrder['transactionType'], $arrTransType))
                                $orders[] = $arrOrder;
                        }
                    }
                }
            } // if

        } // foreach

        // Processing of data
        $finalArr = [];
        foreach ($orders as $dataOrd) {

            $transactionType = $dataOrd['transactionType'];
            $vendor = $dataOrd['vendor'];
            $affiliate = $dataOrd['affiliate'];
            $account_amount = 0;
            $customer_amount = 0;

            if (isset($dataOrd['lineItemData']['lineItemType'])) {

                $quantity = $dataOrd['lineItemData']['quantity'];
                $account_amount = $dataOrd['lineItemData']['accountAmount'];
                $customer_amount = $dataOrd['lineItemData']['customerAmount'];

                if (isset($dataOrd['transactionType']) && isset($dataOrd['vendor']) && !is_array($dataOrd['affiliate']) && in_array($dataOrd['lineItemData']['itemNo'], explode(',', $dataOrd['product_ids']))) {

                    $lineItemData = [];
                    $lineItemData['receipt'] =  $dataOrd['receipt'];
                    $lineItemData['itemNo'] =  $dataOrd['lineItemData']['itemNo'];
                    $lineItemData['recurring'] =  $dataOrd['lineItemData']['recurring'];
                    $lineItemData['customerAmount'] =  $dataOrd['lineItemData']['customerAmount'];
                    $lineItemData['accountAmount'] =  $dataOrd['lineItemData']['accountAmount'];

                    LeaderboardController::saveRawTransactionApiData($dataOrd, $lineItemData);
                }
            } else {

                foreach ($dataOrd['lineItemData'] as $dataLineItem) {

                    $account_amount = 0;
                    $customer_amount = 0;
                    $quantity = $dataLineItem['quantity'];

                    $account_amount = $dataLineItem['accountAmount'];
                    $customer_amount = $dataLineItem['customerAmount'];

                    if (isset($dataOrd['transactionType']) && isset($dataOrd['vendor']) && !is_array($dataOrd['affiliate']) && in_array($dataLineItem['itemNo'], explode(',', $dataOrd['product_ids']))) {

                        $lineItemData = [];
                        $lineItemData['receipt'] =  $dataOrd['receipt'];
                        $lineItemData['itemNo'] =  $dataLineItem['itemNo'];
                        $lineItemData['recurring'] =  $dataLineItem['recurring'];
                        $lineItemData['customerAmount'] =  $dataLineItem['customerAmount'];
                        $lineItemData['accountAmount'] =  $dataLineItem['accountAmount'];

                        LeaderboardController::saveRawTransactionApiData($dataOrd, $lineItemData);
                    }
                    //

                } // foreach

            } // if-else

        } // foreach


    }

    // TransactionRaw: check if record exist in database, if not insert it
    public static function saveRawTransactionApiData($dataOrd, $dataLineItem)
    {

        $contest_id = $dataOrd['contest_id'];
        $receipt = $dataOrd['receipt'];

        $transactionTime = new DateTime($dataOrd['transactionTime']);
        $transactionTime->format('Y-m-d H:i:s');

        $vendor = $dataOrd['vendor'];
        $affiliate = $dataOrd['affiliate'];

        $itemNo = $dataLineItem['itemNo'];
        $transactionType = $dataLineItem['recurring'] == 'false' ? 'SALE' : 'BILL';

        $account_amount =  $dataLineItem['accountAmount'];
        $customer_amount =  $dataLineItem['customerAmount'];

        // check if record exist in DB
        $exist = TransactionRaw::where([
            'contest_id' => $contest_id,
            'receipt' => $receipt,
            'item_no' =>  $itemNo,
            'transaction_type' =>  $transactionType
        ])->first();

        if ($exist == null) {    // INSERT Records

            TransactionRaw::insert([
                'contest_id' => $contest_id,
                'receipt' => $receipt,
                'item_no' => $itemNo,
                'transaction_time' => $transactionTime,
                'transaction_type' => $transactionType,
                'vendor' => $vendor,
                'affiliate' => $affiliate,
                'account_amount' => $account_amount,
                'customer_amount' => $customer_amount,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now()
            ]);
        } else {                // UPDATE Records

            $id = $exist->id;
            $trans_order = TransactionRaw::findOrFail($id);

            $trans_order->account_amount =  $account_amount;
            $trans_order->customer_amount =  $customer_amount;
            $trans_order->updated_at = Carbon::now();
            $trans_order->save();
        }
    }

    public static function processOrderAPIData($ordArr)
    {

        /*
            1. Check if record exist for the contestid, transactionType, vendor,affiliate if not then INSERT
            2. If record not exist then Calculate the sum of quantity, accountAmount (role=vendor/affiliate),
        */

        foreach ($ordArr as $kContest => $vContest) {
            foreach ($vContest as $kTransactiontype => $vTransactiontype) {
                foreach ($vTransactiontype as $kVendor => $vVendor) {
                    foreach ($vVendor as $kAffiliate => $vAffiliate) {

                        // check if record exist in DB
                        $exist = Transaction::where([
                            'contest_id' => $kContest,
                            'transaction_type' => $kTransactiontype,
                            'vendor' =>  $kVendor,
                            'affiliate' =>  $kAffiliate,
                        ])->first();

                        if ($exist == null) {    // INSERT Records

                            Transaction::insert([
                                'contest_id' => $kContest,
                                'transaction_type' => $kTransactiontype,
                                'vendor' => $kVendor,
                                'affiliate' => $kAffiliate,
                                'sales_count' => $vAffiliate['quantity'],
                                'account_amount' => $vAffiliate['account_amount'],
                                'customer_amount' => $vAffiliate['customer_amount'],
                                'updated_at' => $vAffiliate['transaction_time'],
                                'created_at' => $vAffiliate['transaction_time']
                            ]);
                        } else {                // UPDATE Records

                            $id = $exist->id;
                            $trans_order = Transaction::findOrFail($id);
                            $trans_order->sales_count = $vAffiliate['quantity'];
                            $trans_order->account_amount = $vAffiliate['account_amount'];
                            $trans_order->customer_amount = $vAffiliate['customer_amount'];
                            $trans_order->updated_at = Carbon::now();
                            $trans_order->save();
                        }
                    }
                }
            }
        }
    }

    public static function getOrderAPIData($url)
    {
        $data = [];
        $page = 1;

        do {
            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($result, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_TIMEOUT, 360);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

            $api_result = json_decode(curl_exec($result), true);

            if ($api_result != null) {
                $data[] = $api_result['orderData'];
            }
            $page++;
        } while (isset($api_result['orderData']['99']));

        return $data;
    }

    // Check and set the contest Expired based on CURRENT date
    public function getSetExpiredContest()
    {

        $contest_exp = Contest::where('status', 'RUNNING')->orderBy('start_date', 'ASC')->limit(3)->get();
        $current_date = new Carbon;
        foreach ($contest_exp as $key => $rs_contest) {

            if ($current_date > $rs_contest->end_date) {

                $contest_ord = Contest::findOrFail($rs_contest->id);
                $contest_ord->status = 'ENDED';
                $contest_ord->updated_at = Carbon::now();
                $contest_ord->save();

            }
        }
    }

    // function to save consolidated Affiliate Ranking
    public function saveConsolidatedAffiliateRanking($contest_id, $contest_type)
    {

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

    // To get all Affiliate Partner list
    public static function getAllAffiliatePartner()
    {
        $partnerList = DB::table('affiliate_master')
            ->join('cb_affiliate_partner_lists', 'affiliate_master.id', '=', 'cb_affiliate_partner_lists.affiliate_id')
            ->join('partner_master', 'cb_affiliate_partner_lists.partner_id', '=', 'partner_master.id')
            ->select('affiliate_master.name as aff_id', 'partner_master.name as partner_id')
            ->get();
        $partnerArr = [];
        foreach ($partnerList as $rsd) {
            $partnerArr[$rsd->aff_id] = $rsd->partner_id;
        }

        return $partnerArr;
    }

    // To get clickbank account name
    public function getClickbankAccountName($id)
    {
        $cba = ClickbankAccount::find($id);
        $return = '-';
        if (!empty($cba->name))
            $return = $cba->name;

        return $return;
    }

    // To get the Teams array
    public function getTeams()
    {
        $affRecordValues = array();
        $team_sql = Team::select('id', 'name')->get()->toArray();

        foreach ($team_sql as $team) {

            $teamAff = array();
            $teamAffiliates = TeamAffiliate::where("team_id", $team['id'])->select('affiliate_id')->get();
            foreach ($teamAffiliates as $teamAffiliate) {
                $teamAff[] = $teamAffiliate->affiliate_id;
            }

            $affRecords = AffiliateMaster::select('name')->whereIn('id', $teamAff)->orderByRaw('name ASC')->get();

            foreach ($affRecords as $affRecord) {
                $affRecordValues[$affRecord->name] = $team['name'];
            }
        }

        return ($affRecordValues);
    }

    // Set contest status=ENDED
    public function ajaxUpdateContestStatus(Request $request)
    {
        $rs_contest = Contest::select('status')->where('id', $request->expiredcontestid)->first()->toArray();

        if ($rs_contest['status'] == 'RUNNING') {
            $contest_sts = Contest::findOrFail($request->expiredcontestid);
            $contest_sts->status = 'ENDED';
            $contest_sts->updated_at = Carbon::now();
            $contest_sts->save();
        }

        return response()->json([
            "message" => "Contest status 'ENDED' successfully"
        ], 201);
    }
}
