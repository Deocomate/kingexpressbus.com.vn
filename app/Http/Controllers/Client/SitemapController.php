<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function index()
    {
        $routes = DB::table('routes')
            ->where('is_active', true)
            ->select('id', 'slug', 'updated_at')
            ->orderBy('priority', 'desc')
            ->get();

        $companies = DB::table('companies')
            ->where('is_active', true)
            ->select('id', 'slug', 'updated_at')
            ->orderBy('priority', 'desc')
            ->get();

        $pages = DB::table('pages')
            ->where('is_active', true)
            ->select('id', 'slug', 'updated_at')
            ->get();

        $provinces = DB::table('provinces')
            ->where('is_active', true)
            ->select('id', 'slug', 'updated_at')
            ->get();

        return response()->view('client.sitemap.index', compact('routes', 'companies', 'pages', 'provinces'))
            ->header('Content-Type', 'text/xml');
    }
}
