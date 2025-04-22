<?php

namespace App\Http\Controllers\Hemis;

use App\Domain\Departments\Repositories\DepartmentRepository;
use App\Domain\Departments\Resources\DepartmentResource;
use App\Domain\Faculties\Resources\FacultyResource;
use App\Domain\Subjects\Resources\SubjectResource;
use App\Domain\Faculties\Repositories\FacultyRepository;
use App\Domain\Subjects\Repositories\SubjectRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\SessionState;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\GenericProvider;

class HemisController extends Controller
{
    /**
     * @var mixed|FacultyRepository
     */
    public mixed $faculties;

    /**
     * @var mixed|DepartmentRepository
     */
    public mixed $departments;

    /**
     * @var mixed|SubjectRepository
     */
    public mixed $subjects;

    /**
     * @param FacultyRepository $facultyRepository
     * @param DepartmentRepository $departmentRepository
     * @param SubjectRepository $subjectRepository
     */
    public function __construct(FacultyRepository $facultyRepository, DepartmentRepository $departmentRepository, SubjectRepository $subjectRepository)
    {
        $this->faculties = $facultyRepository;
        $this->departments = $departmentRepository;
        $this->subjects = $subjectRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getAllFaculties()
    {
        return $this->successResponse('',FacultyResource::collection($this->faculties->getAllFaculties()));
    }

    /**
     * @param $facultyId
     * @return JsonResponse
     */
    public function getAllDepartments($facultyId)
    {
        return $this->successResponse('',DepartmentResource::collection($this->departments->getAllDepartments($facultyId)));
    }

    /**
     * @return JsonResponse
     */
    public function getAllSubjects()
    {
        return $this->successResponse('',SubjectResource::collection($this->subjects->getAllSubjects()));
    }


//oAuth2
    protected function getProvider(): GenericProvider
    {
        return new GenericProvider([
            'clientId'                => config('hemis.hemis.client_id'),
            'clientSecret'            => config('hemis.hemis.client_secret'),
            'redirectUri'             => config('hemis.hemis.redirect'),
            'urlAuthorize'            => config('hemis.hemis.authorize_url'),
            'urlAccessToken'          => config('hemis.hemis.token_url'),
            'urlResourceOwnerDetails' => config('hemis.hemis.resource_url'),
        ]);
    }

    public function redirectToProvider()
    {
        $provider = $this->getProvider();

        $authUrl = $provider->getAuthorizationUrl();
        Session::put('oauth2state', $provider->getState());

        return redirect($authUrl);
    }

    public function handleCallback(Request $request)
    {
        $provider = $this->getProvider();

        if (!$request->has('code') || $request->get('state') !== Session::get('oauth2state')) {
            Session::forget('oauth2state');
            return redirect('/')->withErrors('Invalid OAuth state');
        }

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            $resourceOwner = $provider->getResourceOwner($accessToken);
            $user = $resourceOwner->toArray();

            Session::put('user',$user);
            Session::put('state',$request->get('state'));
            Session::put('code',$request->get('code'));

//            return response()->json([
//                'access_token' => $accessToken->getToken(),
//                'refresh_token' => $accessToken->getRefreshToken(),
//                'expires_at' => date('Y-m-d H:i:s', $accessToken->getExpires()),
//                'expired' => $accessToken->hasExpired(),
//                'user' => $user
//            ]);
            SessionState::create([
                'state' => $request->get('state'),
                'employee_id_number' => $user['employee_id_number']
            ]);
            return redirect()->away("https://buxdu.uz/videodars/auth/hemis?state=".$request->get('state'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkHemisAuth(Request $request)
    {
        $sessionState = SessionState::query()->latest()->first();

        $request->validate([
            'state' => 'required'
        ]);
        // Compare state and code from session
        if ($sessionState->state === $request->state) {
            $employee_id = $sessionState->employee_id_number;

            if ($employee_id) {
                // Find the user in DB (optional if full object already in session)
                $authUser = User::query()
                    ->where('employee_id_number', $employee_id)
                    ->first();

                if ($authUser) {
                    // Log in the user
                    Auth::login($authUser);

                    // Optional: regenerate session for security
                    Session::regenerate();

                    return response()->json([
                        'status' => true,
                        'message' => 'Login successful',
                        'user' => new UserResource($authUser),
                        'token' => $authUser->createToken('API Token')->plainTextToken, // if using Sanctum
                    ]);

                }
            }
        }

        return response()->json([
            'message' => 'Unauthorized or invalid session state/code',
        ], 401);
    }
}
