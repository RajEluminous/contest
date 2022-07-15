<?php

namespace App\Http\Controllers\Admin;

use App\Models\CbMasterList;
use App\Models\AffiliateMaster;
use App\Models\PartnerMaster;
use App\Http\Controllers\Controller;
use App\Services\UploadImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AffiliateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:affiliate-list|affiliate-create|affiliate-edit|affiliate-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:affiliate-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:affiliate-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:affiliate-delete', ['only' => ['destroy']]);
        $this->middleware('permission:affiliate-block-id', ['only' => ['block_affiliate']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Process Mapping request
        if ($request->all() && !isset($request->page) && isset($request->affiliate)  && isset($request->partner)) {

            // Redirect to list
            $request->validate([
                'affiliate' => 'required',
                'partner' => 'required'
            ]);

            $CbMaster = CbMasterList::where([
                'affiliate_id' => $request->affiliate,
                'partner_id' => $request->partner
            ])->first();

            if ($CbMaster === null) {

                if (isset($request->recid) && $request->recid > 0) {

                    CbMasterList::where('id', $request->recid)->update([
                        'affiliate_id' => $request->affiliate,
                        'partner_id' => $request->partner
                    ]);
                    return redirect()->back()->with('flash_success_message', 'Affiliate - Partner record updated successfully.');
                } else {
                    CbMasterList::insert([
                        'affiliate_id' => $request->affiliate,
                        'partner_id' => $request->partner
                    ]);
                    return redirect()->back()->with('flash_success_message', 'Affiliate assigned to Partner successfully.');
                }
            } else {
                return redirect()->back()->with('flash_error_message', 'Provided Affiliate - Partner already exist.');
            }
        }

        $limitRec = 10;
        $cntr = 0;
        if (isset($request->page)) {
            $cntr = $request->page * $limitRec - $limitRec;
        }

        list($affArry, $partArry) = $this->getCBMasterName();

        if (isset($request->fAffiliate)) {
            $cbmasterlist = CbMasterList::where('affiliate_id', $request->fAffiliate)->paginate($limitRec);
        } else if (isset($request->fPartner)) {
            $cbmasterlist = CbMasterList::where('partner_id', $request->fPartner)->paginate($limitRec);
        } else {
            $cbmasterlist = CbMasterList::orderBy('id', 'desc')->paginate($limitRec);
        }

        //dd('test');
        $affPartLists = array();
        $counter = $cntr + 1;
        foreach ($cbmasterlist as $rs) {
            if (isset($affArry[$rs->affiliate_id]) && isset($partArry[$rs->partner_id])) {
                $affPartLists[] = array(
                    'id' => $rs->id,
                    'aff_id' => $rs->affiliate_id,
                    'affiliate_id' => $affArry[$rs->affiliate_id],
                    'part_id' => $rs->partner_id,
                    'partner_id' => $partArry[$rs->partner_id],
                    'status' => $rs->status,
                    'count' => $counter
                );
                $counter++;
            }
        }

        return view('admin.affiliates.index', compact('affPartLists', 'affArry', 'partArry', 'cbmasterlist'));
    }

    public function create_affiliate(Request $request)
    {
        // Process request
        if ($request->all() && !isset($request->page) && $request->submit != 'Search') {
            // redirect to list
            $request->validate([
                'name' => 'required' /*|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/*/
            ]);

            $aff = AffiliateMaster::where('name', '=', $request->name)->first();
            if ($aff === null) {
                if (isset($request->recid) && $request->recid > 0) {
                    AffiliateMaster::where('id', $request->recid)->update([
                        'name' => $request->name
                    ]);
                    return redirect('/admin/affiliates/create_affiliate')->with('flash_success_message', $request->name . ' Affiliate ID updated successfully.');
                } else {
                    $request['status'] = 'ACTIVE';
                    AffiliateMaster::create($request->all());
                    return redirect('/admin/affiliates/create_affiliate')->with('flash_success_message', $request->name . ' Affiliate ID added successfully.');
                }
            } else {
                return redirect('/admin/affiliates/create_affiliate')->with('flash_error_message', $request->name . ' Affiliate ID already exist.');
            }
        }

        $cbmasterlist = CbMasterList::orderBy('id', 'desc')->get();
        $cbArr = array();
        foreach ($cbmasterlist as $af) {
            $cbArr[] = $af->affiliate_id;
        }

        // Get list of Affiliates
        $limitRec = 10;
        $cntr = 0;
        if (isset($request->page)) {
            $cntr = $request->page * $limitRec - $limitRec;
        }

        if (isset($_POST['submit']) && $request->submit == 'Search')
            $affiliatelist = AffiliateMaster::where('name', 'like', '%' . $request->name . '%')->paginate($limitRec);
        else
            $affiliatelist = AffiliateMaster::orderBy('name', 'asc')->paginate($limitRec);

        $affArr = array();
        $counter = $cntr + 1;
        foreach ($affiliatelist as $rs) {
            $isInMasterList = false;
            if (!isset($cbArr[$rs->id])) {
                $isInMasterList = true;
            }

            $affArr[] = array(
                'id' => $rs->id,
                'name' => $rs->name,
                'image' => $rs->image,
                'count' => $counter,
                'isInMasterList' => $isInMasterList
            );
            $counter++;
        }

        return view('admin.affiliates.create_affiliate', compact('affArr', 'affiliatelist'));
    }

    public function create_partner(Request $request)
    {
        // Process request
        if ($request->all() && !isset($request->page) && $request->submit != 'Search') {

            $request->validate([
                'name' => 'required' /*|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/*/
            ]);

            $part = PartnerMaster::where('name', '=', $request->name)->first();
            if ($part === null) {

                if (isset($request->recid) && $request->recid > 0) {
                    PartnerMaster::where('id', $request->recid)->update([
                        'name' => $request->name
                    ]);
                    return redirect('/admin/affiliates/create_partner')->with('flash_success_message', 'Partner updated successfully.');
                } else {
                    PartnerMaster::create($request->all());
                    return redirect('/admin/affiliates/create_partner')->with('flash_success_message', 'Partner added successfully.');
                }
            } else {
                return redirect('/admin/affiliates/create_partner')->with('flash_error_message', 'Partner already exist.');
            }
        }

        $cbmasterlist = CbMasterList::orderBy('id', 'desc')->get();
        $cbArr = array();
        foreach ($cbmasterlist as $af) {
            $cbArr[] = $af->partner_id;
        }

        $limitRec = 10;
        $cntr = 0;
        if (isset($request->page)) {
            $cntr = $request->page * $limitRec - $limitRec;
        }

        if (isset($_POST['submit']) && $request->submit == 'Search')
            $partnerlist = PartnerMaster::where('name', 'like', '%' . $request->name . '%')->paginate($limitRec);
        else
            $partnerlist = PartnerMaster::orderBy('name', 'asc')->paginate($limitRec);

        $partArr = array();
        $counter = $cntr + 1;
        foreach ($partnerlist as $rs) {
            $isInMasterList = false;
            if (!in_array($rs->id, $cbArr)) {
                $isInMasterList = true;
            }

            $partArr[] = array(
                'id' => $rs->id,
                'name' => $rs->name,
                'count' => $counter,
                'isInMasterList' => $isInMasterList
            );
            $counter++;
        }

        return view('admin.affiliates.create_partner', compact('partArr', 'partnerlist'));
    }

    // To block affiliate
    public function block_affiliate(Request $request)
    {
        // block the affiliate in request
        if ($request->all() && isset($request->fAffiliate)) {
            $aff_block = AffiliateMaster::find($request->fAffiliate);;
            $aff_block->status = 'SUSPENDED';
            $aff_block->update();
        }

        list($affArry, $partArry) = $this->getCBMasterName();

        // Get list of Affiliates
        $limitRec = 10;
        $cntr = 0;
        if (isset($request->page)) {
            $cntr = $request->page * $limitRec - $limitRec;
        }

        $affiliatelist = AffiliateMaster::where('status', 'SUSPENDED')->paginate($limitRec);

        $affArr = array();
        $counter = $cntr + 1;
        foreach ($affiliatelist as $rs) {

            $affArr[] = array(
                'id' => $rs->id,
                'name' => $rs->name,
                'count' => $counter
            );
            $counter++;
        }

        return view('admin.affiliates.block_affiliate', compact('affArr', 'affArry', 'affiliatelist'));
    }

    public function unblock_affiliate($id)
    {
        $aff_block = AffiliateMaster::find($id);;
        $aff_block->status = 'ACTIVE';
        $aff_block->update();
        return redirect()->back()->with('flash_success_message', 'Affiliate unblocked successfully.');
    }

    public function store(Request $request)
    {
        // dd($request->all());
    }

    public function getCBMasterName()
    {
        $aff_list = AffiliateMaster::orderBy('name', 'asc')->get();
        $affArry = array();
        foreach ($aff_list as $rsAff) {
            $affArry[$rsAff->id] = $rsAff->name;
        }

        $part_list = PartnerMaster::orderBy('name', 'asc')->get();
        $partArry = array();
        foreach ($part_list as $rsPart) {
            $partArry[$rsPart->id] = $rsPart->name;
        }
        return array($affArry, $partArry);
    }

    public function destroy($id)
    {
        $cb_master = CbMasterList::findOrFail($id);
        $cb_master->delete();

        return redirect()->back()->with('flash_warning_message', 'Affiliate ID - Partner Name is successfully deleted.');
    }

    public function deleteaaffiliate($id)
    {
        $affiliate_master = AffiliateMaster::findOrFail($id);
        $affiliate_master->delete();

        return redirect()->back()->with('flash_warning_message', 'Affiliate ID is successfully deleted.');
    }

    public function deleteapartner($id)
    {
        $partner_master = PartnerMaster::findOrFail($id);
        $partner_master->delete();

        return redirect()->back()->with('flash_warning_message', 'Partner Name is successfully deleted.');
    }

    public function saveimage(Request $request, UploadImageService $uploadImageService)
    {
        $validator = Validator::make($request->all(), [
            'photo_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('flash_error_message',  'Invalid file type or image size is greater then 2 MB');
        }

        $aff = AffiliateMaster::findOrFail($request->aff_id);

        if ($request->file('photo_name')) {
            $uploadImageService->checkDirectoryIfExits('aff_images');
            $uploadImageService->checkPathExitsThenDelete('aff_images', $aff->image);

            $img_name  = $request->aff_id . '_' . time();
            $img_extension = $request->file('photo_name')->getClientOriginalExtension();
            $img_content = Image::make($request->file('photo_name'))->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            })->stream();
            $uploadImageService->storeFile('aff_images', $img_name, $img_extension, $img_content);

            $photo = AffiliateMaster::find($request->aff_id);
            $photo->image = $img_name . '.' . $img_extension;
            $photo->update();
        }

        return redirect()->back()->with('flash_success_message', 'Image uploaded successfully.');
    }
}
