
                       <div class="form-group">
                            <label >Title</label>
							{{ html()->text('title', $details['title'])->class('form-control')->placeholder('Enter title') }}
                        </div>
				      {{ html()->hidden('message_group_id', $details['message_group_id']) }}
				      {{ html()->hidden('id', $details['id']) }}
					<div class="form-group"><label >Scheduled Time</label>
						<input type="datetime-local" name="scheduled_at" value="{{ \Carbon\Carbon::parse($details['scheduled_at'])->format('Y-m-d\TH:i') }}" class="form-control scheduled_at" placeholder='Scheduled at'>
					</div>
						
                    <button type="submit" class="btn btn-primary">Submit </button>
                