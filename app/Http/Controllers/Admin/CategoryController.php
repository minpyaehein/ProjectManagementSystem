<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CategoryController extends Controller
{
    // category list page
    public function list()
    {
        $data = Category::orderBy('id', 'asc')->paginate(5);
        return view('admin.category.categoryList', compact('data'));
    }

    // create data
    public function create(Request $request)
    {
        $validator = $request->validate([
            'categoryName' => 'required|unique:categories,name',
        ], [
            'categoryName.required' => 'Category field ကို ဖြည့်စွက်ရန် လိုအပ်ပါသည်။',
        ]);

        Category::create([
            'name' => $request->categoryName,
        ]);

        Alert::success('Insert Success', 'Category Inserted Successfully...');

        return back();
    }

    // delete category
    public function delete($id)
    {
        Category::where('id', $id)->delete();

        Alert::success('Delete Success', 'Category Delete Successfully...');

        return back();
    }

    // edit category
    public function edit($id)
    {
        $data = Category::where('id', $id)->first();

        return view('admin.category.categoryUpdate', compact('data'));
    }

    // category update
    public function update(Request $request)
    {
        $validator = $request->validate([
            'categoryName' => 'required|unique:categories,name,' . $request->categoryId,
        ]);

        Category::where('id', $request->categoryId)->update([
            'name' => $request->categoryName,
        ]);

        Alert::success('Update Success', 'Category Update Successfully...');

        return to_route('categoryList');

    }
}
