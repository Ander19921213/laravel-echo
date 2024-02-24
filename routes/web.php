<?php

use App\Events\TaskCreated;
use App\Http\Controllers\ProfileController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Project;

Route::middleware('auth')->group(function () {
    Route::get('/projects/{project}', function (Project $project) {
        $project->load('tasks');
        return Inertia::render('Project/Show', [
            'project' => $project,
        ]);
    });

    Route::get('/', function () {
        return Inertia::render('Index');
    });

    Route::get('/tasks', function () {
        return Task::latest()->pluck('body');
    });

    Route::post('/projects', function () {
        $task = Task::create(request(['body']));
        event(new TaskCreated($task));
    });

    Route::get('/api/projects/{project}', function (Project $project) {
        return $project->tasks->pluck('body');
    });

    Route::post('/api/projects/{project}/tasks', function (Project $project) {
        $task = $project->tasks()->create(request(['body']));
        event(new TaskCreated($task));
        return $task;
    });

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
