<?php

namespace App\Http\Controllers;

use Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\EmailAddress;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\Message;
use Microsoft\Graph\Generated\Models\Recipient;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\ApiException;
use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MailList;
use Session;
use DB;
use View;
use Validator;

class MailController extends Controller
{
    /**
     * Get emails from Microsoft Graph API and store them in the database.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMail()
    {
        // Get the access token from the session
        $accessToken = Session::get('access_token');

        // Make a request to Microsoft Graph API to get the user's emails
        $response = Http::withToken($accessToken)
            ->get('https://graph.microsoft.com/v1.0/me/messages?$top=100&$orderby=receivedDateTime desc');

        if ($response->ok()) {
            // Handle successful response
            $emails = $response->json()['value'];
            
            if (!empty($emails)) {
                foreach ($emails as $email) {
                    // Check if the email already exists in the database
                    $checkExistance = DB::table('mail_listing')->where('mail_id', $email['id'])->first();

                    if (!$checkExistance) {
                        // If the email does not exist, create a new MailList entry and save it
                        $mailList = new MailList();

                        $mailList->mail_id = $email['id'];
                        $mailList->createdDateTime = $email['createdDateTime'];
                        $mailList->changeKey = $email['changeKey'];
                        $mailList->receivedDateTime = $email['receivedDateTime'];
                        $mailList->subject = $email['subject'];
                        $mailList->bodyPreview = $email['bodyPreview'];
                        $mailList->webLink = $email['webLink'];
                        $mailList->content = $email['body']['content'];
                        $mailList->sentDateTime = $email['sentDateTime'];

                        $mailList->save();
                    }
                }
            }

            // Paginate the retrieved emails and return them
            $emails = MailList::orderBy('receivedDateTime', 'desc')->paginate(2);
            return $emails;
        } else {
            // Handle error response
            $error = $response->json()['error']['code'];
            return $error;
        }
    }

    /**
     * Search emails in the database based on the provided key.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function searchMail(Request $request)
    {
        if(!isset($request->key) || $request->key == ''){   
            Session::forget('search');
        }
        $serchHistory = Session::get('search') ? Session::get('search') : '';
        $key = isset($request->key) ? $request->key : $serchHistory;
        // Retrieve emails from the database that match the search key
        $emails = MailList::where('subject', 'like', '%' . $key . '%')->orderBy('receivedDateTime', 'desc')->paginate(2);
        // Return the view with the search results
        if($request->ajax()){
            Session::put('search',$request->key);
            return response()->json(View::make('mail.pages.emailList', ['emails' => $emails])->render());
        }
        else{
            return view('mail.list', compact('emails'));
        }
    }

    /**
     * Display a list of emails.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function mails(Request $request)
    {
        // Get emails and return the view with the list
        $emails = $this->getMail();
        if(Session::has('access_token') && $emails === 'InvalidAuthenticationToken'){
            Session::flash('message', 'Token has been expired.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('/home');
        }
        return view('mail.list', compact('emails'));
    }

    /**
     * Send an email using Microsoft Graph API.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        // Set the Graph API URL for sending emails
        $graphUrl = 'https://graph.microsoft.com/v1.0/me/sendMail';

        try {
            // Make a POST request to send the email
            $response = Http::withToken(Session::get('access_token'))->post($graphUrl, [
                'message' => [
                    'subject' => $request->subject,
                    'body' => [
                        'contentType' => 'Text',
                        'content' => $request->description
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $request->email
                            ]
                        ]
                    ]
                ],
                'saveToSentItems' => 'true'
            ]);

            // Display success message and redirect to the emails list
            Session::flash('message', 'e-mail sent successfully!');
            Session::flash('alert-class', 'alert-success');
            return redirect('/mails');
        } catch (\Exception $e) {
            // Display error message and redirect to the emails list
            Session::flash('message', $e->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect('/mails');
        }
    }
}