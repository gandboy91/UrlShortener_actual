<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Elements extends Model
{
    protected $table = 'elements'; //таблица
	protected $fillable = ['id','type_id', 'dependencies','ifUsed', 'name']; //поля доступные для массового заполнения
 	public $incrementing = false; //отменили инкрементирование перв. ключа
    public $timestamps = false; 
	public function type()
    {
        return $this->belongsTo('App\ElTypes','type_id'); // 1 к 1 связь с типами по id
    }
}
