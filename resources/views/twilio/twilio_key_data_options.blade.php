@extends('layouts.app')
@section('favicon' , 'productstats.png')
@section('title', 'Twilio Message Tones')
@section('content')
<div class=>
    <div class="mt-3 col-md-12">
        <form action="{{ route('twilio.get_website_wise_key_data_options') }}" method="get" class="search">
            <div class="form-group col-md-2">
                <h5>search website</h5>
                {{ html()->multiselect("website_ids[]", \App\StoreWebsite::pluck('website', 'id')->toArray(), request('website_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Website") }}
            </div>
            <div class="form-group col-md-2"><br><br>
                <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{ route('twilio.get_website_wise_key_data_options') }}" class="btn btn-image" id="">
                    <img src="/images/resend2.png" style="cursor: nwse-resize;">
                </a>
            </div>
        </form>
    </div>
<table class="table table-bordered table-hover" style="table-layout: fixed;">
    <tr>
        <th width="8%">Website</th>
        <th class="text-center"width="10%">1</th>
        <th class="text-center"width="10%">2</th>
        <th class="text-center"width="10%">3</th>
        <th class="text-center"width="10%">4</th>
        <th class="text-center"width="10%">5</th>
        <th class="text-center"width="10%">6</th>
        <th class="text-center"width="10%">7</th>
        <th class="text-center"width="10%">8</th>
        <th class="text-center"width="10%">9</th>

    </tr>
    <?php $web_site_id = isset($web_site_id) ? $web_site_id : ''; ?>
    @foreach ($store_websites as $store_web_data)
        @if($web_site_id)
            <tr>
                <td colspan="10">
                    <span class="" id="welcome_message_div">
                        <strong style="float: left;"> Greeting Message : </strong> &nbsp;
                        <input type="text" name="welcome_message" id="welcome_message" value="{{ $store_web_data->twilio_greeting_message ?? '' }}" class="form-control" style="float: left;width: 50% !important; margin-left:10px;" />&nbsp;&nbsp;
                        <a href="#"  style="float: left; margin-left:10px;" class="btn btn-secondary save_twilio_greeting_message" data-website-id="{{$web_site_id}}">Save</a>
                    </span>
                </td>
            </tr>
        @endif
        <tr>
            <td class="Website-task">{{$store_web_data->website}}</td>
              <td>
                    <select class="form-control mb-2 option_menu_1{{$store_web_data->id}}" aria-label="Default select example">
                    <option value="">Select</option>
                    <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][1]['option']) && $twilio_key_arr[$store_web_data->id][1]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                    <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][1]['option']) && $twilio_key_arr[$store_web_data->id][1]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                    <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][1]['option']) && $twilio_key_arr[$store_web_data->id][1]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                    <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][1]['option']) && $twilio_key_arr[$store_web_data->id][1]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                    <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][1]['option']) && $twilio_key_arr[$store_web_data->id][1]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                    <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][1]['option']) && $twilio_key_arr[$store_web_data->id][1]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control mb-2 key_description_1{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][1]['desc']) ? $twilio_key_arr[$store_web_data->id][1]['desc'] : ''}}</textarea>
                    <textarea class="form-control mb-2 key_message_1{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][1]['message']) ? $twilio_key_arr[$store_web_data->id][1]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_1{{$store_web_data->id}}" class="id_1{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][1]['id']) ? $twilio_key_arr[$store_web_data->id][1]['id'] : 0}}" />
                    <a href="#" class=" save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="1"><i class="fa fa-floppy-o " aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td>
                    <select class="form-control mb-2 option_menu_2{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][2]['option']) && $twilio_key_arr[$store_web_data->id][2]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][2]['option']) && $twilio_key_arr[$store_web_data->id][2]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][2]['option']) && $twilio_key_arr[$store_web_data->id][2]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                    <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][2]['option']) && $twilio_key_arr[$store_web_data->id][2]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                    <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][2]['option']) && $twilio_key_arr[$store_web_data->id][2]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                    <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][2]['option']) && $twilio_key_arr[$store_web_data->id][2]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control mb-2 key_description_2{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][2]['desc']) ? $twilio_key_arr[$store_web_data->id][2]['desc'] : ''}}</textarea>
                    <textarea class="form-control mb-2 key_message_2{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][2]['message']) ? $twilio_key_arr[$store_web_data->id][2]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_2{{$store_web_data->id}}" class="id_2{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][2]['id']) ? $twilio_key_arr[$store_web_data->id][2]['id'] : 0}}" />
                    <a href="#" class=" save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="2"><i class="fa fa-floppy-o " aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td>
                    <select class="form-control mb-2 option_menu_3{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][3]['option']) && $twilio_key_arr[$store_web_data->id][3]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][3]['option']) && $twilio_key_arr[$store_web_data->id][3]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][3]['option']) && $twilio_key_arr[$store_web_data->id][3]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                        <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][3]['option']) && $twilio_key_arr[$store_web_data->id][3]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                        <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][3]['option']) && $twilio_key_arr[$store_web_data->id][3]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                        <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][3]['option']) && $twilio_key_arr[$store_web_data->id][3]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control  mb-2 key_description_3{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][3]['desc']) ? $twilio_key_arr[$store_web_data->id][3]['desc'] : ''}}</textarea>
                    <textarea class="form-control mb-2 key_message_3{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][3]['message']) ? $twilio_key_arr[$store_web_data->id][3]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_3{{$store_web_data->id}}" class="id_3{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][3]['id']) ? $twilio_key_arr[$store_web_data->id][3]['id'] : 0}}" />
                    <a href="#" class=" save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="3"><i class="fa fa-floppy-o " aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td> 
                    <select class="form-control mb-2 option_menu_4{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][4]['option']) && $twilio_key_arr[$store_web_data->id][4]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][4]['option']) && $twilio_key_arr[$store_web_data->id][4]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][4]['option']) && $twilio_key_arr[$store_web_data->id][4]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                        <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][4]['option']) && $twilio_key_arr[$store_web_data->id][4]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                        <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][4]['option']) && $twilio_key_arr[$store_web_data->id][4]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                        <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][4]['option']) && $twilio_key_arr[$store_web_data->id][4]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control mb-2 key_description_4{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][4]['desc']) ? $twilio_key_arr[$store_web_data->id][4]['desc'] : ''}}</textarea>
                    <textarea class="form-control mb-2  key_message_4{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][4]['message']) ? $twilio_key_arr[$store_web_data->id][4]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_4{{$store_web_data->id}}" class="id_4{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][4]['id']) ? $twilio_key_arr[$store_web_data->id][4]['id'] : 0}}" />
                    <a href="#" class=" save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="4" ><i class="fa fa-floppy-o " aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td>
                    <select class="form-control mb-2 option_menu_5{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][5]['option']) && $twilio_key_arr[$store_web_data->id][5]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][5]['option']) && $twilio_key_arr[$store_web_data->id][5]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][5]['option']) && $twilio_key_arr[$store_web_data->id][5]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                        <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][5]['option']) && $twilio_key_arr[$store_web_data->id][5]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                        <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][5]['option']) && $twilio_key_arr[$store_web_data->id][5]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                        <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][5]['option']) && $twilio_key_arr[$store_web_data->id][5]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control mb-2 key_description_5{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][5]['desc']) ? $twilio_key_arr[$store_web_data->id][5]['desc'] : ''}}</textarea>
                    <textarea class="form-control  mb-2 key_message_5{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][5]['message']) ? $twilio_key_arr[$store_web_data->id][5]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_5{{$store_web_data->id}}" class="id_5{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][5]['id']) ? $twilio_key_arr[$store_web_data->id][5]['id'] : 0}}" />
                    <a href="#" class=" save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="5"><i class="fa fa-floppy-o " aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td>
                    <select class="form-control mb-2 option_menu_6{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][6]['option']) && $twilio_key_arr[$store_web_data->id][6]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][6]['option']) && $twilio_key_arr[$store_web_data->id][6]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][6]['option']) && $twilio_key_arr[$store_web_data->id][6]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                        <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][6]['option']) && $twilio_key_arr[$store_web_data->id][6]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                        <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][6]['option']) && $twilio_key_arr[$store_web_data->id][6]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                        <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][6]['option']) && $twilio_key_arr[$store_web_data->id][6]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control  mb-2 key_description_7{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][6]['desc']) ? $twilio_key_arr[$store_web_data->id][6]['desc'] : ''}}</textarea>
                    <textarea class="form-control mb-2 key_message_6{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][6]['message']) ? $twilio_key_arr[$store_web_data->id][6]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_6{{$store_web_data->id}}" class="id_6{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][6]['id']) ? $twilio_key_arr[$store_web_data->id][6]['id'] : 0}}" />
                    <a href="#" class="save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="6"><i class="fa fa-floppy-o" aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td>
                    <select class="form-control mb-2 option_menu_7{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][7]['option']) && $twilio_key_arr[$store_web_data->id][7]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][7]['option']) && $twilio_key_arr[$store_web_data->id][7]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][7]['option']) && $twilio_key_arr[$store_web_data->id][7]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                        <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][7]['option']) && $twilio_key_arr[$store_web_data->id][7]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                        <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][7]['option']) && $twilio_key_arr[$store_web_data->id][7]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                        <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][7]['option']) && $twilio_key_arr[$store_web_data->id][7]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control  mb-2 key_description_7{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][7]['desc']) ? $twilio_key_arr[$store_web_data->id][7]['desc'] : ''}}</textarea>
                    <textarea class="form-control  mb-2 key_message_7{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][7]['message']) ? $twilio_key_arr[$store_web_data->id][7]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_7{{$store_web_data->id}}" class="id_7{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][7]['id']) ? $twilio_key_arr[$store_web_data->id][7]['id'] : 0}}" />
                    <a href="#" class="save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="7"><i class="fa fa-floppy-o " aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td> 
                    <select class="form-control mb-2 option_menu_8{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][8]['option']) && $twilio_key_arr[$store_web_data->id][8]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][8]['option']) && $twilio_key_arr[$store_web_data->id][8]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][8]['option']) && $twilio_key_arr[$store_web_data->id][8]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                        <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][8]['option']) && $twilio_key_arr[$store_web_data->id][8]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                        <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][8]['option']) && $twilio_key_arr[$store_web_data->id][8]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                        <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][8]['option']) && $twilio_key_arr[$store_web_data->id][8]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control  mb-2 key_description_8{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][8]['desc']) ? $twilio_key_arr[$store_web_data->id][8]['desc'] : ''}}</textarea>
                    <textarea class="form-control   mb-2 key_message_8{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][8]['message']) ? $twilio_key_arr[$store_web_data->id][8]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_8{{$store_web_data->id}}" class="id_8{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][8]['id']) ? $twilio_key_arr[$store_web_data->id][8]['id'] : 0}}" />
                    <a href="#" class=" save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="8"><i class="fa fa-floppy-o" aria-hidden="true" style="color:gray;"></i></a>
            </td>
            <td>
                    <select class="form-control mb-2 option_menu_9{{$store_web_data->id}}" aria-label="Default select example">
                        <option value="">Select</option>
                        <option value="order" {{ isset($twilio_key_arr[$store_web_data->id][9]['option']) && $twilio_key_arr[$store_web_data->id][9]['option'] == 'order' ? 'selected' : ''}}>Order Status and information</option>
                        <option value="product" {{ isset($twilio_key_arr[$store_web_data->id][9]['option']) && $twilio_key_arr[$store_web_data->id][9]['option'] == 'product' ? 'selected' : ''}}>Product and Shipping Information</option>
                        <option value="administration" {{ isset($twilio_key_arr[$store_web_data->id][9]['option']) && $twilio_key_arr[$store_web_data->id][9]['option'] == 'administration' ? 'selected' : ''}}>Administration</option>
                        <option value="socialmedia" {{ isset($twilio_key_arr[$store_web_data->id][9]['option']) && $twilio_key_arr[$store_web_data->id][9]['option'] == 'socialmedia' ? 'selected' : ''}}>Social Media and Collaborations</option>
                        <option value="return_refund_exchange" {{ isset($twilio_key_arr[$store_web_data->id][9]['option']) && $twilio_key_arr[$store_web_data->id][9]['option'] == 'return_refund_exchange' ? 'selected' : ''}}>Returns , Refunds , Exchanges</option>
                        <option value="general" {{ isset($twilio_key_arr[$store_web_data->id][9]['option']) && $twilio_key_arr[$store_web_data->id][9]['option'] == 'general' ? 'selected' : ''}}>General</option>
                    </select>
                    <textarea class="form-control  mb-2 key_description_9{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][9]['desc']) ? $twilio_key_arr[$store_web_data->id][9]['desc'] : ''}}</textarea>
                    <textarea class="form-control mb-2 key_message_9{{$store_web_data->id}}" rows="1">{{ isset($twilio_key_arr[$store_web_data->id][9]['message']) ? $twilio_key_arr[$store_web_data->id][9]['message'] : ''}}</textarea>
                    <input type="hidden" name="id_9{{$store_web_data->id}}" class="id_9{{$store_web_data->id}}" value="{{ isset($twilio_key_arr[$store_web_data->id][9]['id']) ? $twilio_key_arr[$store_web_data->id][9]['id'] : 0}}" />
                    <a href="#" class=" save_key_option" data-website-id="{{$store_web_data->id}}"  data-id="9"><i class="fa fa-floppy-o " aria-hidden="true" style="color:gray;"></i></a>
            </td>
        </tr>
    @endforeach
