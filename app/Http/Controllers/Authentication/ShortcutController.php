<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Permission;
use App\Models\Authentication\Route;
use App\Models\Authentication\System;
use App\Models\Ignug\State;
use Illuminate\Http\Request;

class ShortcutController extends Controller
{
    public function index(Request $request)
    {
        $shortcuts = Permission::whereHas('shortcuts', function ($shortcuts) use ($request) {
            $shortcuts
                ->where('role_id', $request->role_id)
                ->where('user_id', $request->user_id);
        })->with(['shortcuts' => function ($shortcuts) {
            $shortcuts->with('images');
        }])->with('route')
            ->where('institution_id', $request->institution_id)
            ->where('state_id', State::where('code', '1')->first()->id)
            ->limit(100)
            ->get();
        return response()->json(['data' => $shortcuts], 200);
    }
}
