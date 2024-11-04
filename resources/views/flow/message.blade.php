	<div class="form-group {{ $errors->has('sender_name') ? 'has-error' : '' }}">
        {{ html()->label('Sender Name:') }}
        {{ html()->text('sender_name', $flowMessage['sender_name'])->class('form-control')->placeholder('Enter Sender Name')->required() }}
    </div>

    <div class="form-group {{ $errors->has('sender_email_address') ? 'has-error' : '' }}">
        {{ html()->label('Sender Email Address:') }}
        {{ html()->text('sender_email_address', $flowMessage['sender_email_address'])->class('form-control')->placeholder('Enter Sender Email Address')->required() }}
    </div>

    <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
        {{ html()->label('Subject:') }}
        {{ html()->text('subject', $flowMessage['subject'])->class('form-control')->placeholder('Enter Subject')->required() }}
    </div>

	<div class="form-group">
        <label for="mail_tpl">Email Template</label>
        {{ html()->select("mail_tpl", ['' => "-- None --"] + $rViewMail, $flowMessage['mail_tpl'])->class("form-control select2")->required()->id("form_mail_tpl") }}
        <span class="text-danger"></span>
    </div>

    <div class="form-group {{ $errors->has('html_content') ? 'has-error' : '' }}">
        {{ html()->label('Content:') }}
        {{ html()->textarea('html_content', $flowMessage['html_content'])->class('form-control')->placeholder('Enter Content')->id('html_content')->required() }}
    </div>
    <div class="form-group">
        {{ html()->hidden('action_id', $flowMessage['action_id'])->id('flow_message_action_id') }}
        {{ html()->hidden('id', $flowMessage['id']) }}
		<button type="submit" class="btn btn-secondary">Create</button>
    </div>
