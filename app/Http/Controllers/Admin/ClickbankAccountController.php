<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClickbankAccountStoreRequest;
use App\Http\Requests\ClickbankAccountUpdateRequest;
use App\Models\ClickbankAccount;
use App\Models\ClickbankProduct;
use Carbon\Carbon;

class ClickbankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:clickbank-account-list|clickbank-account-create|clickbank-account-edit|clickbank-account-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:clickbank-account-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:clickbank-account-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:clickbank-account-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cb_accounts = ClickbankAccount::all();
        return view('admin.clickbank_accounts.index', compact('cb_accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.clickbank_accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ClickbankAccountStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClickbankAccountStoreRequest $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        $objClickbankAcc = ClickbankAccount::create(['name' => $request->input('name')]);

        return redirect()->route('admin.clickbank_accounts.index')
            ->with(
                'flash_success_message',
                trans('global.data_created', ['name' => "$objClickbankAcc->name"])
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
        $clickbank_account = ClickbankAccount::find($id);
        $clickbank_products = ClickbankProduct::where("clickbank_account_id", $id)->get();
        return view('admin.clickbank_accounts.edit', compact('clickbank_account', 'clickbank_products'));
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
            'name' => 'required|min:3|unique:clickbank_accounts,name,' . $id,
        ]);

        if (isset($request->btnFetch)) {

            $this->curlFetchLatestProducts($id, $request->input('name'));
        } else {
            // Update clickbank account name
            $objClickbankAcc = ClickbankAccount::find($id);
            $objClickbankAcc->name = $request->input('name');
            $objClickbankAcc->save();
        }
        $cbaccount_name = $request->input('name');

        return redirect()->route('admin.clickbank_accounts.index')
            ->with(
                'flash_success_message',
                trans('global.data_updated', ['name' => "$cbaccount_name"])
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
        $objClickbankAcc = ClickbankAccount::whereId($id)->first();
        $objClickbankAcc->delete();

        return redirect()->route('admin.clickbank_accounts.index')
            ->with(
                'flash_warning_message',
                trans('global.data_deleted', ['name' => "$objClickbankAcc->name"])
            );
    }

    public function curlFetchLatestProducts($vendor_id, $vendor)
    {
        // echo $vendor;

        $site = strtolower($vendor);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.clickbank.com/rest/1.3/products/list?site=$site");

        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization:DEV-PMP8PL7CANEMMUNLBS940FCGM7OLDEP4:API-080K3VBKB1BKM5982RGEF1CAMFMNKJID"));

        //curl_close($ch);
        $data = json_decode(curl_exec($ch), true);
        //print_r($data);
        //print_r($result);
        //$api_result = json_decode($result);
        echo '<pre>';
        $arr_products = [];
        if (isset($data["products"]["product"])) {
            $api_result = $data["products"]["product"];

            ClickbankProduct::where('clickbank_account_id', $vendor_id)->delete();
            foreach ($api_result as $cKey => $cVal) {
                if ($cKey > 0) {
                    //$arr_products[] = array('sku' => $cVal['@sku'], 'title' => $cVal['title']);

                    ClickbankProduct::insert([
                        'clickbank_account_id' => $vendor_id,
                        'product_id' => $cVal['@sku'],
                        'name' => $cVal['title'],
                        'updated_at' =>  Carbon::now(),
                        'created_at' =>  Carbon::now()
                    ]);
                }
            }
        }
    }
}
