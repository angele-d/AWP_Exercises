<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    // User profile : projects managed + projects where he is a member
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $managedProjects = $user->managedProjects()->with('members')->get();
        $memberProjects = $user->memberProjects()->with('manager')->get();

        return view('projects.index', compact('managedProjects', 'memberProjects'));
    }

    // Creation form
    public function create()
    {
        // List of users excluding the currently authenticated user
        $users = User::where('id', '!=', Auth::id())->get();
        return view('projects.create', compact('users'));
    }

    // Store a newly created project in storage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        // Project creation (the connected user is the manager)
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $project = $user->managedProjects()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'done_jobs' => $request->has('done_jobs'),
        ]);

        // If the "members" field is not empty, associate the selected members with the project
        if (!empty($validated['members'])) {
            $project->members()->sync($validated['members']);
        }

        return redirect()->route('projects.index')->with('success', 'Projet créé avec succès.');
    }

    // Edition form
    public function edit(Project $project)
    {
        $this->authorize('updateStatus', $project);

        $users = User::where('id', '!=', $project->user_id)->get();
        return view('projects.edit', compact('project', 'users'));
    }

    // Update the project
    public function update(Request $request, Project $project)
    {
        $user = Auth::user();

        // 1st case : The user is the Manager (he can modify everything)
        if ($user->id === $project->user_id) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id',
            ]);

            $project->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'done_jobs' => $request->has('done_jobs'),
            ]);

            $project->members()->sync($request->input('members', []));
        } 
        // 2nd case : The user is a simple Member (he can only check/uncheck 'done_jobs')
        elseif ($project->members->contains($user->id)) {
            $project->update([
                'done_jobs' => $request->has('done_jobs'),
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('projects.index')->with('success', 'Projet mis à jour.');
    }
}