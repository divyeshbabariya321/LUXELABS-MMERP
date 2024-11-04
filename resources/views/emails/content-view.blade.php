<p></p></p></p><p></p></p></p>
@if($emailAddresses->signature_title)
<p><b>Title:</b> {!! $emailAddresses->signature_title !!}</p>
@endif
@if($emailAddresses->signature_name)
<p><b>Name:</b> {!! $emailAddresses->signature_name !!}</p>
@endif
@if($emailAddresses->signature_email)
<p><b>Signature Email:</b> {!! $emailAddresses->signature_email !!}</p>
@endif
@if($emailAddresses->signature_phone)
<p><b>Signature Phone:</b> {!! $emailAddresses->signature_phone !!}</p>
@endif
@if($emailAddresses->signature_website)
<p><b>Signature Websiter:</b> {!! $emailAddresses->signature_website !!}</p>
@endif
@if($emailAddresses->signature_address)
<b>Signature Address:</b> {!! $emailAddresses->signature_address !!}
@endif
@if($emailAddresses->signature_social)
<b>Signature Social:</b> {!! $emailAddresses->signature_social !!}
@endif
@if($emailAddresses->signature_logo)
<p><img src="uploads/{!! $emailAddresses->signature_logo !!}"></p>
@endif
@if($emailAddresses->signature_image)
<p><img src="uploads/{!! $emailAddresses->signature_image !!}"></p>
@endif