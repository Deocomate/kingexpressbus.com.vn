<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $normalizedSlug = Str::slug($slug);
        $page = null;

        if (Schema::hasTable('pages')) {
            $page = DB::table('pages')->where('slug', $normalizedSlug)->first();
        }

        if (!$page) {
            $page = $this->resolveFallbackPage($normalizedSlug);
        }

        abort_if(!$page, 404);

        $description = $page->description ?? $this->excerptFromContent($page->content ?? '');

        return view('client.page.show', [
            'page' => $page,
            'title' => $page->title ?: 'Trang nội dung',
            'description' => Str::limit($description, 155),
        ]);
    }

    private function resolveFallbackPage(string $slug): ?object
    {
        $webProfile = DB::table('web_profiles')->where('is_default', true)->first();
        if (!$webProfile) {
            return null;
        }

        $fallbacks = [
            'gioi-thieu' => [
                'title' => 'Giới thiệu King Express Bus',
                'description' => 'Thông tin giới thiệu về lịch sử và dịch vụ của King Express Bus.',
                'content' => $webProfile->introduction_content,
                'updated_at' => $webProfile->updated_at ?? null,
            ],
            'chinh-sach' => [
                'title' => 'Chính sách hỗ trợ khách hàng',
                'description' => 'Tổng hợp chính sách đặt vé, thanh toán và hỗ trợ khách hàng của King Express Bus.',
                'content' => $webProfile->policy_content,
                'updated_at' => $webProfile->updated_at ?? null,
            ],
        ];

        if (!isset($fallbacks[$slug])) {
            return null;
        }

        $config = $fallbacks[$slug];
        if (empty($config['content'])) {
            return null;
        }

        return (object) [
            'title' => $config['title'],
            'slug' => $slug,
            'description' => $config['description'],
            'content' => $config['content'],
            'updated_at' => $config['updated_at'],
        ];
    }

    private function excerptFromContent(string $content): string
    {
        $plain = trim(strip_tags($content));
        return $plain === '' ? 'Nội dung tổng hợp của King Express Bus.' : $plain;
    }
}
