<!doctype html>
<html lang="{{ config('app.lang') }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>

    <style>
        @if (!app()->environment('testing')){!! file_get_contents(public_path('/dist/export-styles.css')) !!} @endif.page-break {
            page-break-after: always;
        }

        .chapter-hint {
            color: #888;
            margin-top: 32px;
        }

        .chapter-hint+h1 {
            margin-top: 0;
        }

        ul.contents ul li {
            list-style: circle;
        }

        @media screen {
            .page-break {
                border-top: 1px solid #DDD;
            }
        }

    </style>
    @yield('head')
    @include('bookstack::partials.custom-head')
</head>

<body>


    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Category</th>
                <th>website</th>
                <th>status</th>
                <th>Description</th>
                <th>created At</th>
                <th>is_site_asset</th>
                <th>is_site_list</th>
            </tr>
        </thead>
        <tbody>
            @if ($site_developments->count())

                @foreach ($site_developments as $site_development)
                    <tr>
                        <th>{{ isset($site_development->category) ? $site_development->category->title : '-' }}</th>
                        <th>{{ isset($site_development->store_website) ? $site_development->store_website->title : '-' }}
                        <th>{{ isset($site_development->site_development_status) ? $site_development->site_development_status->name : '-' }}
                        </th>
                        <th>{{ $site_development->description }}</th>
                        <th>{{ $site_development->created_at }}</th>
                        <th>{{ $site_development->is_site_asset }}</th>
                        <th>{{ $site_development->is_site_list }}</th>

                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>


</body>

</html>
