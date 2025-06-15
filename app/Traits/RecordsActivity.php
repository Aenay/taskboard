<?php

namespace App\Traits;

use App\Models\Activity;
use App\Models\User;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        foreach (static::getRecordableEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }

    protected static function getRecordableEvents()
    {
        return ['created', 'updated', 'deleted'];
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function recordActivity($action)
    {
        $description = $this->getActivityDescription($action);

        $this->activities()->create([
            'user_id' => auth()->id() ?? 1,
            'action' => $action,
            'description' => $description,
            'properties' => $this->getActivityProperties($action)
        ]);
    }

    protected function getActivityDescription($action)
    {
        $modelName = class_basename($this);

        if ($modelName === 'Task') {
            return match($action) {
                'created' => "Created task '{$this->title}' and assigned to {$this->assignedTo->name}",
                'updated' => $this->getTaskUpdateDescription(),
                'deleted' => "Deleted task '{$this->title}'",
                default => $action . " " . $modelName
            };
        }

        return "{$action} {$modelName}";
    }

    protected function getTaskUpdateDescription()
    {
        $changes = $this->getChanges();
        $descriptions = [];

        if (isset($changes['status'])) {
            $descriptions[] = "changed status to '{$changes['status']}'";
        }
        if (isset($changes['assigned_to'])) {
            $newAssignee = User::find($changes['assigned_to'])->name;
            $descriptions[] = "reassigned to {$newAssignee}";
        }
        if (isset($changes['documentation'])) {
            $descriptions[] = "added documentation";
        }

        return "Updated task '{$this->title}': " . implode(', ', $descriptions);
    }

    protected function getActivityProperties($action)
    {
        return [
            'before' => array_diff_assoc($this->getOriginal(), $this->getAttributes()),
            'after' => $action === 'deleted' ? [] : $this->getChanges(),
            'project_id' => $this->project_id ?? null,
            'assigned_to' => $this->assigned_to ?? null,
            'created_by' => $this->created_by ?? null,
        ];
    }
}
