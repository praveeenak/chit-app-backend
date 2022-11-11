<?php
namespace App\Models;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $fillable = [
        'customer_id',
        'amount',
        'is_selected',
        'chitty_id',
    ];
    //transaction_no
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->transaction_no = IdGenerator::generate(['table' => 'transactions', 'field' => 'transaction_no', 'length' => 10, 'prefix' => 'TRN']);
        });
    }
}
