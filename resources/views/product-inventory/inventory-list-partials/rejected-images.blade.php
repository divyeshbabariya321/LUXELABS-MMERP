<style type="text/css">
.rejected-images > div {
	border-top: 2px solid #ddd;    
}
.rejected-images > div + div {
	margin-top: 10px;
}
.rejected-images ul {
	grid-template-columns: 1fr 1fr 1fr;
    display: grid;
    padding: 0;
    grid-column-gap: 5px;
    grid-row-gap: 5px;
    margin-bottom: 0;
}
.rejected-images h5{
	margin-top: 0;
	background: #f9f9f9;
	font-weight: 600;
	padding: 10px 5px;
}
.rejected-images ul li > img {    
    width: 100%;
    height: auto;
    object-fit: cover;
}
.rejected-images ul li {    
    width: 100%;    
    align-items: center;
    display: flex;
    border: 1px solid;
    height: 100%;
}
</style>
<div class="rejected-images">
	@foreach($site_medias as $key => $media)
		<div>
			<h5>{{$key}}</h5>
			<ul>
			@foreach($media as $m)
				<li><img src="{{ asset('/'.$m->disk.'/'.$m->directory.'/'.$m->filename.'.'.$m->extension)}}"></li>
			@endforeach
			</ul>
		</div>
	@endforeach
</div>