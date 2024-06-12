<?php

namespace Modules\ProductManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\AttributeManagement\Entities\Attribute;
use Modules\AttributeManagement\Entities\Attributevalue;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Feature;
use Modules\ProductManagement\Entities\Product;
use Modules\ProductManagement\Entities\Productvariant;
use Modules\ProductManagement\Entities\Specification;
use Modules\ProductManagement\Entities\Productsubcategory;
use Modules\ProductManagement\Entities\Productsmedia;
use Modules\ProductManagement\Entities\Productshipping;
use Modules\ServiceManagement\Entities\Variation;
use Modules\ZoneManagement\Entities\Zone;
use Modules\ProviderManagement\Entities\Provider;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function __construct(Provider $provider, Productcategory $productcategory, Product $product, Category $category, Zone $zone, Variation $variation, Attribute $attribute, Attributevalue $attributeValue, Productvariant $productvariant)
    {
        $this->product = $product;
        $this->category = $category;
        $this->productcategory = $productcategory;
        $this->zone = $zone;
        $this->variation = $variation;
        $this->attribute = $attribute;
        $this->attributeValue = $attributeValue;
        $this->provider = $provider;
        $this->productvariant = $productvariant;
    }


    public function create(Request $request): View|Factory|Application
    {
        $languages = DB::table('language_master')->get();
        $categories = $this->productcategory->where('lang_id', 1)->ofStatus(1)->ofType('main')->latest()->get();
        $arabic_categories = $this->productcategory->where('lang_id', 2)->ofStatus(1)->ofType('main')->latest()->get();

        $provider = $this->provider->ofStatus(1)->latest()->get();

        $attribute = $this->attribute->where('lang_id', 1)->ofStatus(1)->latest()->get();
        $arabic_attribute = $this->attribute->where('lang_id', 2)->ofStatus(1)->latest()->get();

        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $zones = $this->zone
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
                }
            })
            ->where('lang_id', 1)->ofStatus(1)->latest()->get();

        $arabic_zones = $this->zone
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
                }
            })
            ->where('lang_id', 2)->ofStatus(1)->latest()->get();

        $product_data = Product::orderBy('group_id', 'desc')->first();
        if (!empty($product_data)) {
            $product_grp_id = $product_data->group_id;
        } else {
            $product_grp_id = 0;
        }

//        $variation = $this->productvariant->where(['product_id' => '8d3cb1b3-d611-44dd-b7a3-acf97736b605'])
//            ->where(['id' => 117])
//            ->first();
//
////        $productvariant = $this->product->find('0a01b051-5c72-4173-acd5-9f1c1b8310fa');
//$productvariant = $this->product->find('8d3cb1b3-d611-44dd-b7a3-acf97736b605');
//        $quantity = 200;
//        $campaign_discount = product_campaign_discount_calculation($productvariant, $variation->packate_measurement_sell_price * $quantity);
//        dd($campaign_discount);

//        dd($productvariant->campaign_discount->count());
//        dd($productvariant->campaign_discount[0]->discount);


//        $order_item = DB::table('order_items')
//            ->select('order_items.*' , 'product_variant.product_id','product_subcategory.subcategory_id', 'products.category_id', 'products.vendor')
//            ->join('product_variant', 'product_variant.id', '=', 'order_items.product_variant_id')
//            ->join('products', 'products.id', '=', 'product_variant.product_id')
//            ->leftjoin('product_subcategory', 'product_subcategory.product_id', '=', 'product_variant.product_id')
//            ->where('order_items.user_id', '5abdef6c-2256-4523-9c70-e9c7ff21925f')
////            ->where('order_items.product_variant_id', 172)
//            ->get();
//        dd($order_item);

//        foreach ($order_item->pluck('subcategory_id')->unique() as $sub_category) {
//            dd($sub_category);
//        }


        return view('productmanagement::admin.createnew', compact('categories', 'arabic_categories', 'languages', 'search', 'status', 'zones', 'arabic_zones', 'product_grp_id', 'attribute', 'arabic_attribute', 'provider'));
    }

    public function access_method(Request $request): JsonResponse
    {
        $access = $request->pn;
        $dataArr['data'] = array();
        switch ($access) {
            case 'add_action':
                $dataArr['data'] = $request->ar;
                if (count($dataArr['data'])) {
                    session()->put('feature', $dataArr['data']);
                } else
                    session()->put('feature', null);
                return response()->json($dataArr['data']);
                break;
            case 'del_action':
//                $dataArr['data'] = $request->ar;
//                if(count($dataArr['data'])){
//                    if($dataArr['data'][$_POST['id']]['Action'] != "Add"){
//                        $id = $dataArr['data'][$_POST['id']]['Action'];
//                        Post::where('id', 1)->delete();
//                        $sql_query = "DELETE FROM product_features
//					WHERE id =" . $id;
//                        $db->sql($sql_query);
//                        $sts = $db->getResult();
//                    }
//                    unset($dataArr['data'][$_POST['id']]);
//                    $dataArr = array_values($dataArr['data']);
//                    session()->put('feature', $dataArr['data']);
//                }
//                else
//                    session()->put('feature', null);
                if (count($dataArr['data']))
                    return response()->json($dataArr['data']);

//                echo json_encode($dataArr);
                else
                    echo "";
                break;
            case 'add_action_arabic':
                $dataArr['data'] = $request->ar;
                if (count($dataArr['data'])) {
                    session()->put('feature_arabic', $dataArr['data']);
                } else
                    session()->put('feature_arabic', null);
                return response()->json($dataArr['data']);
                break;
            case 'add_specific_action':
                $dataArr['data'] = $request->sp;
                if (count($dataArr['data'])) {
                    session()->put('specific', $dataArr['data']);
                } else
                    session()->put('specific', null);
                return response()->json($dataArr['data']);
                break;
//            case 'del_specific_action':
//                $dataArr['data'] = $request->sp;
//                if (count($dataArr['data'])) {
//                    if ($dataArr['data'][$_POST['id']]['Action'] != "Add") {
//                        $id = $dataArr['data'][$_POST['id']]['Action'];
//                        $sql_query = "DELETE FROM product_specification
//					WHERE id =" . $id;
//                        $db->sql($sql_query);
//                        $sts = $db->getResult();
//                    }
//                    unset($dataArr['data'][$_POST['id']]);
//                    $dataArr = array_values($dataArr['data']);
//                    session()->put('specific', $dataArr['data']);
//                } else
//                    session()->put('specific', null);
//                if (count($dataArr))
//                    echo json_encode($dataArr);
//                else
//                    echo "";
//                break;
            case 'add_specific_action_arabic':
                $dataArr['data'] = $request->sp;
                if (count($dataArr['data'])) {
                    session()->put('specific_arabic', $dataArr['data']);
                } else
                    session()->put('specific_arabic', null);
                return response()->json($dataArr['data']);
                break;
            default:
                break;
        }
    }

    public function index(Request $request): View|Factory|Application
    {

        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

            $products = Product::select('products.group_id','products.is_active','providers.company_name',DB::raw('GROUP_CONCAT(products.name order By products.lang_id) as name'),DB::raw('GROUP_CONCAT(products.id order By products.lang_id) as id'),DB::raw('GROUP_CONCAT(products.lang_id) as lang_id'),DB::raw('GROUP_CONCAT(products.image order By products.lang_id) as image'),DB::raw('GROUP_CONCAT(sku order By lang_id) as sku'))
            ->join('providers', 'providers.id', '=', 'products.vendor')
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
                    $query->orHavingRaw('sku LIKE ?', array("%$key%"));
                }
            })
            ->groupBy('products.group_id', 'products.is_active')
            ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
                if ($request['status'] == 'active') {
                    return $query->where(['products.is_active' => 1]);
                } else {
                    return $query->where(['products.is_active' => 0]);
                }
            })->orderBy('products.created_at', 'desc')->paginate(pagination_limit())->appends($query_param);
