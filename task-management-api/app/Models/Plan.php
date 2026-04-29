<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'max_created_teams', 'max_joined_teams', 'price'];

    // دالة للتحقق: هل الخطة مجانية؟
    public function isFree()
    {
        return $this->price == 0;
    }
    
    // دالة للتحقق: هل الخطة غير محدودة؟
    public function isUnlimited()
    {
        return $this->max_created_teams === -1 && $this->max_joined_teams === -1;
    }
}