
<h3>Hello {{$admin['name']}},</h3> 
<h4>Your account had been disconnected, please connect your account with this <a href="{{ route('googlewebmaster.account.connect', $acc->google_client_account_id)}}">link</a></h4>