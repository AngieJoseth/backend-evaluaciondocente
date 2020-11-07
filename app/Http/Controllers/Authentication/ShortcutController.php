<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Route;
use Illuminate\Http\Request;

class ShortcutController extends Controller
{
    public function index(Request $request)
    {
        $routes = Route::whereHas('shortcuts', function ($shortcuts) use ($request) {
            $shortcuts
                ->where('role_id', $request->role_id)
                ->where('user_id', $request->user_id);
        })->with(['shortcuts' => function ($shortcuts) {
            $shortcuts->with('images');
        }])->orderBy('label')
            ->orderBy('order')
            ->get();
        return response()->json(['data' => $routes], 200);
    }
}
