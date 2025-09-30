<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebProfileController extends Controller
{
    public function index()
    {
        $profiles = DB::table('web_profiles')->orderByDesc('is_default')->orderBy('profile_name')->get();
        return view('admin.web_profiles.index', compact('profiles'));
    }

    public function create()
    {
        return view('admin.web_profiles.edit');
    }

    public function store(Request $request)
    {
        $this->validateProfile($request);

        DB::transaction(function () use ($request) {
            if ($request->has('is_default')) {
                DB::table('web_profiles')->update(['is_default' => false]);
            }

            DB::table('web_profiles')->insert([
                'profile_name' => $request->input('profile_name'),
                'is_default' => $request->has('is_default'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'logo_url' => $request->input('logo_url'),
                'favicon_url' => $request->input('favicon_url'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'hotline' => $request->input('hotline'),
                'whatsapp' => $request->input('whatsapp'),
                'address' => $request->input('address'),
                'facebook_url' => $request->input('facebook_url'),
                'zalo_url' => $request->input('zalo_url'),
                'map_embedded' => $request->input('map_embedded'),
                'policy_content' => $request->input('policy_content'),
                'introduction_content' => $request->input('introduction_content'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.web_profiles.index')->with('success', 'Tạo cấu hình website thành công.');
    }

    public function edit(string $id)
    {
        $profile = DB::table('web_profiles')->find($id);
        abort_if(!$profile, 404);
        return view('admin.web_profiles.edit', compact('profile'));
    }

    public function update(Request $request, string $id)
    {
        $this->validateProfile($request, $id);

        DB::transaction(function () use ($request, $id) {
            if ($request->has('is_default')) {
                DB::table('web_profiles')->where('id', '!=', $id)->update(['is_default' => false]);
            }

            DB::table('web_profiles')->where('id', $id)->update([
                'profile_name' => $request->input('profile_name'),
                'is_default' => $request->has('is_default'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'logo_url' => $request->input('logo_url'),
                'favicon_url' => $request->input('favicon_url'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'hotline' => $request->input('hotline'),
                'whatsapp' => $request->input('whatsapp'),
                'address' => $request->input('address'),
                'facebook_url' => $request->input('facebook_url'),
                'zalo_url' => $request->input('zalo_url'),
                'map_embedded' => $request->input('map_embedded'),
                'policy_content' => $request->input('policy_content'),
                'introduction_content' => $request->input('introduction_content'),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.web_profiles.index')->with('success', 'Cập nhật cấu hình website thành công.');
    }

    public function destroy(string $id)
    {
        $profile = DB::table('web_profiles')->find($id);
        abort_if(!$profile, 404);

        if ($profile->is_default) {
            return back()->with('error', 'Không thể xóa cấu hình đang được đặt làm mặc định.');
        }

        if (DB::table('web_profiles')->count() <= 1) {
            return back()->with('error', 'Không thể xóa cấu hình cuối cùng.');
        }

        DB::table('web_profiles')->where('id', $id)->delete();
        return redirect()->route('admin.web_profiles.index')->with('success', 'Đã xóa cấu hình thành công.');
    }

    public function setDefault(string $id)
    {
        DB::transaction(function () use ($id) {
            DB::table('web_profiles')->update(['is_default' => false]);
            DB::table('web_profiles')->where('id', $id)->update(['is_default' => true]);
        });

        return redirect()->route('admin.web_profiles.index')->with('success', 'Đã đặt cấu hình làm mặc định thành công.');
    }

    private function validateProfile(Request $request, $id = null)
    {
        $rules = [
            'profile_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo_url' => 'nullable|string|max:255',
            'favicon_url' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'hotline' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'zalo_url' => 'nullable|url|max:255',
            'map_embedded' => 'nullable|string',
            'policy_content' => 'nullable|string',
            'introduction_content' => 'nullable|string',
        ];

        $request->validate($rules);
    }
}
