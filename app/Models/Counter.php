<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'amount'];

    public static function getCounter($type)
    {
        // pobieram dane...
        $temp = static::findOrFail($type);
        $number =  $temp->amount;
        $number++;
        $temp->amount = $number;
        $temp->update();
        return $number++;
    }

    public static function getNumber($doc)
    {
        /*
             generowanie nr dokumentu
             zakładam ciąg np TYP/DATA/NR
        */
        $syear = now()->year;
        $smonth = now()->month;
        $sday = now()->day;
        $sdate = $syear.$smonth.$sday;

        switch ($doc) {
           case 'SHIPMENT':
                $type = 'D';
                $counter = 2;
                break;
           case 'ORDER':
                $type = 'W';
                $counter = 3;
                break;
           break;
        default: $type = '';
        }
        $number = Counter::getCounter($counter);

        return $type.'_'.$sdate.'_'.$number;

    }
}
