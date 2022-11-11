<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChittyWinner extends Model
{
    use HasFactory;
    protected $table = 'chitty_winners';
    protected $fillable = [
        'chitty_id',
        'customer_id',
    ];
	public function customer()
	{
		$this->hasOne(Customer::class,'customer_id','id');
	}
}
