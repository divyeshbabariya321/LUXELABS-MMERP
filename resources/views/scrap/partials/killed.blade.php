
    <div class="col-md-12">
        <table class="table table-bordered table-striped sort-priority-scrapper">
            <thead>
                <tr>
                    <th width="2%">#</th>
                    <th width="25%">Scraper Id</th>
                    <th width="35%">Scraper Name</th>
                    <th width="15%">Comment</th>
                    <th width="15%">Created at</th>
                </tr>
            </thead>
            <tbody class="conent">
                @foreach ($histories as $_history)
                    <tr>
                        <td>{{ $_history->id }}</td>
                        <td>{{ $_history->scraper_id }}</td>
                        <td>{{ $_history->scraper_name }}</td>
                        <td>{{ $_history->comment }}</td>
                        <td>{{ $_history->created_at }}</td>
                    </tr>
                @endforeach
           </tbody>
        </table>
    </div>
    <script>


    </script>
