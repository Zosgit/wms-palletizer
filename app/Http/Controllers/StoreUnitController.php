<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\StoreUnit;
use App\Models\StoreUnitType;
use App\Models\Counter;
use PDF;
use DNS1D;
use DB;

class StoreUnitController extends Controller
{
    public function index(Request $request)
    {

        if ( $request->type != null)
            {
                if ($request->search != null and $request->type != null) // mamy rodzaj + wartość
                    {
                        $storeunits = DB::table('v_store_units')->where($request->type,'LIKE','%'.$request->search.'%')->orderby('updated_at','desc')->paginate(100);
                    }
                else
                    {
                        // szukamy pustych
                        $storeunits = DB::table('v_store_units')->wherenull($request->type)->orderby('updated_at','desc')->paginate(100);
                    }
            }
        else // pokazuje wszystko
            {
                $storeunits = DB::table('v_store_units')->orderby('updated_at','desc')->paginate(100);
            }

        return view('storeunits.index', compact('storeunits'));

    }

    public function printMultiPDF($print)
    {
        $storeunits = StoreUnit::where('print',$print)->get();
        return view('storeunits.listprint', compact('storeunits','print'));
    }


    public function create()
    {
        return view('storeunits.create',['store_unit_types' => StoreUnitType::all()]);
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'number'=> 'required|integer',
            'storeunittype_id'=> 'required|integer',
        ]);

        $number = $validatedAttributes['number'];

        $print = Str::uuid()->toString();

        for ($i = 1; $i <= $number; $i++) {

            // pobieram licznik opakowania
            $counter = Counter::getCounter(1);
            $firm = '5905468';   // <- tutaj damy dane z konfiguracji magazynu - firmy
            $ean = $this->generate_sscc_barcode($counter,'0',$firm,$counter);

            if (StoreUnit::where('ean', $ean)->doesntExist())
            {
                StoreUnit::create([
                    'ean'               => $ean,
                    'storeunittype_id'  => $validatedAttributes['storeunittype_id'],
                    'su_multi'          => True,
                    'print'             => $print,
                ]);
            }
        }
        return redirect()->route('storeunit.print-multi-pdf', compact('print'));
    }

    public function edit($id)
    {
        //..
    }

    public function generatePDF($id)
    {
        $storeunits = StoreUnit::where('id',$id)->get();
        $pdf = PDF::loadView('storeunits.printsu',compact('storeunits'));
        return $pdf->download('su.pdf');
    }

    public function generateMultiPDF($print)
    {
        $storeunits = StoreUnit::where('print',$print)->get();
        $pdf = PDF::loadView('storeunits.printsu',compact('storeunits'));
        return $pdf->download('storeunits.pdf');
    }




    /*
        $next_serial_available - aktualna wartość z licznika - do pokazania
        $extension - przedrostek na początku - string
        $gs1_prefix - kod gln firmy
        $max_serial - aktualna wartość z licznika - do wyliczenia
    */
    public function generate_sscc_barcode($next_serial_available, $extension, $gs1_prefix, $max_serial)
    {
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
