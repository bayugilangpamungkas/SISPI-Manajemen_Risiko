<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedExcel extends Model
{
    use HasFactory;

   protected $fillable = [
       'nama_file',
       'jumlah_data',
       'uploaded_by'
   ];
}
