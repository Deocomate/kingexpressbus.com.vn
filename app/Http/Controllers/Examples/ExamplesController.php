<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamplesController extends Controller
{
    /**
     * Tự động điều hướng đến function có tên tương ứng với $name.
     *
     * @param string $name Tên của example (tương ứng với tên function).
     * @return \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application|void
     */
    public function index($name)
    {
        // Chuyển đổi tên từ dạng kebab-case (viết-liền-dấu-gạch) sang camelCase (vietLienDauGach)
        $methodName = \Illuminate\Support\Str::camel($name);

        // Kiểm tra xem phương thức có tồn tại trong controller này không
        if (method_exists($this, $methodName)) {
            // Nếu có, gọi phương thức đó và trả về kết quả
            return $this->$methodName();
        }

        // Nếu không tìm thấy phương thức, trả về lỗi 404 Not Found
        abort(404, 'Example not found.');
    }

    public function ckeditor()
    {
        return view("examples.ckeditor");
    }

    public function ckfinder()
    {
        return view("examples.ckfinder");
    }

    public function dropzone()
    {
        return view("examples.ckfinder-dropzone");
    }
}
