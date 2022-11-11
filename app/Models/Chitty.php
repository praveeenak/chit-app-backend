<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chitty extends Model
{
    use HasFactory;
    protected $table = 'chitty';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'chitty_name',
    ];
    public static function getDefaultChittyId()
    {
        $data = Self::first();
        if (isset($data)) {
            $data = new Self();
            $data->chitty_name = 'C001';
            $data->save();
            return $data->id;
        }
		return $data->id;
    }

}

// {
//     return $data->id;
// }else{
//     $data=new Chitty();
//     $data->chitty_name='C001';
//     $data->save();
//     return $data->id;
// } { {
