<?php
  
namespace App\Http\Controllers;
   
use App\Models\Product;
use Illuminate\Http\Request;
  
class ProductController extends Controller
{
    
    public function index()
    {
        $products = Product::latest()->paginate(5);

        // return response()->json($products);
    
        return view('products.index',compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
     
   
    public function create()
    {
        return view('products.create');
    }
    
   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        Product::create($request->all());
     
        return redirect()->route('products.index')
                        ->with('success','Product created successfully.');
    }
     
  
    public function show(Product $product)
    {
        return response()->json($product);
        return view('products.show',compact('product'));
    } 
     
    
    public function edit(Product $product)
    {
        return response()->json($product);
        return view('products.edit',compact('product'));
    }
    
   
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        return $product->update($request->all());

        // return response()->json($p);
    
        // return redirect()->route('products.index')
        //                 ->with('success','Product updated successfully');
    }
    
   
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['status', 'data delete successful']);
        return redirect()->route('products.index')
                        ->with('success','Product deleted successfully');
    }
}