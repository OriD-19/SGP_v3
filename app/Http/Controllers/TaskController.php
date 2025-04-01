<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Logic to display a list of tasks
    }

    public function store(Request $request)
    {
        // Logic to store a new task
        return response()->json([
            'message' => 'Task created successfully',
            'task' => [
                'id' => 1,
                'title' => $request->input('title'),
            ]
        ], 201);
    }

    public function show($id)
    {
        // Logic to display a specific task
    }

    public function update(Request $request, $id)
    {
        // Logic to update a specific task
    }

    public function destroy($id)
    {
        // Logic to delete a specific task
        return response()->json([
            'message' => 'Task deleted successfully'
        ], 204);
    }
}
