<?php

namespace App\Http\Controllers\Hemis;

use App\Domain\Departments\Repositories\DepartmentRepository;
use App\Domain\Departments\Resources\DepartmentResource;
use App\Domain\Faculties\Resources\FacultyResource;
use App\Domain\Subjects\Resources\SubjectResource;
use App\Domain\Faculties\Repositories\FacultyRepository;
use App\Domain\Subjects\Repositories\SubjectRepository;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        Log::warning('request', $request->all());
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

            return response()->json([
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires_at' => date('Y-m-d H:i:s', $accessToken->getExpires()),
                'expired' => $accessToken->hasExpired(),
                'user' => $user
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
