<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sprints' => SprintResource::collection($this->whenLoaded('sprints')),
            'user_stories' => UserStoryResource::collection($this->whenLoaded('userStories')),
            'team_members' => TeamMemberResource::collection($this->whenLoaded('teamMembers')),
        ];
    }
}
