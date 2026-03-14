<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Comment;
use App\Models\Workspace;
use App\Models\Project;
use App\Policies\CommentPolicy;
use App\Policies\WorkspacePolicy;
use App\Policies\ProjectPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Comment::class => CommentPolicy::class,
        Workspace::class => WorkspacePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
