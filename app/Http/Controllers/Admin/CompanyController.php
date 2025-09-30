<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('companies as c')
            ->join('users as u', 'c.user_id', '=', 'u.id')
            ->select('c.*', 'u.email as user_email');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('c.name', 'like', "%{$searchTerm}%")
                    ->orWhere('c.phone', 'like', "%{$searchTerm}%")
                    ->orWhere('c.email', 'like', "%{$searchTerm}%")
                    ->orWhere('u.email', 'like', "%{$searchTerm}%");
            });
        }

        $companies = $query->orderByDesc('c.priority')->orderByDesc('c.created_at')->paginate(15)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000|unique:companies,name',
            'user_email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'thumbnail_url' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Create User
            $userId = DB::table('users')->insertGetId([
                'name' => $request->input('name'),
                'email' => $request->input('user_email'),
                'password' => Hash::make($request->input('password')),
                'role' => 'company',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create Company
            $companyData = $request->only('name', 'phone', 'email', 'address', 'thumbnail_url', 'content', 'priority');
            $companyData['slug'] = Str::slug($request->input('name'));
            $companyData['user_id'] = $userId;
            $companyData['created_at'] = Carbon::now();
            $companyData['updated_at'] = Carbon::now();

            DB::table('companies')->insert($companyData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Thêm nhà xe và tài khoản thành công.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $company = DB::table('companies as c')
            ->join('users as u', 'c.user_id', '=', 'u.id')
            ->select('c.*', 'u.email as user_email')
            ->where('c.id', $id)
            ->first();

        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhà xe.'], 404);
        }

        return response()->json(['success' => true, 'data' => $company]);
    }

    public function update(Request $request, string $id)
    {
        $company = DB::table('companies')->where('id', $id)->first();
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhà xe.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000|unique:companies,name,' . $id,
            'user_email' => 'required|email|max:255|unique:users,email,' . $company->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'thumbnail_url' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Update Company
            $companyData = $request->only('name', 'phone', 'email', 'address', 'thumbnail_url', 'content', 'priority');
            $companyData['slug'] = Str::slug($request->input('name'));
            $companyData['updated_at'] = Carbon::now();
            DB::table('companies')->where('id', $id)->update($companyData);

            // Update User
            $userData = [
                'name' => $request->input('name'),
                'email' => $request->input('user_email'),
                'updated_at' => Carbon::now(),
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->input('password'));
            }
            DB::table('users')->where('id', $company->user_id)->update($userData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cập nhật nhà xe và tài khoản thành công.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $company = DB::table('companies')->where('id', $id)->first();
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhà xe.'], 404);
        }

        // The onDelete('cascade') in the migration will handle deleting the company record.
        $deleted = DB::table('users')->where('id', $company->user_id)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa nhà xe và tài khoản liên quan thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Xóa thất bại, vui lòng thử lại.'], 500);
    }
}
