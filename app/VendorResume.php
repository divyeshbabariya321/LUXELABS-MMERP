<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorResume extends Model
{
    protected $fillable = ['vendors_id', 'pre_name',
        'first_name',
        'second_name',
        'email',
        'mobile',
        'career_objective',
        'salary_in_usd',
        'expected_salary_in_usd',
        'preferred_working_hours',
        'start_time',
        'end_time',
        'position_id',
        'criteria',
        'time_zone',
        'preferred_working_days',
        'start_day',
        'end_day',
        'full_time',
        'part_time',
        'job_responsibilities',
        'projects_worked',
        'tool_used',
        'work_remark',
        'fulltime_freelancer',
        'current_assignments',
        'current_assignments_description',
        'current_assignments_hours_utilisted',
        'work_experiance', //array
        'reason_for_leaving', //array
        'date_from', //array
        'date_to', //array
        'designation', //array
        'organization', //array
        'project',
        'dev_role',
        'tools',
        'soft_framework',
        'soft_proficiency',
        'soft_description',
        'soft_experience',
        'soft_remark',
        'edu_date_from',
        'edu_date_to',
        'edu_institute_programme',
        'edu_course_name',
        'edu_grades',
        'edu_remark',
        'father_name',
        'dob',
        'gender',
        'marital_status',
        'langauge_know',
        'hobbies',
        'city',
        'state',
        'country',
        'pin_code', ];
}
