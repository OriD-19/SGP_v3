<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatusController extends Controller
{
    //
    public function index()
    {
        // Logic to get all statuses
        return response()->json([
            'statuses' => [
                // Example data
                ['id' => 1, 'name' => 'Open'],
                ['id' => 2, 'name' => 'In Progress'],
                ['id' => 3, 'name' => 'Closed'],
            ]
        ], 200);
    }

    public function show($id)
    {

    }

    public function store(Request $request)
    {
        // Logic to create a new status
        return response()->json([
            'message' => 'Status created successfully',
            'status' => [
                'id' => 1,
                'name' => $request->input('name'),
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing status
        return response()->json([
            'message' => 'Status updated successfully',
            'status' => [
                'id' => $id,
                'name' => $request->input('name'),
            ]
        ], 200);
    }

    public function destroy($id)
    {
        // Logic to delete a status
        return response()->json([
            'message' => 'Status deleted successfully'
        ], 204);
    }
}