//            dd($products);
//        $products = $this->product->selectRaw("group_id,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id order By lang_id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(image order By lang_id) as image,GROUP_CONCAT(sku order By lang_id) as sku")
//            ->when($request->has('search'), function ($query) use ($request) {
//                $keys = explode(' ', $request['search']);
//                foreach ($keys as $key) {
//                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
//                    $query->orHavingRaw('sku LIKE ?', array("%$key%"));
//                }
//            })
//            ->groupBy('group_id', 'is_active')
//            ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
//                if ($request['status'] == 'active') {
//                    return $query->where(['is_active' => 1]);
//                } else {
//                    return $query->where(['is_active' => 0]);
//                }
//            })->paginate(pagination_limit())->appends($query_param);

        $products_qty = Product::select('product_variant.group_id', 'product_variant.product_id', DB::raw('SUM(packate_measurement_qty) As packate_measurement_qty'))
            ->join('product_variant', 'product_variant.product_id', '=', 'products.id')
            ->where('products.lang_id', 1)
            ->groupBy('product_variant.group_id', 'product_variant.product_id')
            ->get();

        return view('productmanagement::admin.list', compact('products', 'search', 'status', 'products_qty'));
    }

    public function pendinglist(Request $request): View|Factory|Application
    {
        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];


        $products = $this->product->selectRaw("group_id,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id order By lang_id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(image order By lang_id) as image,GROUP_CONCAT(sku order By lang_id) as sku,approve_status,bapprovalst")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
                    $query->orHavingRaw('sku LIKE ?', array("%$key%"));
                }
            })
            ->groupBy('group_id', 'is_active')
            ->where('bapprovalst', 2)
            ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
                if ($request['status'] == 'active') {
                    return $query->where(['is_active' => 1]);
                } else {
                    return $query->where(['is_active' => 0]);
                }
            })->paginate(pagination_limit())->appends($query_param);
