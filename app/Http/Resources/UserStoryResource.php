<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'project_id' => $this->project_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'tasks' => $this->tasks,
            'team_members' => $this->team_members,
        ];
    }
}
