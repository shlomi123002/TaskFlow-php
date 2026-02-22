<?php

namespace Tests\Feature;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class WorkspaceCleanupCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_only_workspaces_deleted_more_than_30_days_ago()
    {
        // create two workspaces and soft delete both
        $recent = Workspace::create(['name' => 'recent']);
        $old = Workspace::create(['name' => 'old']);
        
        $recent->delete();
        $old->delete();
        
        // adjust deleted_at timestamps
        $recent->deleted_at = now()->subDays(10);
        $recent->save(['timestamps' => false]);

        $old->deleted_at = now()->subDays(31);
        $old->save(['timestamps' => false]);

        // sanity check: both are still in database (trashed)
        $this->assertNotNull(Workspace::withTrashed()->find($recent->id));
        $this->assertNotNull(Workspace::withTrashed()->find($old->id));

        $exit = Artisan::call('workspace:cleanup');
        $this->assertEquals(0, $exit);

        // recent should still exist (soft deleted) 
        $this->assertNotNull(Workspace::withTrashed()->find($recent->id));
        // old should have been permanently deleted
        $this->assertNull(Workspace::withTrashed()->find($old->id));
    }

    public function test_it_outputs_message_when_there_is_nothing_to_cleanup()
    {
        $exit = Artisan::call('workspace:cleanup');

        $this->assertEquals(0, $exit);
        $this->assertStringContainsString('No workspaces to cleanup', Artisan::output());
    }
}
