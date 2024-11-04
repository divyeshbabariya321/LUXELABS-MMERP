
          @foreach ($customers_all as $c)
            <tr>
            <td>{{ $c->id }}</td>  
            <td>{{ $c->name }}</td>
              <td>{{ $c->email }}</td>
              <td>{{ $c->phone }}</td>
              <td>{{ date("d-m-Y",strtotime($c->created_at)) }}</td>
              <td>{{ $c->whatsapp_number }}</td>
              <td>{{ $c->address }}</td>
              <td>{{ $c->city }}</td>
              <td>{{ $c->pincode }}</td>
              <td>{{ $c->country }}</td>
              <td>{{ $c->title }}</td>
              <td><a href="#" onClick="openInfo({{$c}})"><i class="fa fa-edit"></i></a>
              <a href="#" onClick="showMessagePopup({{$c->id}})"><i class="fa fa-eye"></i></a></td>
            </tr>
          @endforeach
        
