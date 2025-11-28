<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\WelcomeNewUserNotification;
use Illuminate\Support\Facades\Notification;


class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->orderBy('name')
            ->get();

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }



public function store(Request $request)
{
    $request->validate([
        'name'       => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email',
        'phone'      => 'required|string|max:15',
        'department' => 'required|string|max:100',
        'role'       => 'required|exists:roles,name',
    ]);

    // Generate strong random password
    $rawPassword = Str::random(8);

    $user = User::create([
        'employee_code' => 'EMP' . str_pad(User::max('id') + 1, 4, '0', STR_PAD_LEFT),
        'name'          => $request->name,
        'email'         => $request->email,
        'phone'         => $request->phone,
        'department'    => $request->department,
        'password'      => Hash::make($rawPassword),
        'is_active'     => true,
    ]);

    $user->assignRole($request->role);

    // SEND NOTIFICATION IMMEDIATELY (no queue)
    try {
        Notification::send($user, new WelcomeNewUserNotification($user, $rawPassword));
    } catch (\Exception $e) {
        Log::warning("Welcome notification failed for {$user->email}: " . $e->getMessage());
        // User is still created even if email fails
    }

    return response()->json([
        'success' => true,
        'message' => 'Staff created successfully! Login credentials sent instantly to their email.'
    ]);
}
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:15',
            'department' => 'required|string|max:100',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
        ]);

        $user->syncRoles($request->role);

        return response()->json(['success' => true, 'message' => 'User updated successfully!']);
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return response()->json(['success' => false, 'message' => 'Cannot delete the last Admin!'], 422);
        }

        $user->removeRole($user->roles->first());
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted!']);
    }
}