<?php

namespace App\Helpers;
use App\VendorStatus;
use App\Models\VendorRatingQAStatusHistory;
use App\Models\VendorRatingQAStatus;
use App\Models\VendorQuestionStatusHistory;
use App\Models\VendorQuestionStatus;
use App\Models\VendorFlowChartStatusHistory;
use App\Models\VendorFlowChartStatus;
use App\Helpers;

use App\Models\VendorFrameworks;
use App\Vendor;
use App\VendorCategory;

class VendorHelper
{
    public static function getVendorFrameworks()
    {
        return VendorFrameworks::all();
    }

    public static function getVendorCategory()
    {
        return VendorCategory::pluck('title', 'id')->toArray();
    }

    public static function getBlockedVendor()
    {
        return Vendor::where('is_blocked', 1)->get();
    }

    public static function getVendorStatusById($id)
    {
        return VendorStatus::where('id', $id)->first();
    }

    public static function getVendorFrameworksByIds($ids)
    {
        return VendorFrameworks::whereIn('id', $ids)->pluck('name', 'id');
    }

    public static function getVendorRatingQAStatusHistoryByQidAndVendorId($QuestionId, $vendorId)
    {
        return VendorRatingQAStatusHistory::where('question_id', $QuestionId)->where('vendor_id', $vendorId)->orderBy('id', 'DESC')->first();
    }

    public static function getVendorRatingQAStatusById($id)
    {
        return VendorRatingQAStatus::where('id', $id)->first();
    }

    public static function getVendorQuestionStatusHistoryByQidAndVendorId($QuestionId, $vendorId)
    {
        return VendorQuestionStatusHistory::where('question_id', $QuestionId)->where('vendor_id', $vendorId)->orderBy('id', 'DESC')->first();
    }

    public static function getVendorQuestionStatusById($id)
    {
        return VendorQuestionStatus::where('id', $id)->first();
    }

    public static function getVendorFlowChartStatusHistoryByFlowChartAndVendorId($FlowChartId, $vendorId)
    {
        return VendorFlowChartStatusHistory::where('flow_chart_id', $FlowChartId)->where('vendor_id', $vendorId)->orderBy('id', 'DESC')->first();
    }

    public static function getVendorFlowChartStatusById($id)
    {
        return VendorFlowChartStatus::where('id', $id)->first();
    }
}
