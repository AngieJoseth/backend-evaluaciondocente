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

class  AuthController extends Controller
{

    public function index(Request $request)
    {
        $state = State::where('code', '1')->first();
        $users = $state->users()
            ->with('ethnicOrigin')
            ->with('location')
            ->with('identificationType')
            ->with('sex')
            ->with('gender')
            ->get();
        return response()->json([
            'data' => [
                'users' => $users
            ]
        ], 200);

    }

    public function getPermissions(Request $request)
    {
        $role = Role::where('code', $request->role)->first();
        $permissions = $role->permissions()
            ->with(['routes' => function ($query) {
                $query->with('children');
            }])
            ->get();
        return $permissions;
    }

    public function getUser(Request $request)
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
            ->where('username', $request->username)
            ->first();
        return response()->json([
            'data' => [
                'user' => $user
            ]
        ], 200);

    }

    public function login(Request $request)
    {
        $request->validate([
            'system' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $system = Catalogue::findOrFail($request->system);

        $user = User::where('username', $request->username)->with('state')->first();

        // Se valida que el usuario exista
        if (!$user) {
            return response()->json([
                'message' => [
                    'title' => 'not found',
                    'detail' => 'user not found'
                ]
            ], 404);
        }

        // Se valida que el usuario y contraseña son correctos
        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json([
                'message' => [
                    'title' => 'unauthorized',
                    'detail' => 'user unauthorized'
                ]
            ], 401);
        }

        // Se valida que el rol y sistema al cual quiere ingresar, esten asiganados correctamente

        if ($system) {
            $roles = $user->roles()->get();
            $permissions = null;
            $flagRole = false;

            foreach ($roles as $role) {
                if ($role['system_id'] == $system['id']) {
                    $permissions = $role->permissions()->with('route')->get();
                    $flagRole = true;
                }
            }

            if (!$flagRole) {
                return response()->json([
                    'message' => [
                        'title' => 'forbidden',
                        'detail' => 'system forbidden'
                    ]
                ], 403);
            }
        } else {
            return response()->json([
                'message' => [
                    'title' => 'not found',
                    'detail' => 'system not found'
                ]
            ], 404);
        }

        $accessToken = Auth::user()->createToken('authToken');

        if ($request->remember_me) {
            $accessToken->token->expires_at = Carbon::now()->addMonth(1);
        }
        $allPermissions = Permission::all();
        return response()->json([
            'auth' => [
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions,
                'token' => $accessToken],
            'permissions' => $allPermissions,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function logoutAll(Request $request)
    {
        DB::table('oauth_access_tokens')
            ->where('user_id', $request->user_id)
            ->update([
                'revoked' => true
            ]);
        return response()->json(['message' => 'Successfully logged out']);
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

    public function changePassword(Request $request)
    {
        $data = $request->json()->all();
        $dataUser = $data['user'];
        $user = User::findOrFail($dataUser['id']);
        $user->update([
            'password' => Hash::make(trim($dataUser['password'])),
        ]);
        return response()->json(['message' => 'Usuario actualizado', 'user' => $user], 201);
    }

    public function uploadAvatarUri(Request $request)
    {
        if ($request->file('file_avatar')) {
            $user = User::findOrFail($request->user_id);
            Storage::delete($user->avatar);
            $pathFile = $request->file('file_avatar')->storeAs('private/avatar',
                $user->id . '.png');
//            $path = storage_path() . '/app/' . $pathFile;
            $user->update(['avatar' => $pathFile]);
            return response()->json(['message' => 'Archivo subido correctamente'], 201);
        } else {
            return response()->json(['errores' => 'Archivo no válido'], 500);
        }

    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->orWhere('personal_email', $request->username)
            ->first();
        if (!$user) {
            return response()->json([
                'message' => [
                    'title' => 'not found',
                    'detail' => 'user not found'
                ]
            ], 404);
        }
        $token = Str::random(70);
        try {
            PasswordReset::create([
                'username' => $user->username,
                'token' => $token
            ]);

            Mail::send('Mails.forgot', ['token' => $token, 'user' => $user], function (Message $message) use ($user) {
                $message->to($user->email);
                $message->subject('Notificación de restablecimiento de contraseña');
            });
            $domainEmail = strlen($user->email) - strpos($user->email, "@");
            return response()->json([
                'data' => $this->hiddenString($user->email, 3, $domainEmail)], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6',
            'password_confirm' => 'required|same:password',
        ]);
        $passworReset = PasswordReset::where('token', $request->token)->first();
        if (!$passworReset) {
            return response()->json([
                'message' => [
                    'title' => 'token not found',
                    'detail' => 'invalid token'
                ]], 400);

        }
        if (!$passworReset->is_valid) {
            return response()->json([
                'message' => [
                    'title' => 'invalid token',
                    'detail' => 'used token'
                ]], 403);
        }
        if ((new Carbon($passworReset->created_at))->addHour(1) <= Carbon::now()) {
            $passworReset->update(['is_valid' => false]);
            return response()->json([
                'message' => [
                    'title' => 'invalid token',
                    'detail' => 'expired token'
                ]], 403);
        }

        if (!$user = User::where('username', $passworReset->username)->first()) {
            return response()->json([
                'message' => [
                    'title' => 'not found',
                    'detail' => 'user not found'
                ]], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $passworReset->update(['is_valid' => false]);
        return response()->json(['data' => true], 201);
    }

    private function hiddenString($str, $start = 1, $end = 1)
    {
        $len = strlen($str);
        return substr($str, 0, $start) . str_repeat('*', $len - ($start + $end)) . substr($str, $len - $end, $end);
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}
