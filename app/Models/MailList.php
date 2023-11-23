<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailList extends Model
{
    use HasFactory;

    protected $table = 'mail_listing';
    public $timestamps = false;
    
    protected $fillable = [
        'mail_id','createdDateTime','changeKey','receivedDateTime','subject','bodyPreview','webLink','content','sentDateTime'
    ];
}
