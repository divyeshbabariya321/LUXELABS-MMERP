<?php

namespace App\Http\Controllers;
use App\Setting;

use App\CouponType;
use App\Http\Requests\SaveRemarksVoucherCouponRequest;
use App\Models\VoucherCouponStatus;
use App\Models\VoucherCouponStatusHistory;
use App\Platform;
use App\VoucherCoupon;
use App\VoucherCouponCode;
use App\VoucherCouponOrder;
use App\VoucherCouponRemark;
use App\EmailAddress;
use App\Marketing\WhatsappConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;

class VoucherCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $voucher = new VoucherCoupon();

        $voucher = $voucher->select('voucher_coupons.*', 'wc.number', 'em.from_address', 'vcp.name AS plateform_name', 'u.name As user_name')
            ->with(['voucherCouponRemarks' => function ($q) {
                $q->select('id', 'voucher_coupons_id', 'remark');
            }])
            ->leftJoin('users As u', 'voucher_coupons.user_id', 'u.id')
            ->leftJoin('whatsapp_configs As wc', 'voucher_coupons.whatsapp_config_id', 'wc.id')
            ->leftJoin('email_addresses As em', 'voucher_coupons.email_address_id', 'em.id')
            ->leftJoin('voucher_coupon_platforms As vcp', 'voucher_coupons.platform_id', 'vcp.id');
        if (! empty(request('plateform_id'))) {
            $voucher = $voucher->where('platform_id', request('plateform_id'));
        }
        if (! empty(request('email_add'))) {
            $voucher = $voucher->where('email_address_id', request('email_add'));
        }
        if (! empty(request('whatsapp_id'))) {
            $voucher = $voucher->where('whatsapp_config_id', request('whatsapp_id'));
        }
        $voucher = $voucher->orderByDesc('id')->paginate(25)->appends(request()->except('page'));

        $platform = Platform::get()->pluck('name', 'id');
        $whatsapp_configs = WhatsappConfig::get()->pluck('number', 'id');
        $emails = EmailAddress::get()->pluck('id', 'from_address');
        $coupontypes = CouponType::get()->pluck('name', 'id');
        $status = VoucherCouponStatus::all();

        return view('voucher-coupon.index', compact('voucher', 'platform', 'whatsapp_configs', 'emails', 'coupontypes', 'status'));
    }

    public function saveRemarks(SaveRemarksVoucherCouponRequest $request): JsonResponse
    {
        $post = $request->all();

        $input = $request->except(['_token']);
        VoucherCouponRemark::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function getRemarksHistories(Request $request): JsonResponse
    {
        $datas = VoucherCouponRemark::where('voucher_coupons_id', $request->voucher_coupons_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = VoucherCouponStatus::find($key);
            $bugstatus->status_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function statusCreate(Request $request): JsonResponse
    {
        try {
            $status = new VoucherCouponStatus();
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $voucherId = $request->input('voucherId');
        $selectedStatus = $request->input('selectedStatus');

        $voucher = VoucherCoupon::find($voucherId);
        $history = new VoucherCouponStatusHistory();
        $history->voucher_coupons_id = $voucherId;
        $history->old_value = $voucher->status_id;
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        $voucher->status_id = $selectedStatus;
        $voucher->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function statusHistories($id): JsonResponse
    {
        $datas = VoucherCouponStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('voucher_coupons_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if ($request->id) {
                $plate = VoucherCoupon::find($request->id);
            } else {
                $plate = new VoucherCoupon();
            }
            $plate->user_id = Auth::user()->id ?? '';
            $plate->platform_id = $request->plateform_id ?? '';
            $plate->email_address_id = $request->email_id ?? '';
            $plate->whatsapp_config_id = $request->whatsapp_config_id ?? '';
            $plate->password = $request->password ?? '';
            $plate->save();

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function plateformStore(Request $request): JsonResponse
    {
        try {
            $plate = new Platform();
            $plate->name = $request->plateform_name;
            $plate->save();

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(VoucherCoupon $voucherCoupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VoucherCoupon  $voucherCoupon
     */
    public function edit(Request $request): JsonResponse
    {
        try {
            $vou = VoucherCoupon::find($request->id);

            return response()->json(['code' => 200, 'data' => $vou, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\VoucherCoupon  $voucherCoupon
     * @param  mixed  $id
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $vou = VoucherCoupon::find($id);
            $vou->remark = $request->remark;
            $vou->save();

            return response()->json(['code' => 200, 'message' => 'Remark Updated successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VoucherCoupon  $voucherCoupon
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $vou = VoucherCoupon::find($request->id);
            $vou->delete();

            return response()->json(['code' => 200, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function couponCodeCreate(Request $request): JsonResponse
    {
        try {
            if ($request->id) {
                $code = VoucherCouponCode::find($request->id);
            } else {
                $code = new VoucherCouponCode();
            }
            $code->user_id = Auth::user()->id ?? '';
            $code->voucher_coupons_id = $request->voucher_coupons_id;
            $code->coupon_code = $request->coupon_code ?? '';
            $code->valid_date = date('Y-m-d', strtotime($request->valid_date)) ?? '';
            $code->remark = $request->code_remark ?? '';
            $code->coupon_type_id = $request->coupon_type_id ?? '';
            $code->save();

            return response()->json(['code' => 200, 'message' => 'Coupon Code Added successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function couponCodeList(Request $request): JsonResponse
    {
        try {
            $vouCode = VoucherCouponCode::select('voucher_coupon_codes.*', 'users.name AS userName', 'vcp.name AS plateform_name', 'Vct.name AS couponType')
                ->leftJoin('users', 'users.id', 'voucher_coupon_codes.user_id')
                ->where('voucher_coupon_codes.voucher_coupons_id', $request->voucher_coupons_id)
                ->leftJoin('voucher_coupons As vc', 'vc.id', 'voucher_coupon_codes.voucher_coupons_id')
                ->leftJoin('voucher_coupon_platforms As vcp', 'vc.platform_id', 'vcp.id')
                ->leftJoin('voucher_coupon_types AS Vct', 'Vct.id', 'voucher_coupon_codes.coupon_type_id')
                ->get();

            return response()->json(['code' => 200, 'data' => $vouCode, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function couponCodeDelete(Request $request): JsonResponse
    {
        try {
            $vou = VoucherCouponCode::find($request->id);
            $vou->delete();

            return response()->json(['code' => 200, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function couponCodeOrderCreate(Request $request): JsonResponse
    {
        try {
            if ($request->id) {
                $code = VoucherCouponOrder::find($request->id);
            } else {
                $code = new VoucherCouponOrder();
            }
            $code->user_id = Auth::user()->id ?? '';
            $code->voucher_coupons_id = $request->voucher_coupons_id;
            $code->date_order_placed = date('Y-m-d', strtotime($request->date_order_placed)) ?? '';
            $code->order_no = $request->order_no ?? '';
            $code->order_amount = $request->order_amount ?? '';
            $code->discount = $request->discount ?? '';
            $code->final_amount = $request->final_amount ?? '';
            $code->refund_amount = $request->refund_amount ?? '';
            $code->remark = $request->code_remark ?? '';
            $code->save();

            return response()->json(['code' => 200, 'message' => 'Coupon Code Order Added successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function couponCodeOrderList(Request $request): JsonResponse
    {
        try {
            $vouCodeO = VoucherCouponOrder::select('voucher_coupon_orders.*', 'users.name AS userName')->leftJoin('users', 'users.id', 'voucher_coupon_orders.user_id')->where('voucher_coupon_orders.voucher_coupons_id', $request->voucher_coupons_id)->get();

            return response()->json(['code' => 200, 'data' => $vouCodeO, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function couponCodeOrderDelete(Request $request): JsonResponse
    {
        try {
            $vou = VoucherCouponOrder::find($request->id);
            $vou->delete();

            return response()->json(['code' => 200, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store Remark the specified resource in storage.
     *
     * @param  \App\VoucherCoupon  $voucherCouponId
     * @param  mixed  $id
     */
    public function storeRemark(Request $request, $id): JsonResponse
    {
        try {
            $voucherCouponRemark = new VoucherCouponRemark;
            $voucherCouponRemark->voucher_coupons_id = $id;
            $voucherCouponRemark->remark = $request->remark;
            $voucherCouponRemark->save();

            return response()->json(['code' => 200, 'message' => 'Remark Updated successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function coupontypeStore(Request $request): JsonResponse
    {
        try {
            $couponType = new CouponType();
            $couponType->name = $request->coupon_type_name;
            $couponType->save();

            return response()->json(['code' => 200, 'message' => 'Coupon type Created successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function couponTypeList(): JsonResponse
    {
        try {
            $perPage = 10;
            $couponTypeLists = new VoucherCouponCode();

            $couponTypeLists = $couponTypeLists->select('voucher_coupon_codes.*', 'users.name AS userName', 'vcp.name AS plateform_name', 'Vct.name AS couponType')
                ->leftJoin('users', 'users.id', 'voucher_coupon_codes.user_id')
                ->leftJoin('voucher_coupons As vc', 'vc.id', 'voucher_coupon_codes.voucher_coupons_id')
                ->leftJoin('voucher_coupon_platforms As vcp', 'vc.platform_id', 'vcp.id')
                ->leftJoin('voucher_coupon_types AS Vct', 'Vct.id', 'voucher_coupon_codes.coupon_type_id')
                ->latest()
                ->paginate($perPage);

            return response()->json(['code' => 200, 'data' => $couponTypeLists, 'count' => count($couponTypeLists), 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function voucherscouponCodeList(Request $request): View
    {
        $vouCode = new VoucherCouponCode();
        $vouCode = $vouCode->with('voucherCoupon.platform');

        if (($request->coupon_code) !== null) {
            $vouCode = $vouCode->where('coupon_code', 'LIKE', '%'.$request->coupon_code.'%');
        }
        if ($request->coupon_types_ids !== null) {
            $vouCode = $vouCode->whereIn('coupon_type_id', $request->coupon_types_ids);
        }
        if ($request->username_ids !== null) {
            $vouCode = $vouCode->whereIn('user_id', $request->username_ids);
        }
        if ($request->date !== null) {
            $vouCode = $vouCode->where('valid_date', 'LIKE', '%'.$request->date.'%');
        }
        if ($request->platform_ids !== null) {
            $vouCode->whereHas('voucherCoupon.platform', function ($query) use ($request) {
                $query->where('platform_id', $request->platform_ids);
            });
        }

        $vouCode = $vouCode->latest()->paginate(Setting::get('pagination', 10));

        return view('voucher-coupon.voucher-code-listing', compact('vouCode'));
    }
}
