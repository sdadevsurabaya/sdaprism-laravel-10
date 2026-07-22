<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $module, string $action, string $description, ?array $properties = null, ?object $user = null): ?ActivityLog
    {
        try {
            $currentUser = $user ?? Auth::user();
            
            $userName = $currentUser ? $currentUser->name : 'System/Guest';
            $userRole = 'Guest';

            if ($currentUser) {
                if (method_exists($currentUser, 'rolesUsers') && $currentUser->rolesUsers && $currentUser->rolesUsers->first()?->roles) {
                    $userRole = ucfirst($currentUser->rolesUsers->first()->roles->name);
                } else {
                    $userRole = 'User';
                }
            }

            return ActivityLog::create([
                'user_id'     => $currentUser ? $currentUser->id : null,
                'user_name'   => $userName,
                'user_role'   => $userRole,
                'module'      => $module,
                'action'      => $action,
                'description' => $description,
                'properties'  => $properties,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ActivityLogger Error: ' . $e->getMessage());
            return null;
        }
    }
}
