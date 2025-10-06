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
                'label' => __('client.contact.channels.hotline'),
                'value' => $webProfile->hotline ?? null,
                'href' => isset($webProfile->hotline) ? 'tel:' . preg_replace('/[^\d+]/', '', $webProfile->hotline) : null,
            ],
            [
                'icon' => 'fa-solid fa-headset',
                'label' => __('client.contact.channels.care'),
                'value' => $webProfile->phone ?? null,
                'href' => isset($webProfile->phone) ? 'tel:' . preg_replace('/[^\d+]/', '', $webProfile->phone) : null,
            ],
            [
                'icon' => 'fa-regular fa-envelope',
                'label' => __('client.contact.channels.email'),
                'value' => $webProfile->email ?? null,
                'href' => isset($webProfile->email) ? 'mailto:' . $webProfile->email : null,
            ],
            [
                'icon' => 'fa-brands fa-facebook-messenger',
                'label' => __('client.contact.channels.messenger'),
                'value' => $webProfile->facebook_url ?? null,
                'href' => $webProfile->facebook_url ?? null,
            ],
            [
                'icon' => 'fa-brands fa-facebook',
                'label' => __('client.contact.channels.facebook'),
                'value' => $webProfile->facebook_url ?? null,
                'href' => $webProfile->facebook_url ?? null,
            ],
            [
                'icon' => 'fa-solid fa-comment-dots',
                'label' => __('client.contact.channels.zalo'),
                'value' => $webProfile->zalo_url ?? null,
                'href' => $webProfile->zalo_url ?? null,
            ],
        ], function ($channel) {
            return !empty($channel['value']);
        }));

        $faqs = [
            [
                'question' => __('client.contact.faq.q1'),
                'answer' => __('client.contact.faq.a1'),
            ],
            [
                'question' => __('client.contact.faq.q2'),
                'answer' => __('client.contact.faq.a2'),
            ],
            [
                'question' => __('client.contact.faq.q3'),
                'answer' => __('client.contact.faq.a3'),
            ],
        ];

        $workingHours = [
            'weekday_label' => __('client.contact.hours.weekday_label'),
            'weekday_hours' => '07:00 - 22:00',
            'weekend_label' => __('client.contact.hours.weekend_label'),
            'weekend_hours' => '08:00 - 21:00',
        ];

        $mapEmbed = $webProfile->map_embedded ?? null;

        return view('client.contact.index', [
            'webProfile' => $webProfile,
            'offices' => $offices,
            'supportChannels' => $supportChannels,
            'faqs' => $faqs,
            'workingHours' => $workingHours,
            'mapEmbed' => $mapEmbed,
            'title' => __('client.contact.meta.title'),
            'description' => __('client.contact.meta.description'),
        ]);
    }
}
