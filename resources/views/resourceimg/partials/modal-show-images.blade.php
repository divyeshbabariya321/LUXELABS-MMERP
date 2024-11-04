<div class="modal fade" id="showresource" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-image modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="font-size: 24px;">Resources Center Images</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                @if (Auth::user()->hasRole('Admin'))
                    {{ html()->form('POST', route('delete.resource'))->open() }}
                    <input type="hidden" name="id" value="{{ $allresources['id'] }}">
                    <button type="submit" name="button_type" value="delete" class="pull-right btn btn-image"><img
                            src="/images/delete.png" /></button>
                    {{ html()->form()->close() }}
                @endif
                <div class="row justify-content-center">
                    @isset($allresources['image1'])
                        <div class="col-md-8">
                            <img onclick="OpenModel(this.id)" id="myImg1" class="myImg"
                                src="{{ URL::to('/category_images/' . $allresources['image1']) }}"
                                alt="{{ URL::to('/category_images/' . $allresources['image1']) }}"
                                style="width: 100% !important;height: 250px !important;">
                        </div>
                    @endisset
                    @isset($allresources['image2'])
                        <div class="col-md-8">
                            <img onclick="OpenModel(this.id)" id="myImg2" class="myImg"
                                src="{{ URL::to('/category_images/' . $allresources['image2']) }}"
                                alt="{{ URL::to('/category_images/' . $allresources['image2']) }}"
                                style="width: 100% !important;height: 250px !important;">
                        </div>
                    @endisset
                    @isset($allresources['images'])
                        @if ($allresources['images'] != null)
                            @foreach (json_decode($allresources['images']) as $image)
                                <div class="col-md-8" style="margin-top: 15px">
                                    <img onclick="OpenModel(this.id)" id="myImg2" class="myImg"
                                        src="{{ URL::to('/category_images/' . $image) }}"
                                        alt="{{ URL::to('/category_images/' . $image) }}"
                                        style="width: 100% !important;height: 250px !important;">
                                </div>
                            @endforeach
                        @endif
                    @endisset
                </div>

            </div>
            <div class="modal-footer">
                <canvas style="border:none;display: none;" id="my_canvas"></canvas>
                <input type="hidden" autocomplete="off" class="form-control" name="image2" id="cpy_img">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                        class="fa fa-times"></i></button>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal imageModal" onclick="CloseModel()">
    <span onclick="CloseModel()" class="close">&times;</span>
    <img class="modal-content my-modal-content" id="img01">
    <div id="caption"></div>
</div>
<style type="text/css">
    .modal:nth-of-type(even) {
        z-index: 1062 !important;
    }

    .modal-backdrop.show:nth-of-type(even) {
        z-index: 1061 !important;
    }

    .modal-dialog-image {
        width: 750px;
        margin: auto;
    }


    .myImg {
        border-radius: 5px;
        cursor: pointer;
        transition: .3s
    }

    .myImg:hover {
        opacity: .7
    }

    .imageModal {
        display: none;
        position: fixed;
        z-index: 1;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: #000;
        background-color: rgba(0, 0, 0, .9)
    }

    .my-modal-content {
        margin: auto;
        display: block;
        width: 100%;
        max-width: 720px
    }

    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px
    }

    #caption,
    .my-modal-content {
        -webkit-animation-name: zoom;
        -webkit-animation-duration: .6s;
        animation-name: zoom;
        animation-duration: .6s
    }

    @-webkit-keyframes zoom {
        from {
            -webkit-transform: scale(0)
        }

        to {
            -webkit-transform: scale(1)
        }
    }

    @keyframes zoom {
        from {
            transform: scale(0)
        }

        to {
            transform: scale(1)
        }
    }

    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: 700;
        transition: .3s
    }

    .close:focus,
    .close:hover {
        color: #bbb;
        text-decoration: none;
        cursor: pointer
    }

    @media only screen and (max-width:700px) {
        .my-modal-content {
            width: 100%
        }
    }

    .del_btn {
        position: absolute !important;
        background: rgba(255, 255, 255, .5) !important;
        bottom: 0 !important;
        border-radius: 0 !important;
        right: 15px !important;
        padding: 10px 12px 8px 12px
    }

    .myh4 {
        text-align: center;
        background: rgba(0, 0, 0, .4);
        padding: 10px 0;
        margin: 0;
        text-transform: uppercase;
        color: #f5f5f5;
        font-weight: 600
    }
</style>
<script type="text/javascript">
    var modal = document.getElementById("myModal"),
        img = document.getElementsByClassName("myImg"),
        modalImg = document.getElementById("img01"),
        captionText = document.getElementById("caption");

    function OpenModel(e) {
        console.log(e), modal.style.display = "block", modalImg.src = $("#" + e).attr("src"),
            captionText.innerHTML = "Source :: <a target='_blank' href='" + $("#" + e).attr("alt") + "'>" + $("#" + e)
            .attr("alt") + "</a>"
    }
    var span = document.getElementsByClassName("close")[0];

    function CloseModel() {
        modal.style.display = "none"
    }
    document.onkeydown = function(e) {
        ("key" in (e = e || window.event) ? "Escape" === e.key || "Esc" === e.key : 27 === e.keyCode) && (modal
            .style.display = "none")
    };
</script>
