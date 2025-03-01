<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use Cachable;
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllProducts(Request $request)
    {
        // Define the number of items per page
        $perPage = $request->input('perPage', 50);
        // Get the current page from the request, default to 1 if not provided
        $page = $request->input('page', 1);
        // Retrieve the products with pagination
        $products = Product::paginate($perPage, ['sda_global_number'], 'page', $page);

        $productValues = $products->items();
        $outputString = implode(',', array_map(function ($product) {
            return $product->sda_global_number; // Assuming you want to get the sda_global_number
        }, $productValues));

        // $ids = $outputString;

        return response()->json([
            'data' => $this->GetDataFromView($outputString),
            'total_products' => $products->total(),
            'total_pages' => $products->lastPage(),
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sda_global_number' => 'required|string|max:255|unique:products',
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductById(Request $request)
    {
        $id = $request->id;
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified product in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'sda_global_number' => 'sometimes|required|string|max:255|unique:products,sda_global_number,' . $id,
            'product_name' => 'sometimes|required|string|max:255',
            'unit' => 'sometimes|required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product->update($request->all());
        return response()->json($product);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function getSearchProduct(Request $request)
    {
        // Define the number of items per page
        $perPage = $request->input('perPage', 10);
        // Get the current page from the request, default to 1 if not provided
        $page = $request->input('page', 1);
        // Get the search query from the request
        $query = $request->input('query');

        // Start the query for products
        $productsQuery = Product::query();

        // Apply search query if provided
        if ($query) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('product_name', 'LIKE', "%{$query}%")
                  ->orWhere('sda_global_number', 'LIKE', "%{$query}%");
            });
        }

        // Retrieve the products with pagination
        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

        // Check if products are found
        if ($products->isEmpty()) {
            return response()->json(['data' => [], 'message' => 'No products found'], 404);
        }

        $productValues = $products->items();
        $outputString = implode(',', array_map(function ($product) {
            return $product->sda_global_number; // Assuming you want to get the sda_global_number
        }, $productValues));



        return response()->json([
            'data' => $this->GetDataFromView($outputString),
            'total_products' => $products->total(),
            'total_pages' => $products->lastPage(),
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
        ]);
    }



    function GetDataFromView($ids)
    {
        try {

            $cacheKey = 'live_stock_api_' . $ids;
            // dd($ids);
            return Cache::remember($cacheKey, 3600, function () use ($ids) {

                $url = "https://bridge.tokosda.com/index.php?ids=" . $ids;

                // Send HTTP request
                $response = Http::get($url);

                if ($response->failed()) {
                    Log::error('Failed to fetch data from server', [
                        'url' => $url,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return ['success' => false, 'message' => 'Failed to fetch data from server'];
                }

                $data = $response->json();

                return $data;

            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching stock data', ['exception' => $e->getMessage()]);
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
}
