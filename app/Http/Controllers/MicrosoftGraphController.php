<?php

namespace App\Http\Controllers;

use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Microsoft\Graph\GraphServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Session;

use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;

class MicrosoftGraphController extends Controller
{
    /**
     * Redirect the user to Microsoft login for authentication.
     *
     * @return mixed
     * @throws GraphException
     */
    public static function getCode()
    {
        // Redirect the user to the Microsoft login page with the necessary parameters
        return redirect('https://login.microsoftonline.com/consumers/oauth2/v2.0/authorize?client_id=b31edbdd-6825-4485-8ae1-bd5ec580b97e&response_type=code&redirect_uri=https%3A%2F%2Fleadcenter.localhost%2Fauth%2Fmicrosoft%2Foauth2-callback&response_mode=query&scope=offline_access%20User.Read%20Mail.Read%20Mail.Send&state=12345');
    }

    /**
     * Get the access token using the authorization code from Microsoft login.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function getToken()
    {
        // Parse the authorization code from the URL query parameters
        $parts = parse_url(url()->full());
        parse_str($parts['query'], $query);
        $code = $query['code'];

        // Use Guzzle to send a POST request to obtain the access token
        $guzzle = new \GuzzleHttp\Client();
        $url = 'https://login.microsoftonline.com/consumers/oauth2/v2.0/token';
        Session::forget('access_token');

        try {
            // Decode the JSON response and retrieve access token and refresh token
            $response = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => 'b31edbdd-6825-4485-8ae1-bd5ec580b97e',
                    'scope' => 'offline_access,user.read,mail.read,mail.send',
                    'code' => $code,
                    'redirect_uri' => 'https://leadcenter.localhost/auth/microsoft/oauth2-callback',
                    'grant_type' => 'authorization_code',
                    'client_secret' => 'RTh8Q~YoiPCVWsmaUSkBbe-rL4rTG28Q3InK2dnD'
                ],
            ])->getBody()->getContents());

            $access_token = $response->access_token;
            $refresh_token = $response->refresh_token;

            // Store the access token and refresh token in the session and redirect to emails page
            Session::put('access_token', $access_token);
            Session::put('refresh_token', $refresh_token);
            return redirect('/mails');
        } catch (\Exception $e) {
            // Handle any exceptions and redirect to the home page with an error message
            Session::flash('message', $e->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect('/home');
        }
    }

    /**
     * Remove the access token from the session and redirect to the home page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser()
    {
        // Remove the access token from the session and redirect to the home page
        Session::forget('access_token');
        return redirect('/home');
    }
}