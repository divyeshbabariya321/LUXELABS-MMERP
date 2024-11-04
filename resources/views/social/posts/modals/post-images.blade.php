<style>
    .post-images { display: flex; align-items: center; justify-content: center; gap: 15px; padding: 30px; }
    .post-images .img-wrapper { width: 33.33%; float: left; }
    .post-images .img-wrapper img { max-width: 100%; height: 200px; object-fit: cover; object-position: center; }

</style>

<div class="post-images">
    @forelse ( $images as $image )
        <div class="img-wrapper">
            <img src="{{ $image }}" >
        </div>
    @empty
        <p>No Images</p>
    @endforelse
</div>