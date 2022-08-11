<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GA_ecommerce extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['order_id', 'revenue', 'tax', 'shipping', 'refund_amount', 'quantity'];
}
