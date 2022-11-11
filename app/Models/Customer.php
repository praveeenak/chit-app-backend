<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';
    protected $fillable = [
        'name',
        'phone',
        'address',
        'c_id',
        'chitty_number',
    ];
	public function coordinator()
	{
		$this->hasOne(Coordinator::class,'c_id','id');
	}
}
