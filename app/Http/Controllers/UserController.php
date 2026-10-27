<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(
            User::all()->makeHidden(['U_password'])
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'U_id' => 'required|string|size:6|unique:Users,U_id',
            'U_name' => 'required|string|max:100',
            'U_email' => 'required|email|max:100|unique:Users,U_email',
            'U_password' => 'required|string|min:8|max:255',
        ]);

        $data['U_password'] = Hash::make($data['U_password']);

        $user = User::create($data);

        return response()->json(
            $user->makeHidden(['U_password']),
            201
        );
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json(
            $user->makeHidden(['U_password'])
        );
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'U_name' => 'sometimes|required|string|max:100',
            'U_email' => 'sometimes|required|email|max:100|unique:Users,U_email,' . $id . ',U_id',
            'U_password' => 'sometimes|required|string|min:8|max:255',
        ]);

        if (isset($data['U_password'])) {
            $data['U_password'] = Hash::make($data['U_password']);
        }

        $user->update($data);

        return response()->json(
            $user->makeHidden(['U_password'])
        );
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
