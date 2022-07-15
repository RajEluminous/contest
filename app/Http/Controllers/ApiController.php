<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LeadCountRaw;
use App\Models\ClickbankProduct;
use Carbon\Carbon;
use DB;

class ApiController extends Controller
{
    public function saveLead(Request $request)
    {

        $app_secret_client_key = config('app.secret_client_key');
        $auth_token =  $request->header('auth_token');

        //Verify the secret code
        if ((NULL !== $request->header('auth_token')) && (config('app.secret_client_code') == $this->safeDecrypt($auth_token, $app_secret_client_key))) {

            //########### Processing of DATA #############

            $validator =  Validator::make($request->all(), [
                'cb_account' => 'required|string',
                'affiliate_id' => 'required|string',
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "error" => 'validation_error',
                    "message" => $validator->errors(),
                ], 422);
            }

            try {

                $arrContestData = array_change_key_case($this->getContestId($request->cb_account, $request->affiliate_id),CASE_UPPER );
                //dd($arrContestData);

                $cb_account = strtoupper($request->cb_account);
                $affiliate_id = strtoupper($request->affiliate_id);
                if(isset($arrContestData[$cb_account])) {
                    $this->saveLeadData($arrContestData[$cb_account],$cb_account,$affiliate_id,$request->email);
                }
                return response()->json([
                    "message" => "Lead saved successfully"
                ], 201);
            } catch (Exception $e) {

                return response()->json([
                    "error" => "could_not_save_lead",
                    "message" => "Unable to save lead"
                ], 400);
            }
            ######### End of processing code ############

        } else {
            return response()->json([
                "error" => "invalid_auth_token",
                "message" => "Unauthorized"
            ], 400);
        }
    }

    function saveLeadData($contest_id, $cb_account, $affiliate_id, $email)
    {

        $exist = LeadCountRaw::where('contest_id', '=', $contest_id)
            ->where('cb_account', '=', $cb_account)
            ->where('affiliate_id', '=', $affiliate_id)
            ->where('email', '=', $email)
            ->first();
        if ($exist == null) :
            LeadCountRaw::insert(['contest_id' => $contest_id, 'cb_account' => $cb_account, 'affiliate_id' => $affiliate_id, 'email' => $email, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        else :
        // $id = $exist->id;
        // $row = LeadCountRaw::find($id);
        // $row->counts = DB::raw('counts + 1');
        // $row->updated_at = Carbon::now();
        // $row->save();
        endif;
    }

    function getContestId($cb_account, $affiliate_id)
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
            ->where('contest_cb_products.flag_contest_type', 'LEAD')
            ->where('contests.status', 'RUNNING')
            ->where('contests.start_date_curl', '<', $todays_datetime)
            ->where('contests.end_date_curl', '>=', $todays_datetime)
            ->get();
        $array_cb_data = [];
        foreach ($contest as $rs_contest) {
            // echo '<br>------------------';
            // echo '<br>'.$rs_contest->contest_id;
            // echo '<br>'.$rs_contest->contest_name;
            // echo '<br>'.$rs_contest->contest_type;
            // echo '<br>'.$rs_contest->contest_status;
            // echo '<br>'.$rs_contest->clickbank_account_id;
            // echo '<br>'.$rs_contest->clickbank_account_name;
            // echo '<br>------------------';
            if (!empty($rs_contest->contest_id) && !empty($rs_contest->clickbank_account_name)) {
                //$array_cb_data[$rs_contest->contest_id][] = $rs_contest->clickbank_account_name;
                $array_cb_data[$rs_contest->clickbank_account_name] = $rs_contest->contest_id;
            }
        }

        return ($array_cb_data);
    }

    /**
     * Encrypt a message
     *
     * @param string $message - message to encrypt
     * @param string $key - encryption key
     * @return string
     * @throws RangeException
     */
    function safeEncrypt(string $plaintext, string $key): string
    {

        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }

    /**
     * Decrypt a message
     *
     * @param string $encrypted - message encrypted with safeEncrypt()
     * @param string $key - encryption key
     * @return string
     * @throws Exception
     */
    function safeDecrypt(string $ciphertext, string $key): string
    {
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        if (hash_equals($hmac, $calcmac)) // timing attack safe comparison
        {
            $original_plaintext;
        }
        return $original_plaintext;
    }

    // Ajax call to get the Products from CB account
    public function ajxGetProducts(Request $request)
    {

        $cb_products = ClickbankProduct::where('product_id', 'LIKE', '%' . $request->input('term', '') . '%')
            ->where('clickbank_account_id', $request->cb_account_id)
            ->get(['id', 'product_id as text']);

        return ['results' => $cb_products];
    }
}
