<?php

namespace App\Http\Controllers\Hubstaff;

use App\Http\Controllers\Controller;
use Curl\Curl;
use Hubstaff\Authentication\Token;
use Hubstaff\Hubstaff;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HubstaffController extends Controller
{
    public $appToken;

    public $authToken;

    public $email;

    public $password;

    public function __construct(Request $request)
    {
        $this->appToken = getenv('HUBSTAFF_APP_KEY');
    }

    public function checkAuthTokenPresent()
    {
        return (bool) auth()->user()->auth_token_hubstaff;
    }

    public function getToken(Request $request): View
    {
        $token = new Token;

        $this->email = $request->email;

        $this->password = $request->password;

        if ($this->checkAuthTokenPresent()) {
            $authTokenDb = auth()->user()->auth_token_hubstaff;

            $hubstaff = Hubstaff::getInstance();

            $hubstaff->authenticate($this->appToken, $this->email, $this->password, $authTokenDb);

            $authToken = auth()->user()->auth_token_hubstaff;
        } else {
            $this->authToken = $token->getAuthToken($this->appToken, $this->email, $this->password);

            $hubstaff = Hubstaff::getInstance();

            $hubstaff->authenticate($this->appToken, $this->email, $this->password, $this->authToken);

            auth()->user()->update([
                'auth_token_hubstaff' => $this->authToken,
            ]);

            $authToken = $this->authToken;
        }

        $users = $hubstaff->getRepository('user')->getAllUsers();

        session()->flash('message', 'Authentication Successful');

        return view('hubstaff.show-auth-token', compact('users', 'authToken'));
    }

    public function authenticationPage(): View
    {
        return view('hubstaff.hubstaff-api-show');
    }

    public function gettingUsersPage(): View
    {
        return view('hubstaff.get-user');
    }

    public function userDetails(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v1/users';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);

        if ($this->checkAuthTokenPresent()) {
            $curl->setHeader('App-Token', auth()->user()->auth_token_hubstaff);
        } else {
            $curl->setHeader('App-Token', $this->appToken);
        }

        $curl->get($url, [
            'authorization_memberships' => $request->authorization_memberships,
            'project_memberships' => $request->project_memberships,
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.users', compact('results'));
    }

    public function showFormUserById(): View
    {
        return view('hubstaff.user-with-id-page');
    }

    public function getUserById(Request $request): View
    {
        $id = $request->id;

        $url = 'https://api.hubstaff.com/v1/users/'.$id;

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'id' => $request->id,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.user-with-id', compact('results'));
    }

    public function getProjectPage(): View
    {
        return view('hubstaff.get-projects');
    }

    public function getProjects(Request $request): View
    {
        $id = $request->id;

        $url = 'https://api.hubstaff.com/v1/users/'.$id.'/projects';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'id' => $request->id,
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.user-projects', compact('results'));
    }

    // -------projects---------

    public function getUserProject(): View
    {
        return view('hubstaff.project.get-project-page');
    }

    public function postUserProject(Request $request): View
    {

        $url = 'https://api.hubstaff.com/v1/projects';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'status' => $request->status,
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.project.user-projects', compact('results'));
    }

    // ---------Tasks----------

    public function getProjectTask(): View
    {
        return view('hubstaff.task.get-task-page');
    }

    public function postProjectTask(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v1/tasks';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'projects' => $request->projects,
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.task.tasks', compact('results'));
    }

    public function getTaskFromId(): View
    {
        return view('hubstaff.task.get-task-from-id');
    }

    public function postTaskFromId(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v1/tasks/'.$request->id;

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'projects' => $request->projects,
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.task.specific-task-page', compact('results'));
    }

    public function getScreenshotPage(): View
    {
        return view('hubstaff.screenshot.screenshot-page');
    }

    public function postScreenshots(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v1/screenshots/';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'start_time' => $request->start_time,
            'stop_time' => $request->stop_time,
            'organizations' => $request->organizations,
            'projects' => $request->projects,
            'users' => $request->users,
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.screenshot.show-screenshots', compact('results'));
    }

    public function index(): View
    {
        return view('hubstaff.organization.index');
    }

    public function getOrganization(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v1/organizations/';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.organization.show-organizations', compact('results'));
    }

    public function organizationMemberPage(): View
    {
        return view('hubstaff.organization.organization-member-page');
    }

    public function showMembers(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v1/organizations/'.$request->id.'/members';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.organization.show-members', compact('results'));
    }

    public function getTeamPaymentPage(): View
    {
        return view('hubstaff.team.payment-page');
    }

    public function getPaymentDetail(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v1/team_payments/';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'start_time' => $request->start_time,
            'stop_time' => $request->stop_time,
            'organizations' => $request->organizations,
            'projects' => $request->projects,
            'users' => $request->users,
            'offset' => $request->offset,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }
        $curl->close();

        $results = $response;

        return view('hubstaff.team.show-payments-detail', compact('results'));
    }
}
