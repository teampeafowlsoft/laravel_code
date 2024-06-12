<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Productdiscount;
use Modules\ProductManagement\Entities\Product;
use Modules\ServiceManagement\Entities\Service;
use Modules\ZoneManagement\Entities\Zone;

class ProductdiscountType extends Model
{
    use HasFactory;

    protected $fillable = ['discount_id', 'discount_type', 'type_wise_id'];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'type_wise_id');
    }

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class, 'type_wise_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Productcategory::class, 'type_wise_id');
    }

    public function zone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Zone::class, 'type_wise_id');
    }

    public function discount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Productdiscount::class, 'discount_id');
    }
}
