<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffAuth
{
    /**
     * Usage in routes:
     *   Route::middleware('staff')->group(...)                       // any logged-in staff
     *   Route::middleware('staff:Finance Officer')->group(...)       // only Finance Officer
     *   Route::middleware('staff:KTM Officer,Finance Officer')->...  // either role
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! $request->session()->has('staff_id')) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        if (! empty($roles) && ! in_array($request->session()->get('staff_role'), $roles, true)) {
            abort(403, 'Your role does not have access to this page.');
        }

        return $next($request);
    }
}
