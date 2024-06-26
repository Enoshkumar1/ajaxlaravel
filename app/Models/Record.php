<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Record extends Model
{
    use HasFactory;

    public function up()
{
    Schema::create('records', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('number');
        $table->string('email');
        $table->string('image')->nullable();
        $table->text('description')->nullable();
        $table->timestamps();
    });
}

}
