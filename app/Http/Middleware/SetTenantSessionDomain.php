<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetTenantSessionDomain
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Set the session domain dynamically
        $host = $request->getHost();
        $domainParts = explode('.', $host);
        $subdomain = array_shift($domainParts);
        $baseDomain = implode('.', $domainParts);
        $sessionDomain = "{$subdomain}.{$baseDomain}";
        Config::set('session.domain', $sessionDomain);

        // Load tenant-specific configurations
        $tenant = $this->tenantService->getTenant();
        if ($tenant) {
            // Clear existing connections
            DB::purge('tenant');
            // Set tenant-specific database connection details
            Config::set('database.connections.tenant.database', $tenant->database_name);
            Config::set('database.connections.tenant.host', $tenant->database_host);
            Config::set('database.connections.tenant.username', $tenant->database_username);
            Config::set('database.connections.tenant.password', $tenant->database_password);
            // Reconnect with the new configuration
            DB::reconnect('tenant');
        }

        return $next($request);
    }
}
