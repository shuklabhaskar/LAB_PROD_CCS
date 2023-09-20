<?php

namespace App\Http\Controllers\Modules\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class LoginController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Authentication/Login', [
        ]);
    }

    public function verify(Request $request)
    {

        /* LOGIN VALIDATION */

        $data = $request->validate([
            'login_id' => 'required|integer',
            'password' => 'required'
        ]);

        $user = DB::table('user_inventory')
            ->where('user_role_id', '=', 1)
            ->where('user_login', '=', $request->login_id)
            ->where('user_pwd', '=', $request->password)
            ->count();

        if ($user == 1) {
            return redirect()
                ->to('/dashboard')
                ->with([
                    'status' => true,
                    'message' => 'USER LOGGED IN SUCCESSFULLY.'
                ]);
        }

    }


}
