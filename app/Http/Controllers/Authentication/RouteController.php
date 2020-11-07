<?php

namespace App\Http\Controllers\Authentication;

use App\Models\Authentication\Module;
use App\Models\Authentication\Route;
use App\Models\Authentication\System;
use App\models\Ignug\Catalogue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RouteController extends Controller
{
    public function index()
    {
        $modules = Catalogue::with('routes')->where('parent_id', 1)->get();
        return response()->json(['data' => $modules], 200);
    }

    public function getModules()
    {
        $modules = Catalogue::with(['routes' => function ($route) {
            $route->where('type_id', 1);
        }])->where('parent_id', 1)->get();
        return response()->json(['data' => $modules], 200);
    }

    public function getMenus(Request $request)
    {
        $system = System::where('code', $request->system_code)->first();

        $routes = Route::whereHas('permissions', function ($permissions) use ($request) {
            $permissions
                ->where('role_id', $request->role_id)
                ->where('user_id', $request->user_id);
        })->with(['shortcuts' => function ($shortcuts) {
            $shortcuts->with('images');
        }])->get();
        return response()->json(['data' => $routes], 200);
    }

    public function getMegaMenus(Request $request)
    {
        $system = System::where('code', $request->system_code)->first();
        $modules = Catalogue::with(['routes' => function ($route) {
            $route->where('type_id', 2);
        }])->where('parent_id', $system->id)->get();
        return response()->json(['data' => $modules], 200);
    }

    public function store(Request $request)
    {

    }

    public function show(Route $route)
    {
        //
    }

    public function update(Request $request, Route $route)
    {
        //
    }

    public function destroy(Route $route)
    {
        //
    }
}
