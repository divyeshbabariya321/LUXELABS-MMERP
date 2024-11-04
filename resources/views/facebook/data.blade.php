@foreach ($posts as $post)
            <tr>
            <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m') }}</td>
              <td>{{ $post->account->first_name }}</td>
              <td>{{ $post->caption }}</td>
              <td>{{ $post->post_body }}</td>
              <td>
              @if ($post->hasMedia($mediaTags))
              <a data-fancybox="gallery" href="{{ getMediaUrl($post->getMedia($mediaTags)->first()) }}"><img width="100" src="{{ getMediaUrl($post->getMedia($mediaTags)->first()) }}"></a>

              @endif
              
              </td>
              <td>{{ \Carbon\Carbon::parse($post->posted_on)->format('d-m-y h:m') }}</td>
              <td>{{ $post->status ? 'Posted' : '' }}</td>
              <td></td>
              </tr>
          @endforeach
          {{$posts->appends(request()->except("page"))->links()}}