<?php
namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;

   class UpgradeToHttpsUnderNgrok
   {
       public function handle(Request $request, Closure $next)
       {
           if (env('APP_ENV') === 'local' && $request->server('HTTP_X_FORWARDED_PROTO') === 'https') {
               URL::forceScheme('https');
           }

           return $next($request);
       }
   }
