<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Setting;
use App\Position;
use App\VendorResume;
use Illuminate\Http\Request;
use Exception;

class VendorResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $resumes = VendorResume::latest()->paginate(Setting::get('pagination'));

        return view('vendors.list-cv', [
            'resumes' => $resumes,
        ]);
    }

    public function search(Request $request): View
    {
        $resumes = VendorResume::latest();
        //if vendoe_id is not null
        if ($request->vendoe_id != null) {
            $resumes->where('vendoe_id', $request->vendoe_id);
        }

        //If first_name is not null
        if ($request->first_name != null) {
            $resumes->where('first_name', 'LIKE', '%' . $request->first_name . '%');
        }

        //if second_name is not null
        if ($request->second_name != null) {
            $resumes->where('second_name', 'LIKE', '%' . $request->second_name . '%');
        }

        //if email is not null
        if ($request->email != null) {
            $resumes->where('email', 'LIKE', '%' . $request->email . '%');
        }

        //if mobile is not null
        if ($request->mobile != null) {
            $resumes->where('mobile', 'LIKE', '%' . $request->mobile . '%');
        }

        //if expected_salary_in_usd is not null
        if ($request->expected_salary_in_usd != null) {
            $resumes->where('expected_salary_in_usd', 'LIKE', '%' . $request->expected_salary_in_usd . '%');
        }

        $resumes = $resumes->paginate(Setting::get('pagination'));

        return view('vendors.list-cv', [
            'resumes' => $resumes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getWorkExperience(Request $request): JsonResponse
    {
        try {
            $resumes = VendorResume::where('id', $request->id)->first();
            //if vendoe_id is not null
            if ($request->id != null) {
                //
            }
            $html                 = '<tr><td colspan="5">Work Experience(Please add details of your work experience with the latest records first)</td></tr>';
            $workExperiance       = unserialize($resumes->work_experiance);
            $salary_in_usd        = unserialize($resumes->salary_in_usd);
            $date_from            = unserialize($resumes->date_from);
            $date_to              = unserialize($resumes->date_to);
            $organization         = unserialize($resumes->organization);
            $designation          = unserialize($resumes->designation);
            $reason_for_leaving   = unserialize($resumes->reason_for_leaving);
            $part_time            = unserialize($resumes->part_time);
            $full_time            = unserialize($resumes->full_time);
            $job_responsibilities = unserialize($resumes->job_responsibilities);
            $projects_worked      = unserialize($resumes->projects_worked);
            $tool_used            = unserialize($resumes->tool_used);
            $work_remark          = unserialize($resumes->work_remark);
            $project              = unserialize($resumes->project);
            $dev_role             = unserialize($resumes->dev_role);
            $tools                = unserialize($resumes->tools);

            $counter = 0;
            foreach ($workExperiance as $key => $val) {
                $salary_in_usd_val        = $salary_in_usd[$key] ?? '';
                $date_from_val            = $date_from[$key] ?? '';
                $date_to_val              = $date_to[$key] ?? '';
                $organization_val         = $date_to[$key] ?? '';
                $designation_val          = $designation[$key] ?? '';
                $reason_for_leaving_val   = $reason_for_leaving[$key] ?? '';
                $part_time_val            = $part_time[$key] ?? '';
                $full_time_val            = $full_time[$key] ?? '';
                $job_responsibilities_val = $job_responsibilities[$key] ?? '';
                $projects_worked_val      = $projects_worked[$key] ?? '';
                $tool_used_val            = $tool_used[$key] ?? '';
                $work_remark_val          = $work_remark[$key] ?? '';

                $html .= '<tr>';
                $html .= '<td><b>Salary IN USD </b>: ' . $salary_in_usd_val . '</td>';
                $html .= '<td><b>From Date </b>: ' . $date_from_val . '</td>';
                $html .= '<td><b>To Date </b>: ' . $date_to_val . '</td>';
                $html .= '<td><b>Organization </b>: ' . $organization_val . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td><b>Designation </b>: ' . $designation_val . '</td>';
                $html .= '<td><b>Reason for Leaving </b>: ' . $reason_for_leaving_val . '</td>';
                $html .= '<td><b>Part Time </b>: ' . $part_time_val . '</td>';
                $html .= '<td><b>Full Time </b>: ' . $full_time_val . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="4"><b>Job Responsibilities </b>: ' . $job_responsibilities_val . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="4"><b>Projects Worked </b>: ' . $projects_worked_val . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="4"><b>Tool Used </b>: ' . $tool_used_val . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="4"><b>Remark </b>: ' . $work_remark_val . '</td>';
                $html .= '</tr>';

                foreach ($project as $prokey => $pval) {
                    $html .= '<tr>';
                    $html .= '<td colspan="6"><b>Project ' . ($prokey + 1) . '</b></td>';
                    $html .= '</tr>';
                    foreach ($pval as $prokey1 => $pval1) {
                        $html .= '<tr>';
                        $html .= '<td><b>Project ' . ($pval1) . '</b></td>';
                        $html .= '<td><b>Role ' . ($dev_role[$prokey][$prokey][$prokey1]) . '</b></td>';
                        $html .= '<td><b>Project ' . ($tools[$prokey][$prokey][$prokey1]) . '</b></td>';
                        $html .= '</tr>';
                    }
                }
                $counter++;
            }

            return response()->json(['code' => 200, 'data' => $html, 'message' => 'Data listed successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function getEducation(Request $request): JsonResponse
    {
        try {
            $resumes = VendorResume::where('id', $request->id)->first();

            $html                    = '<tr><td colspan="5"><b>Educational Qualifications</b></td></tr>';
            $edu_date_from           = unserialize($resumes->edu_date_from);
            $edu_date_to             = unserialize($resumes->edu_date_to);
            $edu_institute_programme = unserialize($resumes->edu_institute_programme);
            $edu_course_name         = unserialize($resumes->edu_course_name);
            $edu_grades              = unserialize($resumes->edu_grades);
            $edu_remark              = unserialize($resumes->edu_remark);
            foreach ($edu_date_from as $key => $val) {
                $edu_date_to_val             = $edu_date_to[$key] ?? '';
                $edu_institute_programme_val = $edu_institute_programme[$key] ?? '';
                $edu_course_name_val         = $edu_course_name[$key] ?? '';
                $edu_grades_val              = $edu_grades[$key] ?? '';
                $edu_remark_val              = $edu_remark[$key] ?? '';

                $html .= '<tr>';
                $html .= '<td><b>From Date </b>: ' . $val . '</td>';
                $html .= '<td><b>To Date </b>: ' . $edu_date_to_val . '</td>';
                $html .= '<td><b>Institute Programme </b>: ' . $edu_institute_programme_val . '</td>';
                $html .= '<td><b>Course Name </b>: ' . $edu_course_name_val . '</td>';
                $html .= '<td><b>Grades </b>: ' . $edu_grades_val . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="4"><b>Remark </b>: ' . $edu_remark_val . '</td>';
                $html .= '</tr>';
            }

            return response()->json(['code' => 200, 'data' => $html, 'message' => 'Educational Data listed successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function create($vendor_id = null): View
    {
        $positions = Position::get();

        return view('vendor-resume.create', ['vendor_id' => $vendor_id, 'positions' => $positions]);
    }

    public function getAddress(Request $request): JsonResponse
    {
        try {
            $resumes = VendorResume::where('id', $request->id)->first();

            $html    = '<tr><td colspan="5"><b>Address</b></td></tr>';
            $address = unserialize($resumes->address);

            $html .= '<tr>';
            $html .= '<td><b>City </b>: ' . $resumes->city . '</td>';
            $html .= '<td><b>State </b>: ' . $resumes->state . '</td>';
            $html .= '<td><b>Country </b>: ' . $resumes->country . '</td>';
            $html .= '</tr>';

            foreach ($address as $key => $val) {
                $html .= '<tr>';
                $html .= '<td colspan="4"><b>Address ' . ($key + 1) . '</b>:</td>';
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td colspan="4">' . $val . '</td>';
                $html .= '</tr>';
            }

            return response()->json(['code' => 200, 'data' => $html, 'message' => 'Educational Data listed successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $requestData = $request->all();
        $project     = [];
        $dev_role    = [];
        $tools       = [];

        foreach ($requestData['work_experiance'] as $key => $val) {
            if (isset($requestData['project' . $key])) {
                foreach ($requestData['project' . $key] as $pKey => $pval) {
                    $project[$key][$pKey]  = $pval;
                    $dev_role[$key][$pKey] = $request->input('dev_role' . $key);
                    $tools[$key][$pKey]    = $request->input('tools' . $key);
                }
            }
        }

        $path = public_path('vendorResume');
        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $vendorResumeName = '';
        if ($request->file('soft_upload_document')) {
            $file             = $request->file('soft_upload_document');
            $vendorResumeName = uniqid() . '_' . trim($file->getClientOriginalName());
            $file->move($path, $vendorResumeName);
        }
        try {
            $vendorResume              = new VendorResume();
            $vendorResume->vendor_id   = $request->vendor_id;
            $vendorResume->pre_name    = $request->pre_name;
            $vendorResume->first_name  = $request->first_name;
            $vendorResume->second_name = $request->second_name;
            $vendorResume->email       = $request->email;
            $vendorResume->mobile      = $request->mobile;
            $vendorResume->position_id = $request->position_id;
            if (! empty($request->criteria)) {
                $vendorResume->criteria = implode(',', $request->criteria);
            }
            $vendorResume->career_objective                    = $request->career_objective;
            $vendorResume->salary_in_usd                       = serialize($request->salary_in_usd);
            $vendorResume->expected_salary_in_usd              = $request->expected_salary_in_usd;
            $vendorResume->start_time                          = $request->start_time;
            $vendorResume->end_time                            = $request->end_time;
            $vendorResume->time_zone                           = $request->time_zone;
            $vendorResume->preferred_working_days              = $request->preferred_working_days;
            $vendorResume->start_day                           = $request->start_day;
            $vendorResume->end_day                             = $request->end_day;
            $vendorResume->full_time                           = serialize($request->full_time);
            $vendorResume->part_time                           = serialize($request->part_time);
            $vendorResume->job_responsibilities                = serialize($request->job_responsibilities);
            $vendorResume->projects_worked                     = serialize($request->projects_worked);
            $vendorResume->tool_used                           = serialize($request->tool_used);
            $vendorResume->work_remark                         = serialize($request->work_remark);
            $vendorResume->soft_upload_document                = $vendorResumeName;
            $vendorResume->fulltime_freelancer                 = serialize($request->fulltime_freelancer);
            $vendorResume->current_assignments                 = serialize($request->current_assignments);
            $vendorResume->current_assignments_description     = serialize($request->current_assignments_description);
            $vendorResume->current_assignments_hours_utilisted = serialize($request->current_assignments_hours_utilisted);
            $vendorResume->work_experiance                     = serialize($request->work_experiance); //array
            $vendorResume->reason_for_leaving                  = serialize($request->reason_for_leaving); //array
            $vendorResume->date_from                           = serialize($request->date_from); //array
            $vendorResume->date_to                             = serialize($request->date_to); //array
            $vendorResume->designation                         = serialize($request->designation); //array
            $vendorResume->organization                        = serialize($request->organization); //array
            $vendorResume->project                             = serialize($project); //array
            $vendorResume->dev_role                            = serialize($dev_role); //array
            $vendorResume->tools                               = serialize($tools); //array
            $vendorResume->soft_framework                      = $request->soft_framework;
            $vendorResume->soft_proficiency                    = $request->soft_proficiency;
            $vendorResume->soft_description                    = $request->soft_description;
            $vendorResume->soft_experience                     = $request->soft_experience;
            $vendorResume->soft_remark                         = serialize($request->soft_remark);
            $vendorResume->edu_date_from                       = serialize($request->edu_date_from);
            $vendorResume->edu_date_to                         = serialize($request->edu_date_to);
            $vendorResume->edu_institute_programme             = serialize($request->edu_institute_programme);
            $vendorResume->edu_course_name                     = serialize($request->edu_course_name);
            $vendorResume->edu_grades                          = serialize($request->edu_grades);
            $vendorResume->edu_remark                          = serialize($request->edu_remark);
            $vendorResume->father_name                         = $request->father_name;
            $vendorResume->dob                                 = $request->dob;
            $vendorResume->gender                              = $request->gender;
            $vendorResume->marital_status                      = $request->marital_status;
            $vendorResume->langauge_know                       = $request->langauge_know;
            $vendorResume->hobbies                             = $request->hobbies;
            $vendorResume->city                                = $request->city;
            $vendorResume->state                               = $request->state;
            $vendorResume->country                             = $request->country;
            $vendorResume->pin_code                            = $request->pin_code;
            $vendorResume->address                             = serialize($request->address);
            $vendorResume->save();

            return redirect()->back()->with('success', 'Data stored successfully!!!');
        } catch (Exception $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VendorResume $vendorResume): JsonResponse
    {
        try {
            $vendorResume = new VendorResume();

            return response()->json(['code' => 200, 'data' => $vendorResume, 'message' => 'Data stored successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorResume $vendorResume)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorResume $vendorResume)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorResume $vendorResume)
    {
        //
    }

    public function storeCVWithoutLogin(Request $request): RedirectResponse
    {
        $requestData = $request->all();
        $project     = [];
        $dev_role    = [];
        $tools       = [];

        foreach ($requestData['work_experiance'] as $key => $val) {
            if (isset($requestData['project' . $key])) {
                foreach ($requestData['project' . $key] as $pKey => $pval) {
                    $project[$key][$pKey]  = $pval;
                    $dev_role[$key][$pKey] = $request->input('dev_role' . $key);
                    $tools[$key][$pKey]    = $request->input('tools' . $key);
                }
            }
        }

        $path = public_path('vendorResume');
        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $vendorResumeName = '';
        if ($request->file('soft_upload_document')) {
            $file             = $request->file('soft_upload_document');
            $vendorResumeName = uniqid() . '_' . trim($file->getClientOriginalName());
            $file->move($path, $vendorResumeName);
        }

        $vendorResume              = new VendorResume();
        $vendorResume->vendor_id   = $request->vendor_id;
        $vendorResume->pre_name    = $request->pre_name;
        $vendorResume->first_name  = $request->first_name;
        $vendorResume->second_name = $request->second_name;
        $vendorResume->email       = $request->email;
        $vendorResume->mobile      = $request->mobile;
        $vendorResume->position_id = $request->position_id;
        if (! empty($request->criteria)) {
            $vendorResume->criteria = implode(',', $request->criteria);
        }
        $vendorResume->career_objective                    = $request->career_objective;
        $vendorResume->salary_in_usd                       = serialize($request->salary_in_usd);
        $vendorResume->expected_salary_in_usd              = $request->expected_salary_in_usd;
        $vendorResume->start_time                          = $request->start_time;
        $vendorResume->end_time                            = $request->end_time;
        $vendorResume->time_zone                           = $request->time_zone;
        $vendorResume->preferred_working_days              = $request->preferred_working_days;
        $vendorResume->start_day                           = $request->start_day;
        $vendorResume->end_day                             = $request->end_day;
        $vendorResume->full_time                           = serialize($request->full_time);
        $vendorResume->part_time                           = serialize($request->part_time);
        $vendorResume->job_responsibilities                = serialize($request->job_responsibilities);
        $vendorResume->projects_worked                     = serialize($request->projects_worked);
        $vendorResume->tool_used                           = serialize($request->tool_used);
        $vendorResume->work_remark                         = serialize($request->work_remark);
        $vendorResume->soft_upload_document                = $vendorResumeName;
        $vendorResume->fulltime_freelancer                 = serialize($request->fulltime_freelancer);
        $vendorResume->current_assignments                 = serialize($request->current_assignments);
        $vendorResume->current_assignments_description     = serialize($request->current_assignments_description);
        $vendorResume->current_assignments_hours_utilisted = serialize($request->current_assignments_hours_utilisted);
        $vendorResume->work_experiance                     = serialize($request->work_experiance); //array
        $vendorResume->reason_for_leaving                  = serialize($request->reason_for_leaving); //array
        $vendorResume->date_from                           = serialize($request->date_from); //array
        $vendorResume->date_to                             = serialize($request->date_to); //array
        $vendorResume->designation                         = serialize($request->designation); //array
        $vendorResume->organization                        = serialize($request->organization); //array
        $vendorResume->project                             = serialize($project); //array
        $vendorResume->dev_role                            = serialize($dev_role); //array
        $vendorResume->tools                               = serialize($tools); //array
        $vendorResume->soft_framework                      = $request->soft_framework;
        $vendorResume->soft_proficiency                    = $request->soft_proficiency;
        $vendorResume->soft_description                    = $request->soft_description;
        $vendorResume->soft_experience                     = $request->soft_experience;
        $vendorResume->soft_remark                         = serialize($request->soft_remark);
        $vendorResume->edu_date_from                       = serialize($request->edu_date_from);
        $vendorResume->edu_date_to                         = serialize($request->edu_date_to);
        $vendorResume->edu_institute_programme             = serialize($request->edu_institute_programme);
        $vendorResume->edu_course_name                     = serialize($request->edu_course_name);
        $vendorResume->edu_grades                          = serialize($request->edu_grades);
        $vendorResume->edu_remark                          = serialize($request->edu_remark);
        $vendorResume->father_name                         = $request->father_name;
        $vendorResume->dob                                 = $request->dob;
        $vendorResume->gender                              = $request->gender;
        $vendorResume->marital_status                      = $request->marital_status;
        $vendorResume->langauge_know                       = $request->langauge_know;
        $vendorResume->hobbies                             = $request->hobbies;
        $vendorResume->city                                = $request->city;
        $vendorResume->state                               = $request->state;
        $vendorResume->country                             = $request->country;
        $vendorResume->pin_code                            = $request->pin_code;
        $vendorResume->address                             = serialize($request->address);
        $vendorResume->save();

        return redirect()->back()->with('success', 'Data stored successfully!!!');
    }
}
