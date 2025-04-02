<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // used only on a single task
        // therefore, it is more detailed

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'team_members' => TeamMemberResource::collection($this->team_members),
            'user_story' => UserStoryEmbedTaskResource::make($this->user_story),
            'project' => ProjectEmbedTaskResource::make($this->project),
            'status' => StatusResource::make($this->status),
            'priority' => PriorityResource::make($this->priority),
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
