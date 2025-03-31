<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserStoryController extends Controller
{
    public function index()
    {
        // Logic to get all user stories
    }
    public function show($id)
    {
        // Logic to get a specific user story by ID
    }

    public function store(Request $request)
    {
        // Logic to create a new user story
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing user story
    }

    public function destroy($id)
    {
        // Logic to delete a user story
    }
}
