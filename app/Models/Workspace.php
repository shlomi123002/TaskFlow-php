<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Workspace extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'created_by'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_user')->withTimestamps();
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
