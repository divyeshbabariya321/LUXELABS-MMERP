@foreach ($settings as $setting)
            <tr>
              <td>{{ $setting->id }}</td>
              <td>{{ $setting->project_id ?? "-" }}</td>
              <td>
                {{ $setting->email }}
              </td>
              <td>
                {!! ($setting->account_json )?"<span class='lesstext'>".(\Illuminate\Support\Str::limit($setting->account_json , 10, '<a href="javascript:void(0)" class="readmore btn btn-xs text-dark">...<i class="fa fa-plus" aria-hidden="true"></i></a>'))."</span>":"-" !!}
                {!! ($setting->account_json )?"<span class='alltext' style='display:none;'>".$setting->account_json ."<a href='javascript:void(0)' class='readless btn btn-xs text-dark'>...<i class='fa fa-minus' aria-hidden='true'></i></a></span>":"-" !!}
                <?php if(!empty($setting->account_json)){ ?>
                  <button class="btn btn-xs text-dark" onclick="copyDataToClipBoard('<?php echo $setting->account_json; ?>')"><i class="fa fa-copy"></i></button>
                <?php } ?>
              </td>
              <td>
                @if ($setting->status == 1)
                    Enable
                @else
                    Disabled
                @endif
              </td>
              <td>
                {{ $setting->last_note }}
              </td>
              <td>
                <a href="{{ route('google-traslation-settings.edit',$setting->id) }}" class="btn btn-xs text-dark pull-left"><i class="fa fa-edit"></i></a>
                <form action="{{ route('google-traslation-settings.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="setting" value="{{ $setting->id }}">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="btn btn-xs text-dark pull-left"><i class="fa fa-trash"></i></button>
                </form>
                <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $setting->id}}"><i class="fa fa-eye"></i></button>
                
              </td>
            </tr>
          @endforeach