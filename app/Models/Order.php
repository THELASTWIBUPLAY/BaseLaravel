<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'table_id', 
        'customer_name', // DIGANTI
        'subtotal', 
        'tax_amount', 
        'final_total', 
        'status'];

    // Relasi: Order dimiliki oleh SATU Table
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    // Relasi: Order memiliki BANYAK OrderItem (detail pesanan)
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}