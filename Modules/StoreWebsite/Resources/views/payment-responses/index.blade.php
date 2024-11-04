@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', $title)

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }

        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .change-is_price_ovveride {
            cursor: pointer;
        }
    </style>

    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ $title }} <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col">
                    <form class="form-inline message-search-handler" method="get">
                        <div class="col-lg-4">
                            <label for="store_website_id">Store Websites:</label>
                            <select class="form-control" name="store_website_id">
                                <option value="">Select Website</option>
                                @foreach ($websites as $web)
                                    <option value="{{ $web->id }}">{{ $web->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="card_type">Card Type:</label>
                            <select class="form-control" name="card_type">
                                <option value="">Select Card Type</option>
                                <option value="visa">VISA</option>
                                <option value="master_card">MasterCard</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="date">Date:</label>
                            <input class="form-control" type="date" name="date">
                        </div>
                        <div class="col-lg-2">
                            <label for="amount">Amount:</label>
                            <input class="form-control" type="number" name="amount" placeholder="Enter Amount">
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="button">&nbsp;</label>
                                <button type="submit" style="display: inline-block;width: 10%"
                                    class="btn btn-sm btn-image btn-search-action">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success" id="alert-msg" style="display: none;">
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="col-md-12 margin-tb" id="page-view-result">
            </div>
        </div>
    </div>
    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
    </div>
    @include('storewebsite::payment-responses.templates.list-template')
    <script type="text/javascript" src="/js/jsrender.min.js"></script>
    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/payment-responses.js') }}"></script>
    <script type="text/javascript">
        page.init({
            bodyView: $("#common-page-layout"),
            baseUrl: "<?php echo url('/'); ?>"
        });
    </script>
@endsection
