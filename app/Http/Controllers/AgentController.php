<?php

namespace App\Http\Controllers;

use App\Models\CollectionAgent;
use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgentController extends Controller
{
    public function index()
    {
        $agents = CollectionAgent::with('user')->get();
        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && !$permissions->can_add_agent) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to add agents.');
        }

        return view('admin.agents.create');
    }

    public function store(Request $request)
    {
        try {
            Log::info('Starting agent creation process', ['user_id' => Auth::guard('admin')->user()->user_id]);

            $user = Auth::guard('admin')->user();
            $permissions = $user->role->permissions;

            if ($user->role->role_name !== 'admin' && !$permissions->can_add_agent) {
                Log::warning('Permission denied for adding agent', ['user_id' => $user->user_id]);
                return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to add agents.');
            }

            Log::info('Validating request data', $request->all());
            $validated = $request->validate([
                'full_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'photo' => 'nullable|file|mimes:jpg,png|max:2048',
                'aadhar' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'passbook' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            ]);

            Log::info('Creating user with agent role');
            $agentRole = Role::where('role_name', 'agent')->first();
            if (!$agentRole) {
                Log::error('Agent role not found');
                return redirect()->route('admin.agents.index')->with('error', 'Agent role not found. Please ensure roles are properly seeded.');
            }

            $user = User::create([
                'role_id' => $agentRole->role_id,
                'username' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email' => $validated['email'],
                'full_name' => $validated['full_name'],
            ]);

            Log::info('User created', ['user_id' => $user->user_id]);

            Log::info('Creating agent');
            $agent = CollectionAgent::create([
                'user_id' => $user->user_id,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            Log::info('Agent created', ['agent_id' => $agent->agent_id]);

            Log::info('Handling document uploads');
            $documents = ['photo', 'aadhar', 'passbook'];
            foreach ($documents as $docType) {
                if ($request->hasFile($docType)) {
                    $path = $request->file($docType)->store('documents', 'public');
                    Document::create([
                        'agent_id' => $agent->agent_id,
                        'document_type' => $docType,
                        'file_path' => $path,
                    ]);
                    Log::info('Document uploaded', ['type' => $docType, 'path' => $path]);
                }
            }

            Log::info('Agent creation completed successfully');
            return redirect()->route('admin.agents.index')->with('success', 'Agent created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create agent', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.agents.index')->with('error', 'Failed to create agent. Please try again. Error: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        $agent = CollectionAgent::with('user', 'documents', 'loans')->findOrFail($id);
        return view('admin.agents.show', compact('agent'));
    }

    public function edit($id)
    {
        $agent = CollectionAgent::with('user')->findOrFail($id);
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && (!$permissions->can_edit_delete || now()->diffInHours($agent->created_at) > $permissions->edit_delete_time_limit)) {
            return redirect()->route('admin.agents.index')->with('error', 'You do not have permission to edit this agent.');
        }

        return view('admin.agents.edit', compact('agent'));
    }

    public function update(Request $request, $id)
    {
        $agent = CollectionAgent::findOrFail($id);
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && (!$permissions->can_edit_delete || now()->diffInHours($agent->created_at) > $permissions->edit_delete_time_limit)) {
            return redirect()->route('admin.agents.index')->with('error', 'You do not have permission to edit this agent.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $agent->user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_status' => 'required|in:pending,approved,rejected',
            'photo' => 'nullable|file|mimes:jpg,png|max:2048',
            'aadhar' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'passbook' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $agent->user->update([
            'email' => $validated['email'],
            'full_name' => $validated['full_name'],
        ]);

        $agent->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'profile_status' => $validated['profile_status'],
        ]);

        $documents = ['photo', 'aadhar', 'passbook'];
        foreach ($documents as $docType) {
            if ($request->hasFile($docType)) {
                $oldDoc = $agent->documents()->where('document_type', $docType)->first();
                if ($oldDoc) {
                    Storage::disk('public')->delete($oldDoc->file_path);
                    $oldDoc->delete();
                }
                $path = $request->file($docType)->store('documents', 'public');
                Document::create([
                    'agent_id' => $agent->agent_id,
                    'document_type' => $docType,
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.agents.index')->with('success', 'Agent updated successfully.');
    }

    public function destroy($id)
    {
        $agent = CollectionAgent::findOrFail($id);
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && (!$permissions->can_edit_delete || now()->diffInHours($agent->created_at) > $permissions->edit_delete_time_limit)) {
            return redirect()->route('admin.agents.index')->with('error', 'You do not have permission to delete this agent.');
        }

        foreach ($agent->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
        }

        $agent->user->delete();
        $agent->delete();

        return redirect()->route('admin.agents.index')->with('success', 'Agent deleted successfully.');
    }
}
