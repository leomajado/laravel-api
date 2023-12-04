<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
  use HasFactory, SoftDeletes;

  protected $primaryKey = 'id';

  protected $table = 'posts';

  protected $fillable = ['title', 'description', 'user_id', 'created_at', 'updated_at', 'deleted_at'];

  protected $dates = ['created_at', 'updated_at', 'deleted_at'];

  protected $hidden = ['deleted_at'];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
