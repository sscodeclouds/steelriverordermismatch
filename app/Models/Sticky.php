<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sticky extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['order_id', 'is_test_order', 'revenue', 'order_status', 'created_at'];
}
