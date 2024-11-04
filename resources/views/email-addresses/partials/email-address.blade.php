@forelse($emailAddress as $storeWebsite)
    <tr>
        <td>
            {{$storeWebsite->id}}
        </td>
        <td width="45%">
            <div style="display: flex">
                <input type="text" class="form-control" name="username[{{$storeWebsite->id}}]" value="{{$storeWebsite->username}}">
                <button type="button" class="btn btn-copy-username btn-sm" data-id="{{$storeWebsite->username}}">
                    <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
            </div>
        </td>
        <td width="30%">
            <div style="display: flex">
                <input type="text" class="form-control" name="password[{{$storeWebsite->id}}]" value="{{$storeWebsite->password}}">
                <button type="button" class="btn btn-copy-password btn-sm" data-id="{{$storeWebsite->password}}">
                    <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" style="text-align: center"><h4>No Data Found </h4></td>
    </tr>
@endforelse