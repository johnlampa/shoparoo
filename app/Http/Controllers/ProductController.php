<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::query()->with(['images', 'categories']);

        return $this->renderProducts($query, true);
    }

    public function byCategory(Category $category)
    {
        $categories = Category::getAllChildrenByParent($category);

        $query = Product::query()
            ->with(['images', 'categories'])
            ->select('products.*')
            ->join('product_categories AS pc', 'pc.product_id', 'products.id')
            ->whereIn('pc.category_id', array_map(fn($c) => $c->id, $categories));

        return $this->renderProducts($query, false);
    }

    public function view(Product $product)
    {
        $product->load(['images', 'categories']);

        return view('product.view', ['product' => $product]);
    }

    private function renderProducts(Builder $query, bool $isHome = false)
    {
        $search = \request()->get('search');
        $sort = \request()->get('sort', '-updated_at');

        if ($sort) {
            $sortDirection = 'asc';
            if ($sort[0] === '-') {
                $sortDirection = 'desc';
            }
            $sortField = preg_replace('/^-?/', '', $sort);

            $query->orderBy($sortField, $sortDirection);
        }

        $products = $query
            ->where('published', '=', 1)
            ->where(function ($query) use ($search) {
                /** @var $query \Illuminate\Database\Eloquent\Builder */
                $query->where('products.title', 'like', "%$search%")
                    ->orWhere('products.description', 'like', "%$search%");
            })
            ->paginate(24);

        $flashSaleProducts = collect();
        $topCategories = collect();

        if ($isHome && !request()->filled('search')) {
            $flashSaleProducts = Product::query()
                ->with('images')
                ->where('published', true)
                ->whereNotNull('compare_at_price')
                ->whereColumn('compare_at_price', '>', 'price')
                ->orderByRaw('(compare_at_price - price) / compare_at_price DESC')
                ->limit(6)
                ->get();

            $topCategories = Category::query()
                ->where('active', true)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();
        }

        return view('product.index', [
            'products' => $products,
            'isHome' => $isHome && !request()->filled('search'),
            'flashSaleProducts' => $flashSaleProducts,
            'topCategories' => $topCategories,
            'currentCategory' => null,
        ]);
    }
}
