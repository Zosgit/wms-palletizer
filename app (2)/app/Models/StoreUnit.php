<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Counter;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreUnit extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['ean', 'storeunittype_id', 'location_id','su_multi','status_id'];

    public function CreateUnit($type)
    {
        // pobieram licznik opakowania
        $counter = Counter::getCounter(1);
        $firm = '5905468';   // <- tutaj damy dane z konfiguracji magazynu - firmy
        $label = $this->generate_sscc_barcode($counter,'0',$firm,$counter);
        $this->ean = $label;
        $this->storeunittype_id = $type;
        $this->su_multi = False;
        $this->status_id = 201;
        $this->save();
        return true;
    }

    public function storeunittype(): BelongsTo
    {
        return $this->belongsTo(StoreUnitType::class, 'storeunittype_id', 'id');
    }

    public static function booted()
    {
        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }


    /*
        $next_serial_available - aktualna wartość z licznika - do pokazania
        $extension - przedrostek na początku - string
        $gs1_prefix - kod gln firmy
        $max_serial - aktualna wartość z licznika - do wyliczenia
    */
    public function generate_sscc_barcode($next_serial_available, $extension, $gs1_prefix, $max_serial){
        // sprawdzam czy liczba
        if(!is_numeric($next_serial_available) || !is_numeric($gs1_prefix) || !is_numeric($max_serial)){
          return null;
        }
        if($max_serial > 999999999){
          $max_serial = 999999999;
        }

        // ustawiam nastepny nr seryjny
        $serial = $next_serial_available;

        // sprawdzam czy istnieje wpis i czy cyfra jest prawidłowa w przeciwnym razie ustawiam na 0
        if(empty($extension) || strlen($extension) !== 1 || !preg_match('/[0-9]/', $extension)){
          $extension = '0';
        }

        // tworzę ciąg do generowania
        $barcode = $extension.str_pad($gs1_prefix, 7, '0', STR_PAD_LEFT).str_pad($serial, 9, '0', STR_PAD_LEFT);
        //pobieram cyfre kontrolna
        $check_digit = $this->sscc_check_digit($barcode);
        // jezeli brak to zwracam null
        if($check_digit === false){
          return null;
        }

        return "$barcode$check_digit";
      }

      function sscc_check_digit($barcode){
        // jezeli ciąg ma 20 znaków to usuwam zera
        $len = strlen($barcode);
        if (($len == 20  || $len == 19) && strpos($barcode, '00') === 0){
          $barcode = substr($barcode, 2);
        }
        // jeżeli długość zła <> (17,18) to zwracam false
        $len = strlen($barcode);
        if ($len != 18 && $len != 17){
          return false;
        }

        // Na podstawie: http://www.gs1.org/how-calculate-check-digit-manually
        $sum = 0;
        for($i=0; $i<17; $i++){
            // mnoznik - nieparzyste 1 a parzyste 3
          $multiplier = ($i % 2 == 0) ? 3 : 1;

            // sumuje kolejne pozycje (wartosc z barcode * mnoznik)
          $sum += intval(substr($barcode,$i,1)) * $multiplier;
        }
        // suma dzialona, zaokrąglona i pomnożona - odejmuje sumę i różnica to cyfra kontrolna
        $check_digit = (ceil($sum / 10) * 10) - $sum;

        if($len == 17){
          return "$check_digit";
        }
        if($len == 18){
          return ($check_digit == intval(substr($barcode,17,1)));
        }
      }


}


// $ok = $this->generate_sscc_barcode(127,0,5905468,127);
