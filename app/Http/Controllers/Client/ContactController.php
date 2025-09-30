<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index()
    {
        $webProfile = DB::table('web_profiles')->where('is_default', true)->first();

        $offices = DB::table('stops as s')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select([
                's.id',
                's.name',
                's.address',
                'p.name as province_name',
                'd.name as district_name',
            ])
            ->orderByDesc('s.priority')
            ->orderBy('p.priority')
            ->get();

        $supportChannels = array_values(array_filter([
            [
                'icon' => 'fa-solid fa-phone',
                'label' => 'Hotline đặt vé',
                'value' => $webProfile->hotline ?? null,
                'href' => isset($webProfile->hotline) ? 'tel:' . preg_replace('/[^\d+]/', '', $webProfile->hotline) : null,
            ],
            [
                'icon' => 'fa-solid fa-headset',
                'label' => 'Tổng đài chăm sóc',
                'value' => $webProfile->phone ?? null,
                'href' => isset($webProfile->phone) ? 'tel:' . preg_replace('/[^\d+]/', '', $webProfile->phone) : null,
            ],
            [
                'icon' => 'fa-regular fa-envelope',
                'label' => 'Email hỗ trợ',
                'value' => $webProfile->email ?? null,
                'href' => isset($webProfile->email) ? 'mailto:' . $webProfile->email : null,
            ],
            [
                'icon' => 'fa-brands fa-facebook-messenger',
                'label' => 'Messenger',
                'value' => $webProfile->facebook_url ?? null,
                'href' => $webProfile->facebook_url ?? null,
            ],
            [
                'icon' => 'fa-brands fa-facebook',
                'label' => 'Facebook fanpage',
                'value' => $webProfile->facebook_url ?? null,
                'href' => $webProfile->facebook_url ?? null,
            ],
            [
                'icon' => 'fa-solid fa-comment-dots',
                'label' => 'Zalo',
                'value' => $webProfile->zalo_url ?? null,
                'href' => $webProfile->zalo_url ?? null,
            ],
        ], function ($channel) {
            return !empty($channel['value']);
        }));

        $faqs = [
            [
                'question' => 'Làm sao để kiểm tra tình trạng đặt vé?',
                'answer' => 'Đăng nhập tài khoản King Express Bus, vào trang Tài khoản > Đặt vé của tôi để xem chi tiết mã vé và trạng thái.',
            ],
            [
                'question' => 'Thời gian hỗ trợ của tổng đài?',
                'answer' => 'Tổng đài hỗ trợ khách hàng từ 07:00 đến 22:00 mỗi ngày, kể cả cuối tuần và ngày lễ.',
            ],
            [
                'question' => 'Có thể đổi lịch khởi hành không?',
                'answer' => 'Bạn có thể liên hệ tổng đài trước giờ khởi hành 12 giờ để được tư vấn đổi lịch hoặc hủy vé theo quy định.',
            ],
        ];

        $workingHours = [
            'weekday' => '07:00 - 22:00 (thứ Hai - thứ Sáu)',
            'weekend' => '08:00 - 21:00 (thứ Bảy, Chủ Nhật)',
        ];

        $mapEmbed = $webProfile->map_embedded ?? null;

        return view('client.contact.index', [
            'webProfile' => $webProfile,
            'offices' => $offices,
            'supportChannels' => $supportChannels,
            'faqs' => $faqs,
            'workingHours' => $workingHours,
            'mapEmbed' => $mapEmbed,
            'title' => 'Liên hệ King Express Bus',
            'description' => 'Thông tin dịch vụ khách hàng, tổng đài và hệ thống văn phòng của King Express Bus.',
        ]);
    }
}
