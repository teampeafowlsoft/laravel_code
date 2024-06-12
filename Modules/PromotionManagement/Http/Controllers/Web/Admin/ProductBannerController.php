<?php

namespace Modules\PromotionManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Product;
use Modules\PromotionManagement\Entities\ProductBanner;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductBannerController extends Controller
{
    private ProductBanner $productbanner;
    private Productcategory $productcategory;
    private Product $product;

    public function __construct(ProductBanner $productbanner, Productcategory $productcategory, Product $product)
    {
        $this->productbanner = $productbanner;
        $this->productcategory = $productcategory;
        $this->product = $product;
    }

    public function create(Request $request): View|Factory|Application
    {
        $search = $request->has('search') ? $request['search'] : '';
        $resource_type = $request->has('resource_type') ? $request['resource_type'] : 'all';
        $query_param = ['search' => $search, 'resource_type' => $resource_type];

        $categories = $this->productcategory->ofStatus(1)->ofType('main')->where('lang_id',1)->latest()->get();
        $products = $this->product->where('lang_id',1)->where('bapprovalst',1)->latest()->get();

        $banners = $this->productbanner->with(['product', 'category'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('banner_title', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('resource_type') && $request['resource_type'] != 'all', function ($query) use ($request) {
                return $query->where(['resource_type' => $request['resource_type']]);
            })->where('lang_id',1)->latest()->paginate(pagination_limit())->appends($query_param);

        $product_data = $this->productbanner->orderBy('group_id', 'desc')->first();
        if (!empty($product_data)) {
            $product_grp_id = $product_data->group_id;
        } else {
            $product_grp_id = 0;
        }

        return view('promotionmanagement::admin.product-banners.create', compact('banners', 'products', 'categories','resource_type','search','product_grp_id'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'banner_title' => 'required',
            'product_id' => 'uuid',
            'category_id' => 'uuid',
            'resource_type' => 'required|in:product,category,link',
            'banner_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        $banner = New ProductBanner();
        $banner->banner_title = $request['banner_title'];
        $banner->redirect_link = $request['redirect_link'];
        $banner->resource_type = $request['resource_type'];
        if ($request['resource_type'] != 'link') {
            $resource_id = $request['resource_type'] == 'product' ? $request['product_id'] : $request['category_id'];
        } else {
            $resource_id = null;
        }
        $banner->resource_id = $resource_id;
        $banner->banner_image = file_uploader('productbanner/', 'png', $request->file('banner_image'));
        $banner->is_active = 1;
        $banner->lang_id = 1;
        $banner->group_id = ($request->group_id) + 1;
        $banner->save();

        if($request['resource_type'] == 'product'){
            $productData = Product::where('id',$resource_id)->first();
            if(!empty($productData)){
                if($productData->lang_id == 1){
                    $productId = Product::where('group_id',$productData->group_id)->where('lang_id',2)->first();
                }
            }
        }else{
            $productCat = Productcategory::where('id',$resource_id)->first();
            if(!empty($productCat)){
                if($productCat->lang_id == 1){
                    $productCatId = Productcategory::where('group_id',$productCat->group_id)->where('lang_id',2)->first();
                }
            }
        }

        $banner_arb = New ProductBanner();
        $banner_arb->banner_title = $request['banner_title'];
        $banner_arb->redirect_link = $request['redirect_link'];
        $banner_arb->resource_type = $request['resource_type'];
        if ($request['resource_type'] != 'link') {
            $resource_id = $request['resource_type'] == 'product' ? $productId->id : $productCatId->id;
        } else {
            $resource_id = null;
        }
        $banner_arb->resource_id = $resource_id;
        $banner_arb->banner_image = file_uploader('productbanner/', 'png', $request->file('banner_image'));
        $banner_arb->is_active = 1;
        $banner_arb->lang_id = 2;
        $banner_arb->group_id = ($request->group_id) + 1;
        $banner_arb->save();

        Toastr::success(BANNER_CREATE_200['message']);
        return back();
    }

    public function edit(string $id): View|Factory|Application
    {
        $banner = $this->productbanner->with(['product', 'category'])->where('id', $id)->first();
        $categories = $this->productcategory->ofStatus(1)->ofType('main')->where('lang_id',1)->latest()->get();
        $products = $this->product->where('lang_id',1)->where('bapprovalst',1)->latest()->get();

        return view('promotionmanagement::admin.product-banners.edit', compact('categories', 'products', 'banner'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
//    public function update(Request $request, $id): RedirectResponse
//    {
//        $request->validate([
//            'banner_title' => 'required',
//            'resource_type' => 'required|in:product,category,link',
//            'product_id' => 'uuid',
//            'category_id' => 'uuid',
//            'banner_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000'
//        ]);
//
//        $banner = $this->productbanner->where(['id' => $id])->first();
//        $banner->banner_title = $request['banner_title'];
//        $banner->redirect_link = $request['redirect_link'];
//        $banner->resource_type = $request['resource_type'];
//        if ($request['resource_type'] != 'link') {
//            $resource_id = $request['resource_type'] == 'product' ? $request['product_id'] : $request['category_id'];
//        } else {
//            $resource_id = null;
//        }
//        $banner->resource_id = $resource_id;
//        $banner->banner_image = file_uploader('productbanner/', 'png', $request->file('banner_image'), $banner->banner_image);
//        $banner->save();
//
//        Toastr::success(BANNER_UPDATE_200['message']);
//        return back();
//    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'banner_title' => 'required',
            'resource_type' => 'required|in:product,category,link',
            'product_id' => 'uuid',
            'category_id' => 'uuid',
            'banner_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        $banner = $this->productbanner->where(['id' => $id])->first();

        $banner->banner_title = $request['banner_title'];
        $banner->redirect_link = $request['redirect_link'];
        $banner->resource_type = $request['resource_type'];
        if ($request['resource_type'] != 'link') {
            $resource_id = $request['resource_type'] == 'product' ? $request['product_id'] : $request['category_id'];
        } else {
            $resource_id = null;
        }
        $banner->resource_id = $resource_id;
        $banner->banner_image = file_uploader('productbanner/', 'png', $request->file('banner_image'), $banner->banner_image);
        $banner->group_id = $request->group_id;
        $banner->save();

//        if(!empty($banner)) {
//            if ($request['resource_type'] == 'product') {
//                $productData = Product::where('id', $banner->resource_id)->first();
//                if (!empty($productData)) {
//                    if ($productData->lang_id == 1) {
//                        $productId = Product::where('group_id',$productData->group_id)->where('lang_id', 2)->first();
//                    }
//                }
//            }
//        }else{
//            $productCat = Productcategory::where('id',$banner->resource_id)->first();
//            if(!empty($productCat)){
//                if($productCat->lang_id == 1){
//                    $productCatId = Productcategory::where('group_id',$productCat->group_id)->where('lang_id',2)->first();
//                }
//            }
//        }
        if($request['resource_type'] == 'product'){
            $productData = Product::where('id',$resource_id)->first();
            if(!empty($productData)){
                if($productData->lang_id == 1){
                    $productId = Product::where('group_id',$productData->group_id)->where('lang_id',2)->first();
                }
            }
        }else{
            $productCat = Productcategory::where('id',$resource_id)->first();
            if(!empty($productCat)){
                if($productCat->lang_id == 1){
                    $productCatId = Productcategory::where('group_id',$productCat->group_id)->where('lang_id',2)->first();
                }
            }
        }
//        $proId = '';
//        if($request['resource_type'] == 'product'){
//            $proId = $productId->id;
//        }else{
//            $proId = $productCatId->id;
//        }
        $getArbicData = $this->productbanner->where('group_id',$request->group_id)->where('lang_id',2)->first();
        $banner1 = $this->productbanner->where(['id' => $getArbicData->id])->first();
//        dd($banner1);
        $banner1->banner_title = $request['banner_title'];
        $banner1->redirect_link = $request['redirect_link'];
        $banner1->resource_type = $request['resource_type'];
        if ($request['resource_type'] != 'link') {
            $resource_id = $request['resource_type'] == 'product' ? $productId->id : $productCatId->id;
        } else {
            $resource_id = null;
        }
        $banner1->resource_id = $resource_id;
        $banner1->banner_image = file_uploader('productbanner/', 'png', $request->file('banner_image'), $banner1->banner_image);
        $banner1->group_id = $request->group_id;
        $banner1->save();

        Toastr::success(BANNER_UPDATE_200['message']);
        return back();
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $banner = $this->productbanner->where('id', $id)->where('lang_id',1)->first();
        $banner1 = $this->productbanner->where('group_id',$banner->group_id)->where('lang_id',2)->first();

        if (isset($banner)){
            file_remover('productbanner/', $banner['banner_image']);
            $this->productbanner->where('id', $id)->delete();
            $this->productbanner->where('id', $banner1->id)->delete();
        }
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }

    public function status_update(Request $request, $id): JsonResponse
    {
        $banner = $this->productbanner->where('id', $id)->where('lang_id',1)->first();
        $banner1 = $this->productbanner->where('group_id',$banner->group_id)->where('lang_id',2)->first();
        $this->productbanner->where('id', $id)->update(['is_active' => !$banner->is_active]);
        $this->productbanner->where('id', $banner1->id)->update(['is_active' => !$banner->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->productbanner->with(['product', 'category'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('banner_title', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('resource_type') && $request['resource_type'] != 'all', function ($query) use ($request) {
                return $query->where(['resource_type' => $request['resource_type']]);
            })->latest()->get();

        return (new FastExcel($items))->download(time().'-file.xlsx');
    }
}
