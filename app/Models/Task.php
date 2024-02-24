<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'task';  
    
    protected $fillable = ['body'];

    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
