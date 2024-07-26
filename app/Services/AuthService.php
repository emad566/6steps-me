<?php


namespace App\Services;

use App\Http\Resources\AdminResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CreatorResource;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Creator;
use Illuminate\Support\Facades\Auth;

class AuthService
{ 
    public $table;
    public $primaryKey;
    public $model;
    public $resource;
    public $id;
    public $guard;

    function __construct()
    {
        if(auth('admin')->check()){
            $this->table = 'admins';
            $this->primaryKey = 'admin_id';
            $this->model = Admin::class;
            $this->resource = AdminResource::class;
            $this->id = Auth::user()->admin_id;
            $this->guard = 'admin';
        }

        if(auth('brand')->check()){
            $this->table = 'brands';
            $this->primaryKey = 'brand_id';
            $this->model = Brand::class;
            $this->resource = BrandResource::class;
            $this->id = Auth::user()->brand_id;
            $this->guard = 'brand';
        }

        if(auth('creator')->check()){
            $this->table = 'creators';
            $this->primaryKey = 'creator_id';
            $this->model = Creator::class;
            $this->resource = CreatorResource::class;
            $this->id = Auth::user()->creator_id;
            $this->guard = 'creator';
        }
    }
}
