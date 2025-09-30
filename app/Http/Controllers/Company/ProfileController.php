<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $company = DB::table('companies')->where('user_id', Auth::id())->first();
        return view('company.profile.index', compact('company'));
    }

    public function update(Request $request)
    {
        $company = DB::table('companies')->where('user_id', Auth::id())->first();
        $companyId = $company->id ?? null;

        if (empty($request->slug) && !empty($request->name)) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies')->ignore($companyId),
            ],
            'phone' => 'nullable|string|max:20',
            'hotline' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'image_list_url' => 'nullable|json',
            'content' => 'nullable|string',
        ], [
            'slug.unique' => 'Tên slug này đã được sử dụng, vui lòng chọn tên khác.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['updated_at'] = now();

        if ($companyId) {
            DB::table('companies')->where('id', $companyId)->update($validatedData);
            $message = 'Cập nhật thông tin nhà xe thành công.';
        } else {
            $validatedData['user_id'] = Auth::id();
            $validatedData['created_at'] = now();
            $validatedData['priority'] = 0;
            DB::table('companies')->insert($validatedData);
            $message = 'Lưu thông tin nhà xe thành công.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }
}
