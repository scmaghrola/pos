<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn($q) => $q->where('name', 'like', "%{$request->q}%")
                ->orWhere('email', 'like', "%{$request->q}%"))
            ->paginate(10);

        if ($request->ajax()) {
            return view('pos.users.table', compact('users'))->render();
        }

        return view('pos.users.users', compact('users'));
    }
    // public function index(Request $request)
    // {
    //     $users = User::paginate(10);
    //     $roles = Role::all(); // fetch all roles
    //     if($request->ajax()){
    //         $html = view('pos.users.table', compact('users', 'roles'));
    //         return response()->json(['html' => $html], 200);
    //         // return 
    //     }else{

    //         return view('pos.users.users', compact('users', 'roles'));
    //     }
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all(); // get all roles
        return view('pos.users.add_user', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name', // validate role
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Assign role
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions();

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles, // collection
                'permissions' => $permissions, // collection
                'created_at' => $user->created_at->diffForHumans(),
            ]);
        }

        $action = $request->query('action'); // Get 'action' query parameter
        if ($action === 'edit') {
            return view('pos.users.edit_user', compact('user', 'roles', 'permissions'));
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Editing Super Admin is not allowed.');
        }

        $user = User::findOrFail($user->id);
        $roles = Role::all(); // fetch all roles
        return view('pos.users.edit_user', compact('user', 'roles'));
        // return response()->json([
        //     'id' => $user->id,
        //     'name' => $user->name,
        //     'email' => $user->email,
        //     'roles' => $user->roles->pluck('id')->toArray(),
        // ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'User updated successfully']);
    }

    public function profile()
    {
        $user = auth()->user();
        return view('pos.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete the Super Admin account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
