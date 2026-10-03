<?php

namespace App\Models;
use App\Http\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    use HasFactory;
    use HasUuid;

    protected $guarded = [];
    public $timestamps = true;

    public function listAll($searchTerm){
        return $this->with(['user'])
                    ->where(function ($query) use ($searchTerm) {
                        $query->where('title', 'like', '%' . $searchTerm . '%')
                            ->orWhere('comment', 'like', '%' . $searchTerm . '%');
                    })
                    ->latest()
                    ->paginate(10);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getMessageAttribute()
    {
        return $this->attributes['comment'] ?? null;
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['comment'] = $value;
    }

    public function getIsReadAttribute()
    {
        return (bool) ($this->attributes['seen'] ?? 0);
    }

    public function setIsReadAttribute($value)
    {
        $this->attributes['seen'] = $value ? 1 : 0;
    }
}