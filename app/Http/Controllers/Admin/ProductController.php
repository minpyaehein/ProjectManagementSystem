<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\products;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    // product list page
    public function list()
    {

        $products = products::when(request('searchKey'), function ($query) {
            $query->whereAny(['products.name', 'products.price', 'products.count', 'categories.name'], 'like', '%' . request('searchKey') . '%');
        })
            ->select('products.id', 'products.name', 'products.price', 'products.description', 'products.category_id', 'products.count', 'products.image', 'categories.name as categoryName')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->paginate(3);

        return view('admin.product.productList', compact('products'));
    }

    // product create page
    public function create()
    {
        $categories = Category::get();
        return view('admin.product.productCreate', compact('categories'));
    }

    // product_create page
    public function productCreate(Request $request)
    {
        $this->validationCheck($request, 'create');
        $data = $this->requestProductData($request);

        if ($request->hasFile('image')) {
            $fileName = uniqid() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path() . '/productImages/', $fileName);
            $data['image'] = $fileName;
        }

        products::create($data);

        Alert::success('Insert Success', 'Product Insert Successfully...');

        return to_route('product_create');

    }

    // create | update validation check
    private function validationCheck($request, $action)
    {
        $rules = [
            'name'         => 'required|unique:products,name,' . $request->productId,
            'price'        => 'required',
            'description'  => 'required',
            'categoryName' => 'required',
            'count'        => 'required|numeric',
            // 'image' => 'required|mimes:png,jpg,jpeg,bmp|file'
        ];

        $rules['image'] = $action == 'create' ? "required|mimes:png,jpg,jpeg,bmp,webp|file" : "mimes:png,jpg,jpeg,bmp,webp|file";

        $message = [];

        $validator = $request->validate($rules, $message);

    }

    private function requestProductData($request)
    {
        return [
            'name'        => $request->name,
            'price'       => $request->price,
            'description' => $request->description,
            'category_id' => $request->categoryName,
            'count'       => $request->count,
        ];
    }

    // delete product
    public function delete($id)
    {
        products::where('id', $id)->delete();
        Alert::success('Delete Success', 'Product Delete Successfully...');
        return back();
    }

    // details product
    public function details($id)
    {
        $data = products::select('products.id', 'products.name', 'products.price', 'products.description', 'products.category_id', 'products.count', 'products.image', 'categories.name as categoryName')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.id', $id)->first();

        return view('admin.product.productDetails', compact('data'));
    }

    // details product
    public function edit($id)
    {
        $products = products::select('products.id', 'products.name', 'products.price', 'products.description', 'products.category_id', 'products.count', 'products.image', 'categories.name as categoryName')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.id', $id)->first();

        $categories = Category::get();

        return view('admin.product.productEdit', compact('products', 'categories'));
    }

    // update product
    public function update(Request $request)
    {

        $this->validationCheck($request, 'update');
        $data = $this->requestProductData($request);

        if ($request->hasFile('image')) {
            // delete old image
            if (file_exists(public_path('productImages/' . $request->oldImage))) {
                unlink(public_path('productImages/' . $request->oldImage));
            }

            // upload new image
            $fileName = uniqid() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path() . '/productImages/', $fileName);
            $data['image'] = $fileName;

        } else {
            $data['image'] = $request->oldImage;
        }

        products::where('id', $request->productId)->update($data);
        Alert::success('Update Success', 'Product Update Successfully...');
        return to_route('productList');

    }

}
