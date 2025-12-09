<?php
    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\Auth;

    class RoleMiddleware
    {
        public function handle($request, Closure $next, ...$roles)
        {
            $user = Auth::user();

            if (!$user || !$user->rule) {
                abort(403, 'Role tidak ditemukan.');
            }

            if (!in_array($user->rule->name, $roles)) {
                abort(403, 'Anda tidak punya akses.');
            }

            return $next($request);
        }
    }
?>