</table>
</div>
@endsection

@section('scripts')

<script type="text/javascript">
 $('.save_key_option').on("click", function(e){
    var key_no = $(this).data("id");
    var website_id = $(this).data("website-id");//$('.store_website_twilio_key').val();
    var option = $('.option_menu_'+key_no+website_id).val();
    var desc = $('.key_description_'+key_no+website_id).val();
    var message = $('.key_message_'+key_no+website_id).val();
    
    var id = $('.id_'+key_no+website_id).val();

    if(option == '')
    {
        toastr['error']('Please select Option');
        return false;
    }
    if(desc == '')
    {
        toastr['error']('Please Enter Description');
        return false;
    }

    $.ajax({
        type: "POST",
        url: "{{ route('twilio.set_twilio_key_options') }}",  
        data: {
            _token: "{{csrf_token()}}",
            key_no:key_no,
            option:option,
            description:desc,
            message:message,
            website_store_id:website_id,
            up_id:id,
        },
        beforeSend : function() {
            
        },
        success: function (response) {
            if(response.status == 1){
                toastr['success'](response.message);
            }else if(response.status == 0){
                toastr['error'](response.message);
            }
        },
        error: function (response) { 
            
        }
    }); 
});

$('.save_twilio_greeting_message').on("click", function(e){
    var message = $('#welcome_message').val();
    var website_id = $(this).data("website-id");
   
    if(message == '')
    {
        toastr['error']('Please enter message');
        return false;
    }

    $.ajax({
        type: "POST",
        url: "{{ route('twilio.set_twilio_greeting_message') }}",  
        data: {
            _token: "{{csrf_token()}}",
            welcome_message:message,
            website_store_id:website_id
        },
        success: function (response) {
            if(response.status == 1){
                toastr['success'](response.message);
            }else if(response.status == 0){
                toastr['error'](response.message);
            }
        },
        error: function (response) { 
            
        }
    }); 
});


</script>
@endsection