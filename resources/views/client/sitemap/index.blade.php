<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    <!-- Homepage -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Static Pages -->
    <url>
        <loc>{{ route('client.routes.search') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ route('client.companies.index') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc>{{ route('client.contact.index') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <!-- Routes -->
    @foreach($routes as $route)
    <url>
        <loc>{{ route('client.routes.show', $route->slug) }}</loc>
        <lastmod>{{ $route->updated_at ? \Carbon\Carbon::parse($route->updated_at)->toAtomString() : now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    <!-- Companies -->
    @foreach($companies as $company)
    <url>
        <loc>{{ route('client.companies.show', $company->slug) }}</loc>
        <lastmod>{{ $company->updated_at ? \Carbon\Carbon::parse($company->updated_at)->toAtomString() : now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    <!-- Custom Pages -->
    @foreach($pages as $page)
    <url>
        <loc>{{ route('client.pages.show', $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at ? \Carbon\Carbon::parse($page->updated_at)->toAtomString() : now()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>
