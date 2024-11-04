<div id="createVendorCvModal" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="/vendors/cv/store" id="vandor-cv-form" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Create CV</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body row">
          <input type="hidden" class="hidden-vendor-id" name="vendor_id">
          <div class="form-group col-3">
            <select class="form-control validate" name="pre_name" style="width: 37% !important;float: left;">
              <option>Mr.</option>
              <option>Miss.</option>
              <option>Mrs.</option>
            </select>
            <input type="text" id="first_name" name="first_name" class="form-control validate" placeholder="First name" style="width: 63%;float: left;" required>
          </div>
          <div class="form-group col-3">
            <input type="text" id="second_name" name="second_name" class="form-control validate" placeholder="Second name" required>
          </div>

          <div class="form-group  col-3">
            <input type="email" id="email" name="email" class="form-control validate" placeholder="Your email" required>
          </div>

          <div class="form-group col-3">
            <input type="text" id="mobile" name="mobile" class="form-control validate" placeholder="Contact No. Ex. 987654321" required>
          </div>
          <div class="form-group col-12">
            <textarea type="text" id="career_objective" name="career_objective" class="md-textarea form-control" rows="3" placeholder="Career Objective"></textarea>
          </div>
          <div class="form-group  col-12">
            <label>Preferred working hours</label>
            <!--<input type="text" id="email" name="preferred_working_hours" class="form-control validate" placeholder="Preferred working hours">-->
          </div>
          <div class="form-group col-3">
            <input type="time" id="start_time" name="start_time" class="form-control validate" placeholder="Start Time">
          </div>
          <div class="form-group col-3">
            <input type="time" id="end_time" name="end_time" class="form-control validate" placeholder="End Time">
          </div>
          <div class="form-group col-3">
            <?php
                // Create a timezone identifiers
                $timezone_identifiers =
                  DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                echo '<select id="mySelect2" name="time_zone" class="form-control validate">';

                echo "<option disabled selected>
                    Please Select Timezone
                  </option>";

                $n = 425;
                for($i = 0; $i < $n; $i++) {
                  
                  // Print the timezone identifiers
                  echo "<option value='" . $timezone_identifiers[$i] .
                    "'>" . $timezone_identifiers[$i] . "</option>";
                }

                echo "</select>";

                ?>
            <!--<input type="text" id="time_zone" name="time_zone" class="form-control validate" placeholder="Time Zone">-->
          </div>
          
          <div class="form-group  col-12">
            <label>Preferred working Days</label>
            <!--<input type="text" id="email" name="preferred_working_days" class="form-control validate" placeholder="Preferred working hours">-->
          </div>
          <div class="form-group col-3">
            <input type="text" id="start_day" name="start_day" class="form-control validate" placeholder="Start day Ex. Monday">
          </div>
          <div class="form-group col-3">
            <input type="text" id="end_day" name="end_day" class="form-control validate" placeholder="End day Ex. Friday, Saturday">
          </div>
          <div class="form-group col-3">
            <input type="text" id="expected_salary_in_usd" name="expected_salary_in_usd" class="form-control validate" placeholder="Expected Salary IN USD">
          </div>

          <div class="form-group col-12">
            <label>Full Time Freelancer</label>
            <input type="checkbox" id="fulltime_freelancer" name="fulltime_freelancer[]" value="Yes" class="" style="height: unset;"> Yes
            <input type="checkbox" id="fulltime_freelancer" name="fulltime_freelancer[]" value="No" class="" style="height: unset;"> No
          </div>
          <div class="row col-12 current_assigment_div">
              <div class="form-group col-4">
                <input type="text" id="current_assignments" name="current_assignments[]" class="form-control validate" placeholder="Current Assignments">
              </div>
                <div class="form-group col-4">
                <input type="text" id="current_assignments_description" name="current_assignments_description[]" class="form-control validate" placeholder="Description">
              </div>
              <div class="form-group col-4">
                <input type="text" id="current_assignments_hours_utilisted" name="current_assignments_hours_utilisted[]" class="form-control validate" placeholder="Hours Utilisted">
              </div>
          </div>
          <div class="form-group col-12">
            <button type="button" style="cursor:pointer" class="btn btn-image add-current-assignments" title="Add Current Assignments"><i class="fa fa-plus" aria-hidden="true"></i>Add Current Assignments</button>
          </div>
          

          <div class="col-12 work_experiance_model">
            <label>Work Experience</label>( Please add details of your work experience with the latest records first )
            <hr>
            <div class="row">
              <div class="form-group col-3">
                <input type="text" id="salary_in_usd" name="salary_in_usd" class="form-control validate" placeholder="Salary IN USD">
              </div>
              <div class="form-group col-3">
                <input type="hidden" name="work_experiance[]" class="form-control validate work_experiance" placeholder="Work Experiance Ex. 1 Year or 2 year">
                <input type="month" id="date_from" name="date_from[]" class="form-control validate date_from " placeholder="From" value="{{ date('Y-m')}}">
              </div>
              <div class="form-group col-3">
                <input type="month" id="date_to" name="date_to[]" class="form-control validate date_to " placeholder="To"  value="{{ date('Y-m')}}">
              </div>
              <div class="form-group col-3">
                <input type="text"  name="organization[]" class="form-control validate organization" placeholder="Organization">
              </div>
              <div class="form-group col-3">
                <input type="text"  name="designation[]" class="form-control validate designation" placeholder="Designation">
              </div>
              <div class="form-group col-3">
                 <input type="text" name="reason_for_leaving[]" class="form-control validate reason_for_leaving" placeholder="Reason for Leaving">
               </div>
               <div class="form-group col-3">
                 <input type="checkbox" id="part_time" name="part_time[]" value="Yes" class="form-input-check validate" style="height: unset"> <b>Part Time</b>
                 <input type="checkbox" id="full_time" name="full_time[]" value="Yes" class="form-input-check validate" style="height: unset"> <b>Full Time</b>
               </div>
               <div class="form-group col-12">
                 <input type="text" id="job_responsibilities" name="job_responsibilities[]" class="form-control validate" placeholder="Job Responsibilities">
               </div>
              <div class="form-group col-12">
                <input type="text" id="projects_worked" name="projects_worked[]" class="form-control validate" placeholder="Projects Worked">
              </div>
              <div class="form-group col-12">
                <input type="text" id="tool_used" name="tool_used[]" class="form-control validate" placeholder="Tool Used">
              </div>
              <div class="form-group col-12">
                <textarea  id="work_remark" name="work_remark[]" class="form-control validate" placeholder="Remark" rows="2"></textarea>
              </div>
            </div>
            <div class="project_detail">
              <div  class="row">
                <div class="form-group col-2">
                  <input type="text" name="project0[]" class="form-control validate project" placeholder="Project">
                </div>
                <div class="form-group col-2">
                  <input type="text"  name="dev_role0[]" class="form-control validate dev_role" placeholder="Role">
                </div>
                <div class="form-group col-2">
                  <input type="text"name="tools0[]" class="form-control validate tools" placeholder="Tools">
                </div>
              </div>
              <button type="button" style="cursor:pointer" class="btn btn-image add-project" title="Add Project" data-project_counter="0"><i class="fa fa-plus" aria-hidden="true"></i>Add Project</button>
            </div>
          </div>
          <div class="col-12">
            <input type="hidden" id="work_expariance" value="0" /> 
            <button type="button" style="cursor:pointer" class="btn btn-image add-work-expariance" title="Add Work Expirenace" data-work_expariance="0"><i class="fa fa-plus" aria-hidden="true"></i>Add Work Expirenace</button>
          </div>          
            
            <div class="form-group col-12">
              <hr>
              <label data-error="wrong" data-success="right" for="name">Professional Expertise</label>
            </div>
            <div class="form-group col-9">
              <input type="text" id="soft_expertise" name="soft_expertise" class="form-control validate" placeholder="Please add your professional expertise such as Software Frameworks , Seo , Digiital Marketing Tools , Photoshop , Figma Etc"> 
            </div>
            <div class="form-group col-3">
              <select id="soft_experience" name="soft_experience" class="form-control validate" aria-label="Default select example" style="width: 70% !important;height: 30px;float: left;">
                <option value="">Experience</option>
                <option value="0">0</option>
                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                <option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option>
                <option value="9">9</option><option value="10">10</option>
                <option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option>
                <option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option>
                <option value="19">19</option><option value="20">20</option>
            </select>
              <label style="margin: 5px;">Years</label>
            </div>
            <div class="form-group col-6">
              <textarea id="soft_description" name="soft_description" class="form-control" placeholder="Description"></textarea>
            </div>
            <div class="form-group col-6">
              <textarea id="soft_certifications" name="soft_certifications" class="form-control" placeholder="Certifications"></textarea>
            </div>
            
            <div class="form-group col-4">
              <input type="text" id="soft_framework" name="soft_framework" class="form-control validate" placeholder="Frameworks" >
            </div>
            <div class="form-group col-4">
              <select id="soft_proficiency" name="soft_proficiency" class="form-control validate" aria-label="Default select example">
                  <option value="">Proficiency</option>
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
              </select>
            </div>
            <div class="form-group col-4">
                <input type="file" name="soft_upload_document" style="width: 100%;">
            </div>
            <div class="form-group col-12 remark_div" >
              <textarea id="soft_remark" name="soft_remark[]" class="form-control validate" placeholder="Remark"></textarea>
            </div>
            <div class="col-12">
              <input type="hidden" id="soft_remark_counter" value="0" /> 
              <button type="button" style="cursor:pointer" class="btn btn-image add-remark" title="Add Remark"><i class="fa fa-plus" aria-hidden="true"></i>Add Remark</button>
            </div>
            
          
          <div class="col-12 educational_model">
            <label>Educational Qualifications</label>
            <hr>
            <div class="row">
              <div class="form-group col-2">
                  <input type="month"  name="edu_date_from[]" class="form-control validate edu_date_from " placeholder="Start From" value="{{date('Y-m')}}">
              </div>
              <div class="form-group col-2">
                <input type="month"  name="edu_date_to[]" class="form-control validate edu_date_to " placeholder="End Date" value="{{date('Y-m')}}">
              </div>
              <div class="form-group col-3">
                <input type="text" name="edu_institute_programme[]" class="form-control validate reason_for_leaving" placeholder="Institute Programme">
              </div>
              <div class="form-group col-3">
                <input type="text" id="edu_course_name" name="edu_course_name[]" class="form-control validate" placeholder="Course Name">
              </div>
              <div class="form-group col-2">
                <input type="text" id="edu_grades" name="edu_grades[]" class="form-control validate" placeholder="Grades">
              </div>
              <div class="form-group col-12">
                 <textarea  id="edu_remark" name="edu_remark[]" class="form-control validate" placeholder="Remark" rows="2"></textarea>
               </div>
            </div>
          </div>
          <div class="col-12">
            <input type="hidden" id="edu_counter" value="0" /> 
            <button type="button" style="cursor:pointer" class="btn btn-image add-edu" title="Add Educational Qualifications" data-work_expariance="0"><i class="fa fa-plus" aria-hidden="true"></i>Add Educational Qualifications</button>
          </div>
          <div  class="form-group col-12">
            <hr>
            <label data-error="wrong" data-success="right" for="name">Personal details</label>
          </div>
          <div  class="form-group row mx-auto">
              <div class="form-group col-3">
                <input type="text" id="father_name" name="father_name" class="form-control validate" placeholder="Fathers Name">
              </div>
    
              <div class="form-group  col-3">
                <input type="text" id="dob" name="dob" class="form-control validate" placeholder="Date of Birth">
              </div>
    
              <div class="form-group col-3">
                <select class="form-control validate" name="gender">
                  <option value="" >Gender</option>
                  <option value="Male" >Male</option>
                  <option value="Female">Female</option>
                </select>
              </table>
                
              </div>
    
              <div class="form-group col-3">
                <select class="form-control validate" name="marital_status">
                  <option value="">Marital Status</option>
                  <option value="Married" >Married</option>
                  <option value="Unmarried">Unmarried</option>
                  <option value="Divorced">Divorced</option>
                </select>
              </div>
              <div class="form-group col-3">
                <input type="text" id="langauge_know" name="langauge_know" class="form-control" placeholder="Languages Known Ex. English etc"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="hobbies" name="hobbies" class="form-control" placeholder="Hobbies Ex. Reading, Singging, Learning"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="city" name="city" class="form-control" placeholder="City "/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="state" name="state" class="form-control" placeholder="State"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="country" name="country" class="form-control" placeholder="Country"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="pin_code" name="pin_code" class="form-control" placeholder="Pin Code"/>
              </div>
              
              <div class="form-group col-12 address_div">
                <textarea type="text" name="address[]" class="md-textarea form-control" rows="3" placeholder="Address"></textarea>
              </div>
              <div class="col-12">
                <input type="hidden" id="address_counter" value="0" /> 
                <button type="button" style="cursor:pointer" class="btn btn-image add-address" title="Add Address"><i class="fa fa-plus" aria-hidden="true"></i>Add Address</button>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary sub_form">Submit</button>
        </div>
      </form>
    </div>

  </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<script>
  $('#mySelect2').select2({
      
  });
  var datemonth = $(".datepicker_monthYear").datepicker( {
      format: "mm-yyyy",
      startView: "months", 
      minViewMode: "months"
  });
  $(document).on('click', '.add-work-expariance', function (event) {
    var work_expariance = $('#work_expariance').val();//$(this).data('project_counter');
    var work_expariance = parseInt(work_expariance) + 1;
    $('#work_expariance').val(work_expariance)
    var project_counter = work_expariance;

    var addWork = ' <hr><div class="row"><div class="form-group col-3"><input type="text" id="salary_in_usd" name="salary_in_usd[]" class="form-control validate" placeholder="Salary IN USD"></div><div class="form-group col-3"><input type="hidden" name="work_experiance[]" class="form-control validate work_experiance" placeholder="Work Experiance Ex. 1 Year or 2 year"><input type="month" id="date_from" name="date_from[]" class="form-control validate date_from " placeholder="From"></div><div class="form-group col-3"><input type="month" id="date_to" name="date_to[]" class="form-control validate date_to " placeholder="To"></div><div class="form-group col-3"><input type="text" name="organization[]" class="form-control validate organization" placeholder="Organization"></div><div class="form-group col-3"><input type="text" name="designation[]" class="form-control validate designation" placeholder="Designation"></div><div class="form-group col-3"><input type="text" name="reason_for_leaving[]" class="form-control validate reason_for_leaving" placeholder="Reason for Leaving"></div><div class="form-group col-3"><input type="checkbox" id="part_time" name="part_time[]" value="Yes" class="form-input-check validate" style="height: unset"><b>Part Time</b><input type="checkbox" id="full_time" name="full_time[]" value="Yes" class="form-input-check validate" style="height: unset"><b>Full Time</b></div><div class="form-group col-12"><input type="text" id="job_responsibilities" name="job_responsibilities[]" class="form-control validate" placeholder="Job Responsibilities"></div><div class="form-group col-12"><input type="text" id="projects_worked" name="projects_worked[]" class="form-control validate" placeholder="Projects Worked"></div><div class="form-group col-12"><input type="text" id="tool_used" name="tool_used[]" class="form-control validate" placeholder="Tool Used"></div><div class="form-group col-12"><textarea id="work_remark" name="work_remark[]" class="form-control validate" placeholder="Remark" rows="2"></textarea></div></div><div class="project_detail"><div class="row"><div class="form-group col-2"><input type="text" name="project'+project_counter+'[]" class="form-control validate project" placeholder="Project"></div><div class="form-group col-2"><input type="text" name="dev_role'+project_counter+'[]" class="form-control validate dev_role" placeholder="Role"></div><div class="form-group col-2"><input type="text"name="tools'+project_counter+'[]" class="form-control validate tools" placeholder="Tools"></div></div><button type="button" style="cursor:pointer" class="btn btn-image add-project" title="Add Project" data-project_counter="'+project_counter+'"><i class="fa fa-plus" aria-hidden="true"></i>Add Project</button></div>';
    $(".work_experiance_model").append(addWork);
  });
  $(document).on('click', '.add-edu', function (event) {
    var edu_counter = $('#edu_counter').val();//$(this).data('project_counter');
    var edu_counter = parseInt(edu_counter) + 1;
    $('#edu_counter').val(edu_counter)
    var addEdu = ' <hr><div class="row"><div class="form-group col-2"><input type="hidden" name="work_experiance[]" class="form-control validate work_experiance" placeholder="Work Experiance Ex. 1 Year or 2 year"><input type="month" name="edu_date_from[]" class="form-control validate edu_date_from " placeholder="Start From"></div><div class="form-group col-2"><input type="month" name="edu_date_to[]" class="form-control validate edu_date_to " placeholder="End Date"></div><div class="form-group col-3"><input type="text" name="edu_institute_programme[]" class="form-control validate reason_for_leaving" placeholder="Institute Programme"></div><div class="form-group col-3"><input type="text" id="edu_course_name" name="edu_course_name[]" class="form-control validate" placeholder="Course Name"></div><div class="form-group col-2"><input type="text" id="edu_grades" name="edu_grades[]" class="form-control validate" placeholder="Grades"></div><div class="form-group col-12"><textarea id="edu_remark" name="edu_remark[]" class="form-control validate" placeholder="Remark" rows="2"></textarea></div></div>';
    $(".educational_model").append(addEdu);
  });
  
  $(document).on('click', '.add-project', function () {
    var project_counter = $(this).data('project_counter');
    var addProject = '<div class="form-group col-2"><input type="text" name="project'+project_counter+'[]" class="form-control validate project" placeholder="Project"></div><div class="form-group col-2"><input type="text" name="dev_role'+project_counter+'[]" class="form-control validate dev_role" placeholder="Role"></div><div class="form-group col-2"><input type="text"name="tools'+project_counter+'[]" class="form-control validate tools" placeholder="Tools"></div>';
    $(this).parent().find(".row").append(addProject)
  });
  $(document).on('click', '.add-current-assignments', function () {
    //debugger;<textarea id="soft_remark" name="soft_remark[]" class="form-control validate" placeholder="Remark"></textarea>
    var current_assignments = '<div class="form-group col-4"><input type="text" id="current_assignments" name="current_assignments[]" class="form-control validate" placeholder="Current Assignments"></div><div class="form-group col-4"><input type="text" id="current_assignments_description" name="current_assignments_description[]" class="form-control validate" placeholder="Description"></div><div class="form-group col-4"><input type="text" id="current_assignments_hours_utilisted" name="current_assignments_hours_utilisted[]" class="form-control validate" placeholder="Hours Utilisted"></div>';
    //$(this).parent().find("#current_assigment_div").append(current_assignments)
    $(".current_assigment_div").append(current_assignments)
  });
  
  $(document).on('click', '.add-address', function () {
    var address_counter = $('#address_counter').val();//$(this).data('project_counter');
    var address_counter = parseInt(address_counter) + 1;
    $('#address_counter').val(address_counter)
    var address = '<br/><textarea type="text" name="address[]" class="md-textarea form-control" rows="3" placeholder="Address '+address_counter+'"></textarea>';
    $(".address_div").append(address)
  });
  
  $(document).on('click', '.add-remark', function () {
    var soft_remark_counter = $('#soft_remark_counter').val();//$(this).data('project_counter');
    var soft_remark_counter = parseInt(soft_remark_counter) + 1;
    $('#soft_remark_counter').val(soft_remark_counter)
    var soft_remark = '<br/><textarea name="soft_remark[]" class="form-control validate" placeholder="Remark '+soft_remark_counter+'"></textarea>';
    $(".remark_div").append(soft_remark)
  });
  $(document).on('focus',".datepicker", function(){ //bind to all instances of class "date". 
    $(".datepicker").datepicker( {
        format: "dd-mm-yyyy",
        startView: "months", 
        minViewMode: "months"
    });
  });
  $("#dob").datepicker( {
        format: "dd-MM-yyyy",
        /*startView: "months", 
        minViewMode: "months"*/
    });

</script>