@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="emailcontent">
            {{-- Display a flash message if it exists --}}
            @if(Session::has('message'))
                <div class="alert {{Session::get('alert-class')}}">
                    {{Session::get('message')}}
                </div>
            @endif

            {{-- Include the email form partial --}}
            <div id="email-form">
                @include('mail.pages.emailForm')
            </div>

            <hr>

            {{-- Email list section --}}
            <div class="card">
                <div class="card-body">
                    {{-- Search and disconnect account buttons --}}
                    <div class="mailSearch">
                        <div class="form-group pull-left">
                            <a href="/deleteUser" class="btn btn-primary" style="float: left">Disconnect Account</a>
                            <a href="/mails" class="btn btn-primary" style="float: right; margin-left: 10px;">Refresh</a>
                        </div>
                        <div class="form-group pull-right">
                            <input type="text" value="{{Session::get('search')}}" class="search form-control" placeholder="What are you looking for?">
                        </div>
                    </div>

                    {{-- Display search result counter --}}
                    <span class="counter pull-right"></span>

                    {{-- Email list content --}}
                    <div id="listContent">
                        @include('mail.pages.emailList')
                    </div>
                </div>
            </div>
        </div>
    <div>

    {{-- Include jQuery library --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    {{-- Ajax search functionality --}}
    <script>
        $(document).on('keyup','.search',function(){
            // Send AJAX request to searchMail route
            $.ajax({
                url : '/searchMail',
                method : 'post',
                data : {"_token": "{{ csrf_token() }}", 'key' : $(this).val()},
                success : function(result){
                    // Update the content of the email list with the search result
                    $('#listContent').html(result);
                }
            })
        })
    </script>
@endsection