//dd($products);
        $products_qty = Product::select('product_variant.group_id', 'product_variant.product_id', DB::raw('SUM(packate_measurement_qty) As packate_measurement_qty'))
            ->join('product_variant', 'product_variant.product_id', '=', 'products.id')
            ->where('products.lang_id', 1)
            ->groupBy('product_variant.group_id', 'product_variant.product_id')
            ->get();
        //dd($products->toArray());
        return view('productmanagement::admin.pendingproduct', compact('products', 'search', 'status', 'products_qty'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request): RedirectResponse
    {
        $product_validate = $request->validate([
            'name' => 'required',
            'arabic_name' => 'required',
            'category_id' => 'required|uuid',
            'arabic_category_id' => 'required',
            'description' => 'required',
            'arabic_description' => 'required',
            'sku' => 'required',
            'sku_arabic' => 'required',
            'indicator' => 'required',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        if($product_validate) {
            $product = new Product();
            $product->group_id = ($request->group_id) + 1;//
            $product->lang_id = $request->eng_lang_id;//
            $product->name = $request->name;//
            $product->category_id = $request->category_id;
            $product->description = $request->description;
            $product->indicator = $request->indicator;
            $product->sku = $request->sku;
            $product->tags = !empty($request->tags) ? implode(',', $request->tags) : null;
            $product->vendor = !empty($request->vendor) ? $request->vendor : null;
            $product->made_in = !empty($request->made_in) ? $request->made_in : null;
            $product->manufacturer = !empty($request->manufacturer) ? $request->manufacturer : null;
            $product->manufacturer_part_no = !empty($request->manufacturer_part_no) ? $request->manufacturer_part_no : null;
            $product->brand_ids = !empty($request->brand_ids) ? $request->brand_ids : null;
            $product->weight = !empty($request->weight) ? $request->weight : 0;
            $product->length = !empty($request->length) ? $request->length : 0;
            $product->width = !empty($request->width) ? $request->width : 0;
            $product->height = !empty($request->height) ? $request->height : 0;
            $product->return_status = !empty($request->return_status) ? $request->return_status : 0;
            $product->promo_status = !empty($request->promo_status) ? $request->promo_status : 0;
            $product->cancelable_status = !empty($request->cancelable_status) ? $request->cancelable_status : 0;
            $product->till_status = !empty($request->till_status) ? $request->till_status : null;
//        $product->bstatus = $request->bstatus;
            $product->bstatus = 1;
            $product->videoURL = !empty($request->videoURL) ? $request->videoURL : null;
            $product->brochure = file_uploader('product/', 'pdf', $request->file('brochure'));
            $product->seoPageNm = !empty($request->seoPageNm) ? $request->seoPageNm : null;
            $product->sMetaTitle = !empty($request->sMetaTitle) ? $request->sMetaTitle : null;
            $product->sMetaKeywords = !empty($request->sMetaKeywords) ? $request->sMetaKeywords : null;
            $product->sMetaDescription = !empty($request->sMetaDescription) ? $request->sMetaDescription : null;
            $product->approve_status = 1;
            $product->image = file_uploader('product/', 'png', $request->file('image'));
            $product->save();

            //Last Insert ID of product
            $last_id = $product->id;
            $group_id = $product->group_id;

//            if ($request->hasfile('other_images')) {
            if ($request->file('other_images')) {
                foreach ($request->file('other_images') as $key => $file) {
//                $path = $file->store('public/product');
                    $path = file_uploader('product/', 'png', $request->file('other_images')[$key]);
                    $name = $file->getClientOriginalName();

                    $insert[$key]['product_id'] = $last_id;
                    $insert[$key]['group_id'] = $group_id;
                    $insert[$key]['other_images'] = $path;
                }
                DB::table('products_media')->insert($insert);
            }

            if (!empty($request->sub_category_id)) {
                $product['sub_category_id'] = implode(',', $request->sub_category_id);
                $sub_cat_explode = explode(',', $product['sub_category_id']);
                foreach ($sub_cat_explode as $sub_id) {
                    $product_sub_cat[] = [
                        'product_id' => $last_id,
                        'group_id' => $group_id,
                        'subcategory_id' => $sub_id,
                    ];
                }
                DB::table('product_subcategory')->insert($product_sub_cat);
            }
            $product_variant = [];
            $product->packate_measurement_attribute_id = $request->packate_measurement_attribute_id;

            for ($i = 0; $i < count($request->packate_measurement_attribute_id); $i++) {
                $product_variant[] = [
                    'product_id' => $last_id,
                    'group_id' => $group_id,
                    'packate_measurement_attribute_id' => $request->packate_measurement_attribute_id[$i],
                    'packate_measurement_attribute_value' => $request->packate_measurement_attribute_value[$i],
                    'packate_measurement_sell_price' => $request->packate_measurement_sell_price[$i],
                    'packate_measurement_cost_price' => $request->packate_measurement_cost_price[$i],
                    'packate_measurement_discount_price' => $request->packate_measurement_discount_price[$i],
                    'packate_measurement_shelf_life_unit' => !empty($request->packate_measurement_shelf_life_unit[$i]) ? $request->packate_measurement_shelf_life_unit[$i] : null,
                    'packate_measurement_shelf_life_val' => $request->packate_measurement_shelf_life_val[$i] != 0 ? $request->packate_measurement_shelf_life_val[$i] : null,
                    'packate_measurement_barcode' => $request->packate_measurement_barcode[$i] != 0 ? $request->packate_measurement_barcode[$i] : null,
                    'packate_measurement_fssai_number' => $request->packate_measurement_fssai_number[$i] != 0 ? $request->packate_measurement_fssai_number[$i] : null,
                    'packate_measurement_qty' => $request->packate_measurement_qty[$i] != 0 ? $request->packate_measurement_qty[$i] : 0,
                    'packate_measurement_images' => !empty($request->file('packate_measurement_images')[$i]) ? file_uploader('product/', 'png', $request->file('packate_measurement_images')[$i]) : null
                ];
            }
            DB::table('product_variant')->insert($product_variant);

            // PRODUCT FEATURES AND SPECIFICATION DATA
            $product_feature = [];
            if (session()->has('feature')) {
                if (session()->has('feature') != null) {
                    foreach (session('feature') as $item) {
                        $product_feature[] = [
                            'product_id' => $last_id,
                            'group_id' => $group_id,
                            'features_name' => $item['sKeyFeatures'],
                            'features_status' => 1,
                        ];
                    }
                }
                DB::table('product_features')->insert($product_feature);

//            $product->features()->createMany($product_feature);
                session()->forget('feature');
            }

            $product_specification = [];
            if (session()->has('specific')) {
                if (session()->has('specific') != null) {
                    foreach (session('specific') as $specificitem) {
                        $product_specification[] = [
                            'product_id' => $last_id,
                            'group_id' => $group_id,
                            'specification_type' => $specificitem['specification_type'],
                            'specification_name' => $specificitem['specification_name'],
                            'specification_status' => 1,
                        ];
                    }
                }
                DB::table('product_specification')->insert($product_specification);

//            $product->specifications()->createMany($product_specification);
                session()->forget('specific');
            }

//      Product Shipping Data
            if ($request->delivery_charge) {
                $product_shipping = [];
                for ($i = 1; $i <= $request->total_zone; $i++) {
                    $product_shipping[] = [
                        'product_id' => $last_id,
                        'group_id' => $group_id,
                        'delivery_charge' => $request->delivery_charge[$i],
                        'zone_id' => $request->zone_id[$i],
                    ];
                }
                DB::table('product_shipping')->insert($product_shipping);
            }

            //Arabic data
            $product1 = new Product();
            $product1->group_id = ($request->group_id) + 1;
            $product1->lang_id = $request->arabic_lang_id;
            $product1->name = $request->arabic_name;
            $product1->category_id = $request->arabic_category_id;
            $product1->description = $request->arabic_description;
            $product1->indicator = $request->indicator_arabic;
            $product1->sku = $request->sku_arabic;
            $product1->tags = !empty($request->tags_arabic) ? implode(',', $request->tags_arabic) : null;
            $product1->vendor = !empty($request->vendor_arabic) ? $request->vendor_arabic : null;
            $product1->made_in = !empty($request->made_in_arabic) ? $request->made_in_arabic : null;
            $product1->manufacturer = !empty($request->manufacturer_arabic) ? $request->manufacturer_arabic : null;
            $product1->manufacturer_part_no = !empty($request->manufacturer_part_no_arabic) ? $request->manufacturer_part_no_arabic : null;
            $product1->brand_ids = !empty($request->brand_ids_arabic) ? $request->brand_ids_arabic : null;
            $product1->weight = !empty($request->weight_arabic) ? $request->weight_arabic : 0;
            $product1->length = !empty($request->length_arabic) ? $request->length_arabic : 0;
            $product1->width = !empty($request->width_arabic) ? $request->width_arabic : 0;
            $product1->height = !empty($request->height_arabic) ? $request->height_arabic : 0;
            $product1->return_status = !empty($request->return_status_arabic) ? $request->return_status_arabic : 0;
            $product1->promo_status = !empty($request->promo_status_arabic) ? $request->promo_status_arabic : 0;
            $product1->cancelable_status = !empty($request->cancelable_status_arabic) ? $request->cancelable_status_arabic : 0;
            $product1->till_status = !empty($request->till_status_arabic) ? $request->till_status_arabic : null;
            $product1->bstatus = 1;
            $product1->videoURL = !empty($request->videoURL_arabic) ? $request->videoURL_arabic : null;
            $product1->brochure = file_uploader('product/', 'pdf', $request->file('brochure_arabic'));
            $product1->seoPageNm = !empty($request->arabic_seoPageNm) ? $request->arabic_seoPageNm : null;
            $product1->sMetaTitle = !empty($request->arabic_sMetaTitle) ? $request->arabic_sMetaTitle : null;
            $product1->sMetaKeywords = !empty($request->arabic_sMetaKeywords) ? $request->arabic_sMetaKeywords : null;
            $product1->sMetaDescription = !empty($request->arabic_sMetaDescription) ? $request->arabic_sMetaDescription : null;
            $product1->approve_status = 1;
            $product1->image = file_uploader('product/', 'png', $request->file('arabic_image'));

            $product1->save();

            //Last Insert ID of product
            $last_id1 = $product1->id;
            $group_id1 = $product1->group_id;

//            if ($request->hasfile('other_images_arabic')) {
            if ($request->file('other_images_arabic')) {
                foreach ($request->file('other_images_arabic') as $key => $file) {
//                $path = $file->store('public/product');
                    $name = $file->getClientOriginalName();
                    $path = file_uploader('product/', 'png', $request->file('other_images_arabic')[$key]);

                    $insert1[$key]['product_id'] = $last_id1;
                    $insert1[$key]['group_id'] = $group_id1;
                    $insert1[$key]['other_images'] = $path;
                }
                DB::table('products_media')->insert($insert1);
            }


            if (!empty($request->arabic_sub_category_id)) {
                $product_sub_cat1 = [];
                $product1['arabic_sub_category_id'] = implode(',', $request->arabic_sub_category_id);
                $sub_cat_explode1 = explode(',', $product1['arabic_sub_category_id']);
                foreach ($sub_cat_explode1 as $sub_id) {
                    $product_sub_cat1[] = [
                        'product_id' => $last_id1,
                        'group_id' => $group_id1,
                        'subcategory_id' => $sub_id,
                    ];
                }
                DB::table('product_subcategory')->insert($product_sub_cat1);
            }

            $product_variant1 = [];
            $product1->packate_measurement_attribute_id = $request->arabic_packate_measurement_attribute_id;

            for ($i = 0; $i < count($request->arabic_packate_measurement_attribute_id); $i++) {
                $product_variant1[] = [
                    'product_id' => $last_id1,
                    'group_id' => $group_id1,
                    'packate_measurement_attribute_id' => $request->arabic_packate_measurement_attribute_id[$i],
                    'packate_measurement_attribute_value' => $request->arabic_packate_measurement_attribute_value[$i],
                    'packate_measurement_sell_price' => $request->arabic_packate_measurement_sell_price[$i],
                    'packate_measurement_cost_price' => $request->arabic_packate_measurement_cost_price[$i],
                    'packate_measurement_discount_price' => $request->arabic_packate_measurement_discount_price[$i],
                    'packate_measurement_shelf_life_unit' => !empty($request->arabic_packate_measurement_shelf_life_unit[$i]) ? $request->arabic_packate_measurement_shelf_life_unit[$i] : null,
                    'packate_measurement_shelf_life_val' => $request->arabic_packate_measurement_shelf_life_val[$i] != 0 ? $request->arabic_packate_measurement_shelf_life_val[$i] : null,
                    'packate_measurement_barcode' => $request->arabic_packate_measurement_barcode[$i] != 0 ? $request->arabic_packate_measurement_barcode[$i] : null,
                    'packate_measurement_fssai_number' => $request->arabic_packate_measurement_fssai_number[$i] != 0 ? $request->arabic_packate_measurement_fssai_number[$i] : null,
                    'packate_measurement_qty' => $request->arabic_packate_measurement_qty[$i] != 0 ? $request->arabic_packate_measurement_qty[$i] : 0,
                    'packate_measurement_images' => !empty($request->file('arabic_packate_measurement_images')[$i]) ? file_uploader('product/', 'png', $request->file('arabic_packate_measurement_images')[$i]) : null
                ];
            }
            DB::table('product_variant')->insert($product_variant1);

            // PRODUCT FEATURES AND SPECIFICATION DATA
            $product_feature1 = [];
            if (session()->has('feature_arabic')) {
                if (session()->has('feature_arabic') != null) {
                    foreach (session('feature_arabic') as $item) {
                        $product_feature1[] = [
                            'product_id' => $last_id1,
                            'group_id' => $group_id1,
                            'features_name' => $item['sKeyFeatures_arabic'],
                            'features_status' => 1,
                        ];
                    }
                }
                DB::table('product_features')->insert($product_feature1);

//            $product->features()->createMany($product_feature);
                session()->forget('feature');
            }

            $product_specification1 = [];
            if (session()->has('specific_arabic')) {
                if (session()->has('specific_arabic') != null) {
                    foreach (session('specific_arabic') as $specificitem) {
                        $product_specification1[] = [
                            'product_id' => $last_id1,
                            'group_id' => $group_id1,
                            'specification_type' => $specificitem['specification_type_arabic'],
                            'specification_name' => $specificitem['specification_name_arabic'],
                            'specification_status' => 1,
                        ];
                    }
                }
                DB::table('product_specification')->insert($product_specification1);
                session()->forget('specific');
            }

            // Product Shipping Data Arabic
            if ($request->arabic_delivery_charge) {
                $product_shipping1 = [];
                for ($i = 1; $i <= $request->arabic_total_zone; $i++) {
                    $product_shipping1[] = [
                        'product_id' => $last_id1,
                        'group_id' => $group_id1,
                        'delivery_charge' => $request->arabic_delivery_charge[$i],
                        'zone_id' => $request->arabic_zone_id[$i],
                    ];
                }
                DB::table('product_shipping')->insert($product_shipping1);
            }

            Toastr::success(PRODUCT_STORE_200['message']);
            return back();
        } else {
            Toastr::error(DEFAULT_204['message']);
            return back();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('productmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(string $id, int $group_id): View|Factory|RedirectResponse|Application
    {
        $languages = DB::table('language_master')->get();
        $categories = $this->productcategory->where('lang_id', 1)->ofStatus(1)->ofType('main')->latest()->get();
        $arabic_categories = $this->productcategory->where('lang_id', 2)->ofStatus(1)->ofType('main')->latest()->get();
        $zones = $this->zone->where('lang_id', 1)->ofStatus(1)->latest()->get();

        $arabic_zones = $this->zone->where('lang_id', 2)->ofStatus(1)->latest()->get();
        $provider = $this->provider->ofStatus(1)->latest()->get();

        $sub_categories = $this->productcategory->ofStatus(1)->ofType('sub')->where('lang_id', 1)->latest()->get();
        $sub_categories_arabic = $this->productcategory->ofStatus(1)->ofType('sub')->where('lang_id', 2)->latest()->get();


        $attribute = $this->attribute->where('lang_id', 1)->ofStatus(1)->latest()->get();
        $arabic_attribute = $this->attribute->where('lang_id', 2)->ofStatus(1)->latest()->get();

        $attribute_value = $this->attributeValue->where('lang_id', 1)->ofStatus(1)->latest()->get();
        $arabic_attribute_value = $this->attributeValue->where('lang_id', 2)->ofStatus(1)->latest()->get();

//        $products = $this->product->selectRaw("group_id,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id order By lang_id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(image order By lang_id) as image,GROUP_CONCAT(description order By lang_id) as description,GROUP_CONCAT(category_id order By lang_id) as category_id,GROUP_CONCAT(indicator order By lang_id) as indicator,GROUP_CONCAT(sku order By lang_id) as sku,GROUP_CONCAT(tags order By lang_id) as tags,GROUP_CONCAT(vendor order By lang_id) as vendor,GROUP_CONCAT(made_in order By lang_id) as made_in,GROUP_CONCAT(manufacturer order By lang_id) as manufacturer,GROUP_CONCAT(manufacturer order By lang_id) as manufacturer_part_no,GROUP_CONCAT(brand_ids order By lang_id) as brand_ids,GROUP_CONCAT(brand_ids order By lang_id) as brand_ids,GROUP_CONCAT(weight order By lang_id) as weight,GROUP_CONCAT(length order By lang_id) as length,GROUP_CONCAT(width order By lang_id) as width,GROUP_CONCAT(height order By lang_id) as height,GROUP_CONCAT(return_status order By lang_id) as return_status,GROUP_CONCAT(promo_status order By lang_id) as promo_status,GROUP_CONCAT(cancelable_status order By lang_id) as cancelable_status,GROUP_CONCAT(till_status order By lang_id) as till_status,GROUP_CONCAT(bstatus order By lang_id) as bstatus,GROUP_CONCAT(videoURL order By lang_id) as videoURL,GROUP_CONCAT(brochure order By lang_id) as brochure,GROUP_CONCAT(seoPageNm order By lang_id) as seoPageNm,GROUP_CONCAT(sMetaTitle order By lang_id) as sMetaTitle,GROUP_CONCAT(sMetaKeywords order By lang_id) as sMetaKeywords,GROUP_CONCAT(sMetaDescription order By lang_id) as sMetaDescription")
//            ->where('group_id', $group_id)
//            ->groupBy('group_id', 'is_active')
//            ->get();

//        $products_sub_cat = DB::table('product_subcategory')->selectRaw("group_id,GROUP_CONCAT(product_id) as product_id,GROUP_CONCAT(subcategory_id) as subcategory_id")
//            ->where('group_id', $group_id)
//            ->groupBy('group_id')
//            ->get();

        $products = $this->product->selectRaw("group_id,is_active,name,id,lang_id,image,description,category_id,indicator,sku,tags,vendor,made_in,manufacturer,manufacturer_part_no,brand_ids,weight,length,width,height,return_status,promo_status,cancelable_status,till_status,bstatus,videoURL,brochure,seoPageNm,sMetaTitle,sMetaKeywords,sMetaDescription")
            ->where('group_id', $group_id)
            ->orderBy('lang_id', 'asc')
            ->get();

        $products_sub_cat = DB::table('product_subcategory')->selectRaw("product_subcategory.group_id,product_subcategory.product_id,product_subcategory.subcategory_id")
            ->where('product_subcategory.group_id', $group_id)
            ->join('productcategories', function($join){
                $join->on('productcategories.id', '=', 'product_subcategory.subcategory_id')
                    ->where('productcategories.lang_id', '=', 1);
            })
            ->get();
        $products_sub_cat_arb = DB::table('product_subcategory')->selectRaw("product_subcategory.group_id,product_subcategory.product_id,product_subcategory.subcategory_id")
            ->where('product_subcategory.group_id', $group_id)
            ->join('productcategories', function($join){
                $join->on('productcategories.id', '=', 'product_subcategory.subcategory_id')
                    ->where('productcategories.lang_id', '=', 2);
            })
            ->get();
//
//        dd($sub_categories_arabic,$products_sub_cat);
//        DB::enableQueryLog();
        $products_variant = DB::table('product_variant')->selectRaw("product_variant.group_id,product_id,packate_measurement_attribute_id,packate_measurement_attribute_value, packate_measurement_sell_price,packate_measurement_cost_price, packate_measurement_discount_price, packate_measurement_shelf_life_unit, packate_measurement_shelf_life_val,packate_measurement_barcode, packate_measurement_fssai_number,packate_measurement_qty,packate_measurement_images")
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->where('product_variant.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_variant_arabic = DB::table('product_variant')->selectRaw("product_variant.group_id,product_id,packate_measurement_attribute_id,packate_measurement_attribute_value, packate_measurement_sell_price,packate_measurement_cost_price, packate_measurement_discount_price, packate_measurement_shelf_life_unit, packate_measurement_shelf_life_val,packate_measurement_barcode, packate_measurement_fssai_number,packate_measurement_qty,packate_measurement_images")
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->where('product_variant.group_id', $group_id)
            ->where('products.lang_id', 2)
//            ->groupBy('product_variant.group_id')
            ->get();

        $products_other_images = DB::table('products_media')->selectRaw("products_media.group_id,product_id,other_images")
            ->join('products', 'products.id', '=', 'products_media.product_id')
            ->where('products_media.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_other_images_arabic = DB::table('products_media')->selectRaw("products_media.group_id,product_id,other_images")
            ->join('products', 'products.id', '=', 'products_media.product_id')
            ->where('products_media.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        $products_features = DB::table('product_features')->selectRaw("product_features.id,product_features.group_id,product_id,features_name")
            ->join('products', 'products.id', '=', 'product_features.product_id')
            ->where('product_features.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_features_arabic = DB::table('product_features')->selectRaw("product_features.id,product_features.group_id,product_id,features_name")
            ->join('products', 'products.id', '=', 'product_features.product_id')
            ->where('product_features.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        $products_specification = DB::table('product_specification')->selectRaw("product_specification.id,product_specification.group_id,product_id,specification_type,specification_name")
            ->join('products', 'products.id', '=', 'product_specification.product_id')
            ->where('product_specification.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_specification_arabic = DB::table('product_specification')->selectRaw("product_specification.id,product_specification.group_id,product_id,specification_type,specification_name")
            ->join('products', 'products.id', '=', 'product_specification.product_id')
            ->where('product_specification.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        $products_shipping = DB::table('product_shipping')->selectRaw("product_shipping.id,product_shipping.group_id,product_id,zone_id,delivery_charge")
            ->join('products', 'products.id', '=', 'product_shipping.product_id')
            ->where('product_shipping.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_shipping_arabic = DB::table('product_shipping')->selectRaw("product_shipping.id,product_shipping.group_id,product_id,zone_id,delivery_charge")
            ->join('products', 'products.id', '=', 'product_shipping.product_id')
            ->where('product_shipping.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        return view('productmanagement::admin.edit', compact('categories', 'arabic_categories', 'languages', 'zones', 'arabic_zones', 'products', 'sub_categories', 'sub_categories_arabic', 'products_sub_cat', 'products_variant', 'products_other_images', 'products_features', 'products_specification', 'products_shipping', 'products_variant_arabic', 'products_features_arabic', 'products_specification_arabic', 'products_other_images_arabic', 'products_shipping_arabic', 'attribute', 'arabic_attribute', 'attribute_value', 'arabic_attribute_value', 'provider','products_sub_cat_arb'));
//        }
    }

    public function view(string $id, int $group_id): View|Factory|RedirectResponse|Application
    {
        $languages = DB::table('language_master')->get();
        $categories = $this->productcategory->where('lang_id', 1)->ofStatus(1)->ofType('main')->latest()->get();
        $arabic_categories = $this->productcategory->where('lang_id', 2)->ofStatus(1)->ofType('main')->latest()->get();
        $zones = $this->zone->where('lang_id', 1)->ofStatus(1)->latest()->get();

        $arabic_zones = $this->zone->where('lang_id', 2)->ofStatus(1)->latest()->get();
        $provider = $this->provider->ofStatus(1)->latest()->get();

        $sub_categories = $this->productcategory->ofStatus(1)->ofType('sub')->where('lang_id', 1)->latest()->get();
        $sub_categories_arabic = $this->productcategory->ofStatus(1)->ofType('sub')->where('lang_id', 2)->latest()->get();

        $attribute = $this->attribute->where('lang_id', 1)->ofStatus(1)->latest()->get();
        $arabic_attribute = $this->attribute->where('lang_id', 2)->ofStatus(1)->latest()->get();

        $attribute_value = $this->attributeValue->where('lang_id', 1)->ofStatus(1)->latest()->get();
        $arabic_attribute_value = $this->attributeValue->where('lang_id', 2)->ofStatus(1)->latest()->get();

        $products = $this->product->selectRaw("group_id,is_active,name,id,lang_id,image,description,category_id,indicator,sku,tags,vendor,made_in,manufacturer,manufacturer_part_no,brand_ids,weight,length,width,height,return_status,promo_status,cancelable_status,till_status,bstatus,videoURL,brochure,seoPageNm,sMetaTitle,sMetaKeywords,sMetaDescription,bapprovalst,approve_status,published_status,show_home_page_status,review_status,availableStartDt,availableEndDt,mark_as_new_status,topseller_status,indemand_status,approvalDt,block_product_status,block_comment,adminComment")
            ->where('group_id', $group_id)
            ->orderBy('lang_id', 'asc')
            ->get();

        $products_sub_cat = DB::table('product_subcategory')->selectRaw("group_id,product_id,subcategory_id")
            ->where('group_id', $group_id)
            ->groupBy('group_id')
            ->get();

//        DB::enableQueryLog();
        $products_variant = DB::table('product_variant')->selectRaw("product_variant.group_id,product_id,packate_measurement_attribute_id,packate_measurement_attribute_value, packate_measurement_sell_price,packate_measurement_cost_price, packate_measurement_discount_price, packate_measurement_shelf_life_unit, packate_measurement_shelf_life_val,packate_measurement_barcode, packate_measurement_fssai_number,packate_measurement_qty,packate_measurement_images")
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->where('product_variant.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_variant_arabic = DB::table('product_variant')->selectRaw("product_variant.group_id,product_id,packate_measurement_attribute_id,packate_measurement_attribute_value, packate_measurement_sell_price,packate_measurement_cost_price, packate_measurement_discount_price, packate_measurement_shelf_life_unit, packate_measurement_shelf_life_val,packate_measurement_barcode, packate_measurement_fssai_number,packate_measurement_qty,packate_measurement_images")
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->where('product_variant.group_id', $group_id)
            ->where('products.lang_id', 2)
//            ->groupBy('product_variant.group_id')
            ->get();
//dd($products_variant_arabic->toArray());
        $products_other_images = DB::table('products_media')->selectRaw("products_media.group_id,product_id,other_images")
            ->join('products', 'products.id', '=', 'products_media.product_id')
            ->where('products_media.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_other_images_arabic = DB::table('products_media')->selectRaw("products_media.group_id,product_id,other_images")
            ->join('products', 'products.id', '=', 'products_media.product_id')
            ->where('products_media.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        $products_features = DB::table('product_features')->selectRaw("product_features.id,product_features.group_id,product_id,features_name")
            ->join('products', 'products.id', '=', 'product_features.product_id')
            ->where('product_features.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_features_arabic = DB::table('product_features')->selectRaw("product_features.id,product_features.group_id,product_id,features_name")
            ->join('products', 'products.id', '=', 'product_features.product_id')
            ->where('product_features.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        $products_specification = DB::table('product_specification')->selectRaw("product_specification.id,product_specification.group_id,product_id,specification_type,specification_name")
            ->join('products', 'products.id', '=', 'product_specification.product_id')
            ->where('product_specification.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_specification_arabic = DB::table('product_specification')->selectRaw("product_specification.id,product_specification.group_id,product_id,specification_type,specification_name")
            ->join('products', 'products.id', '=', 'product_specification.product_id')
            ->where('product_specification.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        $products_shipping = DB::table('product_shipping')->selectRaw("product_shipping.id,product_shipping.group_id,product_id,zone_id,delivery_charge")
            ->join('products', 'products.id', '=', 'product_shipping.product_id')
            ->where('product_shipping.group_id', $group_id)
            ->where('products.lang_id', 1)
            ->get();

        $products_shipping_arabic = DB::table('product_shipping')->selectRaw("product_shipping.id,product_shipping.group_id,product_id,zone_id,delivery_charge")
            ->join('products', 'products.id', '=', 'product_shipping.product_id')
            ->where('product_shipping.group_id', $group_id)
            ->where('products.lang_id', 2)
            ->get();

        return view('productmanagement::admin.view', compact('categories', 'arabic_categories', 'languages', 'zones', 'arabic_zones', 'products', 'sub_categories', 'sub_categories_arabic', 'products_sub_cat', 'products_variant', 'products_other_images', 'products_features', 'products_specification', 'products_shipping', 'products_variant_arabic', 'products_features_arabic', 'products_specification_arabic', 'products_other_images_arabic', 'products_shipping_arabic', 'attribute', 'arabic_attribute', 'attribute_value', 'arabic_attribute_value', 'provider'));
//        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {

        $products_variant = DB::table('product_variant')->select("packate_measurement_images")
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->where('product_variant.group_id', $id)
            ->where('products.lang_id', 1)
            ->get();
        $products_variant_arabic = DB::table('product_variant')->selectRaw("packate_measurement_images")
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->where('product_variant.group_id', $id)
            ->where('products.lang_id', 2)
//            ->groupBy('product_variant.group_id')
            ->get();

        $request->validate([
            'name' => 'required',
            'category_id' => 'required|uuid',
            'description' => 'required',
            'indicator' => 'required',
        ]);

        $product = $this->product->where('group_id', $id)->where('lang_id', 1)->first();
        $product1 = $this->product->where('group_id', $id)->where('lang_id', 2)->first();

        if (!isset($product)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $product->group_id = $request->group_id;
        $product->lang_id = $request->eng_lang_id;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->description = $request->description;
        $product->indicator = $request->indicator;
        $product->sku = !empty($request->sku) ? $request->sku : null;
        $product->tags = !empty($request->tags) ? implode(',', $request->tags) : null;
        $product->vendor = !empty($request->vendor) ? $request->vendor : null;
        $product->made_in = !empty($request->made_in) ? $request->made_in : null;
        $product->manufacturer = !empty($request->manufacturer) ? $request->manufacturer : null;
        $product->manufacturer_part_no = !empty($request->manufacturer_part_no) ? $request->manufacturer_part_no : null;
        $product->brand_ids = !empty($request->brand_ids) ? $request->brand_ids : null;
        $product->weight = !empty($request->weight) ? $request->weight : 0;
        $product->length = !empty($request->length) ? $request->length : 0;
        $product->width = !empty($request->width) ? $request->width : 0;
        $product->height = !empty($request->height) ? $request->height : 0;
        $product->return_status = !empty($request->return_status) ? $request->return_status : 0;
        $product->promo_status = !empty($request->promo_status) ? $request->promo_status : 0;
        $product->cancelable_status = !empty($request->cancelable_status) ? $request->cancelable_status : 0;
        $product->till_status = !empty($request->till_status) ? $request->till_status : null;
//        $product->bstatus = $request->bstatus;
        $product->bstatus = 1;
        $product->videoURL = !empty($request->videoURL) ? $request->videoURL : null;
        if ($request->has('brochure')) {
            $product->brochure = file_uploader('product/', 'pdf', $request->file('brochure'));
        }
        $product->seoPageNm = !empty($request->seoPageNm) ? $request->seoPageNm : null;
        $product->sMetaTitle = !empty($request->sMetaTitle) ? $request->sMetaTitle : null;
        $product->sMetaKeywords = !empty($request->sMetaKeywords) ? $request->sMetaKeywords : null;
        $product->sMetaDescription = !empty($request->sMetaDescription) ? $request->sMetaDescription : null;
        $product->approve_status = 1;
        if ($request->has('image')) {
            $product->image = file_uploader('product/', 'png', $request->file('image'));
        }
        $product->save();

        if (!empty($request->sub_category_id)) {
            $product['sub_category_id'] = implode(',', $request->sub_category_id);
            $sub_cat_explode = explode(',', $product['sub_category_id']);
            foreach ($sub_cat_explode as $sub_id) {
                $product_sub_cat[] = [
                    'product_id' => $product->id,
                    'group_id' => $product->group_id,
                    'subcategory_id' => $sub_id,
                ];
            }
            DB::table('product_subcategory')->where('group_id', $id)->delete();
            DB::table('product_subcategory')->insert($product_sub_cat);
        }

        if ($request->hasfile('other_images')) {
            foreach ($request->file('other_images') as $key => $file) {
                $path = file_uploader('product/', 'png', $request->file('other_images')[$key]);
                $name = $file->getClientOriginalName();

                $insert[$key]['product_id'] = $product->id;
                $insert[$key]['group_id'] = $product->group_id;
                $insert[$key]['other_images'] = $path;
            }
            DB::table('products_media')->insert($insert);
        }

        $product_variant = [];
        $product->packate_measurement_attribute_id = $request->packate_measurement_attribute_id;

        if (!empty($product->packate_measurement_attribute_id)) {
            for ($i = 0; $i < count($request->packate_measurement_attribute_id); $i++) {
                $product_variant[] = [
                    'product_id' => $product->id,
                    'group_id' => $product->group_id,
                    'packate_measurement_attribute_id' => $request->packate_measurement_attribute_id[$i],
                    'packate_measurement_attribute_value' => $request->packate_measurement_attribute_value[$i],
                    'packate_measurement_sell_price' => $request->packate_measurement_sell_price[$i],
                    'packate_measurement_cost_price' => $request->packate_measurement_cost_price[$i],
                    'packate_measurement_discount_price' => $request->packate_measurement_discount_price[$i],
                    'packate_measurement_shelf_life_unit' => !empty($request->packate_measurement_shelf_life_unit[$i]) ? $request->packate_measurement_shelf_life_unit[$i] : null,
                    'packate_measurement_shelf_life_val' => $request->packate_measurement_shelf_life_val[$i] != 0 ? $request->packate_measurement_shelf_life_val[$i] : null,
                    'packate_measurement_barcode' => $request->packate_measurement_barcode[$i] != 0 ? $request->packate_measurement_barcode[$i] : null,
                    'packate_measurement_fssai_number' => $request->packate_measurement_fssai_number[$i] != 0 ? $request->packate_measurement_fssai_number[$i] : null,
                    'packate_measurement_qty' => $request->packate_measurement_qty[$i] != 0 ? $request->packate_measurement_qty[$i] : 0,
                    'packate_measurement_images' => !empty($request->file('packate_measurement_images')[$i]) ? file_uploader('product/', 'png', $request->file('packate_measurement_images')[$i]) : $products_variant[0]->packate_measurement_images,
//                    'packate_measurement_images' => ($request->has('packate_measurement_images'[$i])) ? file_uploader('product/', 'png', $request->file('packate_measurement_images')[$i]) : null
                ];
            }

            if (!empty($product_variant)) {
                DB::table('product_variant')->where('group_id', $id)->delete();
                DB::table('product_variant')->insert($product_variant);
            }
        }

        // PRODUCT FEATURES AND SPECIFICATION DATA
        $product_feature = [];
        if (session()->has('feature')) {
            if (session()->has('feature') != null) {
                foreach (session('feature') as $item) {
                    $product_feature[] = [
                        'product_id' => $product->id,
                        'group_id' => $product->group_id,
                        'features_name' => $item['sKeyFeatures'],
                        'features_status' => 1,
                    ];
                }
            }
            DB::table('product_features')->insert($product_feature);
            session()->forget('feature');
        }

        $product_specification = [];
        if (session()->has('specific')) {
            if (session()->has('specific') != null) {
                foreach (session('specific') as $specificitem) {
                    $product_specification[] = [
                        'product_id' => $product->id,
                        'group_id' => $product->group_id,
                        'specification_type' => $specificitem['specification_type'],
                        'specification_name' => $specificitem['specification_name'],
                        'specification_status' => 1,
                    ];
                }
            }
            DB::table('product_specification')->insert($product_specification);
            session()->forget('specific');
        }

        //      Product Shipping Data
        if ($request->delivery_charge) {
            $product_shipping = [];
            for ($i = 1; $i <= $request->total_zone; $i++) {
                $product_shipping[] = [
                    'product_id' => $product->id,
                    'group_id' => $product->group_id,
                    'delivery_charge' => $request->delivery_charge[$i],
                    'zone_id' => $request->zone_id[$i],
                ];
            }

            if ($product_shipping) {
                DB::table('product_shipping')->where('group_id', $id)->delete();
            }
            DB::table('product_shipping')->insert($product_shipping);
        }

        //       Arabic data
        $product1->lang_id = $request->arabic_lang_id;
        $product1->name = $request->arabic_name;
        $product1->category_id = $request->arabic_category_id;
        $product1->description = $request->arabic_description;
        $product1->indicator = $request->indicator_arabic;
        $product1->sku = !empty($request->sku_arabic) ? $request->sku_arabic : null;
        $product1->tags = !empty($request->tags_arabic) ? implode(',', $request->tags_arabic) : null;
        $product1->vendor = !empty($request->vendor_arabic) ? $request->vendor_arabic : null;
        $product1->made_in = !empty($request->made_in_arabic) ? $request->made_in_arabic : null;
        $product1->manufacturer = !empty($request->manufacturer_arabic) ? $request->manufacturer_arabic : null;
        $product1->manufacturer_part_no = !empty($request->manufacturer_part_no_arabic) ? $request->manufacturer_part_no_arabic : null;
        $product1->brand_ids = !empty($request->brand_ids_arabic) ? $request->brand_ids_arabic : null;
        $product1->weight = !empty($request->weight_arabic) ? $request->weight_arabic : 0;
        $product1->length = !empty($request->length_arabic) ? $request->length_arabic : 0;
        $product1->width = !empty($request->width_arabic) ? $request->width_arabic : 0;
        $product1->height = !empty($request->height_arabic) ? $request->height_arabic : 0;
        $product1->return_status = !empty($request->return_status_arabic) ? $request->return_status_arabic : 0;
        $product1->promo_status = !empty($request->promo_status_arabic) ? $request->promo_status_arabic : 0;
        $product1->cancelable_status = !empty($request->cancelable_status_arabic) ? $request->cancelable_status_arabic : 0;
        $product1->till_status = !empty($request->till_status_arabic) ? $request->till_status_arabic : null;
//        $product1->bstatus = $request->bstatus_arabic;
        $product1->bstatus = 1;
        $product1->videoURL = !empty($request->videoURL_arabic) ? $request->videoURL_arabic : null;
        if ($request->has('brochure_arabic')) {
            $product1->brochure = file_uploader('product/', 'pdf', $request->file('brochure_arabic'));
        }
        $product1->seoPageNm = !empty($request->arabic_seoPageNm) ? $request->arabic_seoPageNm : null;
        $product1->sMetaTitle = !empty($request->arabic_sMetaTitle) ? $request->arabic_sMetaTitle : null;
        $product1->sMetaKeywords = !empty($request->arabic_sMetaKeywords) ? $request->arabic_sMetaKeywords : null;
        $product1->sMetaDescription = !empty($request->arabic_sMetaDescription) ? $request->arabic_sMetaDescription : null;
        $product1->approve_status = 1;
        if ($request->has('arabic_image')) {
            $product1->image = file_uploader('product/', 'png', $request->file('arabic_image'));
        }
        $product1->save();

        if (!empty($request->arabic_sub_category_id)) {
            $product_sub_cat1 = [];
//            dd($request->arabic_sub_category_id);
            $product1['arabic_sub_category_id'] = implode(',', $request->arabic_sub_category_id);
            $sub_cat_explode1 = explode(',', $product1['arabic_sub_category_id']);
            foreach ($sub_cat_explode1 as $sub_id) {
                $product_sub_cat1[] = [
                    'product_id' => $product1->id,
                    'group_id' => $product1->group_id,
                    'subcategory_id' => $sub_id,
                ];
            }
            DB::table('product_subcategory')->insert($product_sub_cat1);
        }

        if ($request->hasfile('other_images_arabic')) {
            foreach ($request->file('other_images_arabic') as $key => $file) {
                $name = $file->getClientOriginalName();
                $path = file_uploader('product/', 'png', $request->file('other_images_arabic')[$key]);

                $insert1[$key]['product_id'] = $product1->id;
                $insert1[$key]['group_id'] = $product1->group_id;
                $insert1[$key]['other_images'] = $path;
            }
        }

        $product_variant1 = [];
        $product1->packate_measurement_attribute_id = $request->arabic_packate_measurement_attribute_id;
        if (!empty($product1->packate_measurement_attribute_id)) {
            for ($i = 0; $i < count($request->arabic_packate_measurement_attribute_id); $i++) {
                $product_variant1[] = [
                    'product_id' => $product1->id,
                    'group_id' => $product1->group_id,
                    'packate_measurement_attribute_id' => $request->arabic_packate_measurement_attribute_id[$i],
                    'packate_measurement_attribute_value' => $request->arabic_packate_measurement_attribute_value[$i],
                    'packate_measurement_sell_price' => $request->arabic_packate_measurement_sell_price[$i],
                    'packate_measurement_cost_price' => $request->arabic_packate_measurement_cost_price[$i],
                    'packate_measurement_discount_price' => $request->arabic_packate_measurement_discount_price[$i],
                    'packate_measurement_shelf_life_unit' => !empty($request->arabic_packate_measurement_shelf_life_unit[$i]) ? $request->arabic_packate_measurement_shelf_life_unit[$i] : null,
                    'packate_measurement_shelf_life_val' => $request->arabic_packate_measurement_shelf_life_val[$i] != 0 ? $request->arabic_packate_measurement_shelf_life_val[$i] : null,
                    'packate_measurement_barcode' => $request->arabic_packate_measurement_barcode[$i] != 0 ? $request->arabic_packate_measurement_barcode[$i] : null,
                    'packate_measurement_fssai_number' => $request->arabic_packate_measurement_fssai_number[$i] != 0 ? $request->arabic_packate_measurement_fssai_number[$i] : null,
                    'packate_measurement_qty' => $request->arabic_packate_measurement_qty[$i] != 0 ? $request->arabic_packate_measurement_qty[$i] : 0,
                    'packate_measurement_images' => !empty($request->file('arabic_packate_measurement_images')[$i]) ? file_uploader('product/', 'png', $request->file('arabic_packate_measurement_images')[$i]) : $products_variant_arabic[0]->packate_measurement_images
                ];
            }
            if (!empty($product_variant1)) {
                DB::table('product_variant')->insert($product_variant1);

            }
        }

        // PRODUCT FEATURES AND SPECIFICATION DATA
        $product_feature1 = [];
        if (session()->has('feature_arabic')) {
            if (session()->has('feature_arabic') != null) {
                foreach (session('feature_arabic') as $item) {
                    $product_feature1[] = [
                        'product_id' => $product1->id,
                        'group_id' => $product1->group_id,
                        'features_name' => $item['sKeyFeatures_arabic'],
                        'features_status' => 1,
                    ];
                }
            }
            DB::table('product_features')->insert($product_feature1);
            session()->forget('feature_arabic');
        }

        $product_specification1 = [];
        if (session()->has('specific_arabic')) {
            if (session()->has('specific_arabic') != null) {
                foreach (session('specific_arabic') as $specificitem) {
                    $product_specification1[] = [
                        'product_id' => $product1->id,
                        'group_id' => $product1->group_id,
                        'specification_type' => $specificitem['specification_type_arabic'],
                        'specification_name' => $specificitem['specification_name_arabic'],
                        'specification_status' => 1,
                    ];
                }
            }
            DB::table('product_specification')->insert($product_specification1);
            session()->forget('specific_arabic');
        }

        //      Product Shipping Data Arabic
        if ($request->arabic_delivery_charge) {
            $product_shipping1 = [];
            for ($i = 1; $i <= $request->arabic_total_zone; $i++) {
                $product_shipping1[] = [
                    'product_id' => $product1->id,
                    'group_id' => $product1->group_id,
                    'delivery_charge' => $request->arabic_delivery_charge[$i],
                    'zone_id' => $request->arabic_zone_id[$i],
                ];
            }
            DB::table('product_shipping')->insert($product_shipping1);
        }
//dd($request);
        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }

    public function review_update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $product = $this->product->where('group_id', $id)->where('lang_id', 1)->first();
        $product1 = $this->product->where('group_id', $id)->where('lang_id', 2)->first();

        if (!isset($product)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $product->published_status = $request->published_status;
        $product->topseller_status = $request->topseller_status;
        $product->show_home_page_status = $request->show_home_page_status;
        $product->indemand_status = $request->indemand_status;
        $product->review_status = $request->review_status;
        $product->bapprovalst = $request->bapprovalst;
        $product->mark_as_new_status = $request->mark_as_new_status;
        $product->approvalDt = $request->approvalDt;
        $product->adminComment = $request->adminComment;
        $product->block_product_status = $request->block_product_status;
        $product->block_comment = $request->block_comment;

        $product->save();

        $product1->published_status = $request->arabic_published_status;
        $product1->topseller_status = $request->arabic_topseller_status;
        $product1->show_home_page_status = $request->arabic_show_home_page_status;
        $product1->indemand_status = $request->arabic_indemand_status;
        $product1->review_status = $request->arabic_review_status;
        $product1->bapprovalst = $request->arabic_bapprovalst;
        $product1->mark_as_new_status = $request->arabic_mark_as_new_status;
        $product1->approvalDt = $request->arabic_approvalDt;
        $product1->adminComment = $request->arabic_adminComment;
        $product1->block_product_status = $request->arabic_block_product_status;
        $product1->block_comment = $request->arabic_block_comment;

        $product1->save();


        //       Arabic data
        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $product = $this->product->where('group_id', $id)->first();
        if (isset($product)) {
//            foreach (['thumbnail', 'image'] as $item) {
//                file_remover('product/', $product[$item]);
//            }
            Product::where('group_id', $id)->delete();
            $product_variant = DB::table('product_variant')->where('group_id', $id)->delete();
            $products_media = DB::table('products_media')->where('group_id', $id)->delete();
            $product_shipping = DB::table('product_shipping')->where('group_id', $id)->delete();
            $product_subcategory = DB::table('product_subcategory')->where('group_id', $id)->delete();
            $product_feature = DB::table('product_features')->where('group_id', $id)->delete();
            $product_specification = DB::table('product_specification')->where('group_id', $id)->delete();

//            $product->features()->delete();
//            $product->specifications()->delete();
            $product->delete();

            Toastr::success(DEFAULT_DELETE_200['message']);
            return back();
        }
        Toastr::success(DEFAULT_204['message']);
        return back();
    }

    public function status_update(Request $request, $id): JsonResponse
    {
        $product = $this->product->where('group_id', $id)->first();
        $this->product->where('group_id', $id)->update(['is_active' => !$product->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    public function ajax_add_category(Request $request)
    {
        $variation = [
            'variant' => $request['name'],
            'variant_key' => Str::slug($request['name']),
            'price' => $request['price']
        ];

        $zones = session()->has('category_wise_zones') ? session('category_wise_zones') : [];
        $existing_data = session()->has('variations') ? session('variations') : [];
        $editing_variants = session()->has('editing_variants') ? session('editing_variants') : [];

        if (!self::searchForKey($request['name'], $existing_data) && !in_array(Str::slug($request['name']), $editing_variants)) {
            $existing_data[] = $variation;
            session()->put('variations', $existing_data);
        } else {
            return response()->json(['flag' => 0, 'message' => translate('already_exist')]);
        }

        return response()->json(['flag' => 1, 'template' => view('productmanagement::admin.partials._variant-data', compact('zones'))->render()]);
    }

    public function ajax_add_variant(Request $request)
    {
        $variation = [
            'variant' => $request['name'],
            'variant_key' => Str::slug($request['name']),
            'price' => $request['price']
        ];

        $zones = session()->has('category_wise_zones') ? session('category_wise_zones') : [];
        $existing_data = session()->has('variations') ? session('variations') : [];
        $editing_variants = session()->has('editing_variants') ? session('editing_variants') : [];

        if (!self::searchForKey($request['name'], $existing_data) && !in_array(Str::slug($request['name']), $editing_variants)) {
            $existing_data[] = $variation;
            session()->put('variations', $existing_data);
        } else {
            return response()->json(['flag' => 0, 'message' => translate('already_exist')]);
        }

        return response()->json(['flag' => 1, 'template' => view('productmanagement::admin.partials._variant-data', compact('zones'))->render()]);
    }

    public function ajax_remove_variant($variant_key)
    {
        $zones = session()->has('category_wise_zones') ? session('category_wise_zones') : [];
        $existing_data = session()->has('variations') ? session('variations') : [];

        $filtered = collect($existing_data)->filter(function ($values) use ($variant_key) {
            return $values['variant_key'] != $variant_key;
        })->values()->toArray();

        session()->put('variations', $filtered);

        return response()->json(['flag' => 1, 'template' => view('productmanagement::admin.partials._variant-data', compact('zones'))->render()]);
    }

    function searchForKey($variant, $array): int|string|null
    {
        foreach ($array as $key => $val) {
            if ($val['variant'] === $variant) {
                return true;
            }
        }
        return false;
    }

    public function getSKU(Request $request): JsonResponse
    {
        $sku = $request->sku;
        $product_sku = $this->product->where('lang_id', 1)->where('sku', $sku)->get();
        if (count($product_sku) > 0) {
            return response()->json("taken");
        } else {
            return response()->json("not_taken");
        }
    }

    public function getSKUarabic(Request $request): JsonResponse
    {
        $sku = $request->sku_arabic;
        $product_sku = $this->product->where('lang_id', 2)->where('sku', $sku)->get();
        if (count($product_sku) > 0) {
            return response()->json("taken");
        } else {
            return response()->json("not_taken");
        }
    }

    public function ajax_switch_attribute(Request $request): JsonResponse
    {
        $id = $request->attributeID;
        $attribute_val = $this->attributeValue->where('lang_id', 1)->ofStatus(1)->where('attribute_id', $id)->orderBY('attribute_value', 'asc')->get();

        return response()->json([
            'template' => view('productmanagement::admin.partials._attribute-value-selector', compact('attribute_val'))->render(),
        ], 200);

    }

//    public function arabic_ajax_switch_attribute(Request $request, $id): JsonResponse
    public function arabic_ajax_switch_attribute(Request $request): JsonResponse
    {
        $id = $request->attributeID;
        $attribute_val = $this->attributeValue->where('lang_id', 2)->ofStatus(1)->where('attribute_id', $id)->orderBY('attribute_value', 'asc')->get();

        return response()->json([
            'template' => view('productmanagement::admin.partials._arabic-attribute-value-selector', compact('attribute_val'))->render(),
        ], 200);

//        return response()->json();


    }

    public function bulk_upload(Request $request): View|Factory|Application
    {
        $product_data = Product::orderBy('group_id', 'desc')->first();
        if (!empty($product_data)) {
            $product_grp_id = $product_data->group_id + 1;
        } else {
            $product_grp_id = 0;
        }
        return view('productmanagement::admin.bulkupload', compact('product_grp_id'));
    }

    public function import(Request $request)
    {
        $type = $request['type'];
        if ($type == 'products') {
            $products = (new FastExcel)->import($request->file('products'), function ($line) {
                $product_data = Product::orderBy('group_id', 'desc')->first();
                if (!empty($product_data)) {
                    $product_grp_id = $product_data->group_id + 1;
                } else {
                    $product_grp_id = 0;
                }
                $product = Product::create([
                    'group_id' => $product_grp_id,
                    'lang_id' => $line['lang_id'],
                    'name' => $line['name'],
                    'description' => $line['description'],
                    'category_id' => $line['category_id'],
                    'indicator' => $line['indicator'],
                    'sku' => $line['sku'],
                    'tags' => $line['tags'],
                    'vendor' => $line['vendor'],
                    'made_in' => $line['made_in'],
                    'manufacturer' => $line['manufacturer'],
                    'manufacturer_part_no' => $line['manufacturer_part_no'],
                    'brand_ids' => $line['brand_ids'],
                    'weight' => $line['weight'],
                    'length' => $line['length'],
                    'width' => $line['width'],
                    'height' => $line['height'],
                    'return_status' => $line['return_status'],
                    'promo_status' => $line['promo_status'],
                    'cancelable_status' => $line['cancelable_status'],
                    'till_status' => $line['till_status'],
                    'bstatus' => $line['bstatus'],
                    'image' => $line['image'],
                    'videoURL' => $line['videoURL'],
                    'brochure' => $line['brochure'],
                    'seoPageNm' => $line['seoPageNm'],
                    'sMetaTitle' => $line['sMetaTitle'],
                    'sMetaKeywords' => $line['sMetaKeywords'],
                    'sMetaDescription' => $line['sMetaDescription'],
                    'published_status' => $line['published_status'],
                    'show_home_page_status' => $line['show_home_page_status'],
                    'review_status' => $line['review_status'],
                    'availableStartDt' => $line['availableStartDt'],
                    'availableEndDt' => $line['availableEndDt'],
                    'mark_as_new_status' => $line['mark_as_new_status'],
                    'topseller_status' => $line['topseller_status'],
                    'indemand_status' => $line['indemand_status'],
                    'bapprovalst' => $line['bapprovalst'],
                    'approvalDt' => $line['approvalDt'],
                    'block_product_status' => $line['block_product_status'],
                    'block_comment' => $line['block_comment'],
                    'adminComment' => $line['adminComment'],
                    'approve_status' => $line['approve_status'],
                    'rating_count' => $line['rating_count'],
                    'avg_rating' => $line['avg_rating'],
                    'is_active' => $line['is_active'],
                    'created_at' => $line['created_at'],
                    'updated_at' => $line['updated_at'],
                ]);

                    $productId = $product->id;
                    $group_id = $product->group_id;

                    $subcat_id = explode(',',$line['subcategory_id']);
                    foreach ($subcat_id as $sid) {
                        $product_sub_cat[] = [
                            'product_id' => $productId,
                            'group_id' => $group_id,
                            'subcategory_id' => $sid,
                        ];
                    }
                    DB::table('product_subcategory')->insert($product_sub_cat);
            });
        }

        if ($type == 'variants') {
            $variants = (new FastExcel)->import($request->file('products'), function ($line) {
                $product_data = Product::orderBy('group_id', 'desc')->first();
                if (!empty($product_data)) {
                    $product_grp_id = $product_data->group_id + 1;
                } else {
                    $product_grp_id = 0;
                }
                Productvariant::create([
                    'product_id' => $line['product_id'],
                    'group_id' => $line['group_id'],
                    'packate_measurement_attribute_id' => $line['packate_measurement_attribute_id'],
                    'packate_measurement_attribute_value' => $line['packate_measurement_attribute_value'],
                    'packate_measurement_sell_price' => $line['packate_measurement_sell_price'],
                    'packate_measurement_cost_price' => $line['packate_measurement_cost_price'],
                    'packate_measurement_discount_price' => $line['packate_measurement_discount_price'],
                    'packate_measurement_shelf_life_val' => $line['packate_measurement_shelf_life_val'],
                    'packate_measurement_shelf_life_unit' => $line['packate_measurement_shelf_life_unit'],
                    'packate_measurement_barcode' => $line['packate_measurement_barcode'],
                    'packate_measurement_fssai_number' => $line['packate_measurement_fssai_number'],
                    'packate_measurement_qty' => $line['packate_measurement_qty'],
                    'packate_measurement_images' => $line['packate_measurement_images'],
//                    'created_at' => $line['created_at'],
//                    'updated_at' => $line['updated_at'],
                ]);
            });
        }

        if ($type == 'features') {
            $variants = (new FastExcel)->import($request->file('products'), function ($line) {
                $product_data = Product::orderBy('group_id', 'desc')->first();
                if (!empty($product_data)) {
                    $product_grp_id = $product_data->group_id + 1;
                } else {
                    $product_grp_id = 0;
                }
                Feature::create([
                    'product_id' => $line['product_id'],
                    'group_id' => $line['group_id'],
                    'features_name' => $line['features_name'],
                    'features_status' => $line['features_status'],
//                    'created_at' => $line['created_at'],
//                    'updated_at' => $line['updated_at'],
                ]);
            });
        }

        if ($type == 'specifications') {
            $variants = (new FastExcel)->import($request->file('products'), function ($line) {
                $product_data = Product::orderBy('group_id', 'desc')->first();
                if (!empty($product_data)) {
                    $product_grp_id = $product_data->group_id + 1;
                } else {
                    $product_grp_id = 0;
                }
                Specification::create([
                    'product_id' => $line['product_id'],
                    'group_id' => $line['group_id'],
                    'specification_type' => $line['specification_type'],
                    'specification_name' => $line['specification_name'],
                    'specification_status' => $line['specification_status'],
                    'created_at' => $line['created_at'],
                    'updated_at' => $line['updated_at'],
                ]);
            });
        }

        if ($type == 'media') {
            $variants = (new FastExcel)->import($request->file('products'), function ($line) {
                $product_data = Product::orderBy('group_id', 'desc')->first();
                if (!empty($product_data)) {
                    $product_grp_id = $product_data->group_id + 1;
                } else {
                    $product_grp_id = 0;
                }
                Productsmedia::create([
                    'product_id' => $line['product_id'],
                    'group_id' => $line['group_id'],
                    'other_images' => $line['other_images'],
                    'created_at' => $line['created_at'],
                    'updated_at' => $line['updated_at'],
                ]);
            });
        }

        if ($type == 'shipping') {
            $variants = (new FastExcel)->import($request->file('products'), function ($line) {
                $product_data = Product::orderBy('group_id', 'desc')->first();
                if (!empty($product_data)) {
                    $product_grp_id = $product_data->group_id + 1;
                } else {
                    $product_grp_id = 0;
                }
                Productshipping::create([
                    'product_id' => $line['product_id'],
                    'group_id' => $line['group_id'],
                    'zone_id' => $line['zone_id'],
                    'delivery_charge' => $line['delivery_charge'],
                    'created_at' => $line['created_at'],
                    'updated_at' => $line['updated_at'],
                ]);
            });
        }

        return back();
//        return redirect('excel')->with(['success' => "Users imported successfully."]);
    }

    public function download(Request $request): string|StreamedResponse
    {
        $list = collect([
            [ 'id' => 1, 'name' => 'Jane' ],
        ]);
      return (new FastExcel($list))->download(time() . '-file.xlsx');
    }
}
