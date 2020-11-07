<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Authentication\PasswordReset;
use App\Models\Authentication\PassworReset;
use App\Models\Authentication\Permission;
use App\Models\Authentication\System;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\State;
use App\Models\Authentication\User;
use App\Models\Authentication\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class  UserController extends Controller
{
    public function index()
    {
        $state = State::where('code', '1')->first();
        $users = $state->users()
            ->with('ethnicOrigin')
            ->with('location')
            ->with('identificationType')
            ->with('sex')
            ->with('gender')
            ->with('roles')
            ->paginate(10000);
        return response()->json($users, 200);

    }

    public function show($username, Request $request)
    {
        $system = System::where('code', $request->system_code)->first();
        $user = User::
        with('ethnicOrigin')
            ->with('institutions')
            ->with('location')
            ->with('identificationType')
            ->with('sex')
            ->with('gender')
            ->with('state')
            ->with('images')
            ->with(['roles' => function ($roles) use ($request, $system) {
                $roles
                    ->with('system')
                    ->with(['permissions' => function ($permissions) {
                        $permissions->with(['route' => function ($route) {
                            $route->with('module')->with('type')->with('images');
                        }]);
                    }])->where('system_id', $system->id);
            }])
            ->where('username', $username)
            ->first();
        return response()->json(['data' => $user], 200);

    }

    public function store(Request $request)
    {
        $data = $request->json()->all();
        $dataUser = $data['user'];
        $user = new User();

        $user->identification = strtoupper(trim($dataUser['identification']));
        $user->username = trim($dataUser['username']);
        $user->first_name = strtoupper(trim($dataUser['first_name']));
        $user->first_lastname = strtoupper(trim($dataUser['first_lastname']));
        $user->birthdate = trim($dataUser['birthdate']);
        $user->email = strtolower(trim($dataUser['email']));
        $user->password = Hash::make(trim($dataUser['password']));

        $ethnicOrigin = Catalogue::findOrFail($dataUser['ethnic_origin']['id']);
        $location = Catalogue::findOrFail($dataUser['location']['id']);
        $identificationType = Catalogue::findOrFail($dataUser['identification_type']['id']);
        $sex = Catalogue::findOrFail($dataUser['sex']['id']);
        $gender = Catalogue::findOrFail($dataUser['gender']['id']);
        $state = Catalogue::where('code', '1')->first();
        $user->ethnicOrigin()->associate($ethnicOrigin);
        $user->location()->associate($location);
        $user->identificationType()->associate($identificationType);
        $user->sex()->associate($sex);
        $user->gender()->associate($gender);
        $user->state()->associate($state);
        $user->save();
        return response()->json(['message' => 'Usuario creado', 'user' => $user], 201);
    }

    public function update(Request $request)
    {
        $data = $request->json()->all();
        $dataUser = $data['user'];
        $user = User::findOrFail($dataUser['id']);
        $user->identification = $dataUser['identification'];
        $user->username = strtoupper(trim($dataUser['username']));
        $user->first_name = strtoupper(trim($dataUser['first_name']));
        $user->first_lastname = strtoupper(trim($dataUser['first_lastname']));
        $user->birthdate = trim($dataUser['birthdate']);
        $user->email = strtolower(trim($dataUser['email']));

        $ethnicOrigin = Catalogue::findOrFail($dataUser['ethnic_origin']['id']);
        $location = Catalogue::findOrFail($dataUser['location']['id']);
        $identificationType = Catalogue::findOrFail($dataUser['identification_type']['id']);
        $sex = Catalogue::findOrFail($dataUser['sex']['id']);
        $gender = Catalogue::findOrFail($dataUser['gender']['id']);
        $state = Catalogue::where('code', '1')->first();
        $user->ethnicOrigin()->associate($ethnicOrigin);
        $user->location()->associate($location);
        $user->identificationType()->associate($identificationType);
        $user->sex()->associate($sex);
        $user->gender()->associate($gender);
        $user->state()->associate($state);
        $user->save();
        return response()->json(['message' => 'Usuario actualizado', 'user' => $user], 201);
    }

    public function destroy($id)
    {
        $state = Catalogue::where('code', '3')->first();
        $user = User::findOrFail($id);
        $user->state()->associate($state);
        $user->save();
        return response()->json(['message' => 'Usuario eliminado', 'user' => $user], 201);
    }

}
