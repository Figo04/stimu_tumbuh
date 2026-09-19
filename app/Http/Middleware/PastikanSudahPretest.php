<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Materi (dan halaman lain yang dipasangi middleware ini) terkunci sampai pre-test dikirim.
class PastikanSudahPretest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->sudahPretest()) {
            return redirect()->route('pretest');
        }

        return $next($request);
    }
}
