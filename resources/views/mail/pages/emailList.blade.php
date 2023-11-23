<!-- Display a table with email data -->
<table class="table table-responsive table-striped table-bordered">
    <!-- Table headers -->
    <thead>
        <tr>
            <th>S.No.</th>
            <th>Subject</th>
            <th>Body Preview</th>
        </tr>
    </thead>

    <!-- Check if there are emails to display -->
    @if(count($emails))
        <!-- Loop through each email and display its details -->
        <tbody>
            @foreach ($emails as $key => $res)
                <tr>
                    <!-- Incremental serial number -->
                    <td>{{ ++$key }}</td>
                    
                    <!-- Display email subject -->
                    <td>{{ $res->subject }}</td>
                    
                    <!-- Display a preview of the email body -->
                    <td>{{ $res->bodyPreview }}</td>
                </tr>
            @endforeach
        </tbody>
    @else
        <!-- Display a message if no emails are found -->
        <tr>
            <td colspan="3" class="text-center">No Data Found.</td>
        </tr>
    @endif
</table>
<div class="pagination-wrap">
    {!! $emails->render() !!}
</div>

