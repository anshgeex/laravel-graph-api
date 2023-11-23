<!-- Card for sending emails -->
<div class="card">
    <div class="card-body">
        <!-- Title for the card -->
        <h4>Send Mail</h4>

        <!-- Form for sending emails -->
        <div class="row form-horizontal">
            <form id="emailForm" action="{{ url('/submitMail') }}" method="post">
                @csrf
                <div class="col-12">
                    <!-- Input field for the recipient email address -->
                    <div class="form-group">
                        <input type="email" value="{{ old('email') }}" name="email" class="form-control" placeholder="To">
                    </div>

                    <!-- Input field for the email subject -->
                    <div class="form-group">
                        <input type="text" value="{{ old('subject') }}" name="subject" class="form-control" placeholder="Subject" >
                    </div>

                    <!-- Textarea for the email description -->
                    <div class="form-group">
                        <textarea name="description" id="" cols="30" class="form-control" rows="10"
                            placeholder="Enter description here..." required>{{ old('description') }}</textarea>
                    </div>

                    <!-- Submit button to send the email -->
                    <div class="form-group">
                        <input type="submit" class="btn btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
