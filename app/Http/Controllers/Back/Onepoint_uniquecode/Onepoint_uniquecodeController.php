<?php
        namespace App\Http\Controllers\Back\Onepoint_uniquecode;
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use App\Models\Uniquecode;
        use Illuminate\Support\Facades\DB;
        use Hash;
        use Illuminate\Support\Arr;

        class Onepoint_uniquecodeController extends Controller
        {
            /**
             * Display a listing of the resource.
             *
             * @return \Illuminate\Http\Response
             */
        
            public function index(Request $request)
            {
                $data = Uniquecode::orderBy("id","DESC")->get();
                return view("back.Onepoint_uniquecode.index",compact("data"))
                    ->with("i", ($request->input("page", 1) - 1) * 5);
            }

            public function generatecodes(Request $request)
            {

                $limitgenerate = $request->limitgenerate;
                $valuepoint = $request->valuepoint;
                date_default_timezone_set('Asia/Jakarta');
                for ($i=0; $i < $limitgenerate ; $i++) { 
                    $uniquecode = (date("y").date("m").date("d").date("h").date("i").rand(111111, 999999));
                    $uniquecode = str_split($uniquecode, 4);
                    $uniquecode = implode("-",$uniquecode);
                    // DB::insert("insert into onepoint_uniquecode(kode) values ('$uniquecode')");
                    $input = $request->all();
                    $input['kode']=$uniquecode;
                    $input['point']=$valuepoint;
                    $Onepoint_uniquecode = Uniquecode::create($input);
                }

                $data = Uniquecode::orderBy("id","DESC")->get();
                return view("back.Onepoint_uniquecode.index",compact("data"))
                    ->with("i", ($request->input("page", 1) - 1) * 5);
            }
        
            /**
             * Show the form for creating a new resource.
             *
             * @return \Illuminate\Http\Response
             */
        
            public function create()
            {
                return view("back.Onepoint_uniquecode.create");
            }
        
            /**
             * Store a newly created resource in storage.
             *
             * @param  \Illuminate\Http\Request  $request
             * @return \Illuminate\Http\Response
             */
        
            public function store(Request $request)
            {
                
                $request->validate([
                    'total_code' => 'required|integer|min:1', // total_code harus diisi, integer, dan minimal 1
                    'total_point' => 'required|integer|min:0', // total_point harus diisi, integer, dan minimal 0
                    'label' => 'required|string|max:255', // label harus diisi, string, dan maksimal 255 karakter
                    'expired_date' => 'required|date|after_or_equal:today', // expired_date harus diisi, berupa tanggal, dan setidaknya sama dengan hari ini
                ]);

                
                $input = $request->all();


                $jumlahkode = $request->total_code;
                $point = $request->total_point;
                $label = $request->label;
                $expired = $request->expired_date;



                $generatedCodes = [];

                for ($i = 0; $i < $jumlahkode; $i++) {
                 
                    $code = $this->generateCode();
                    $entry = [
                        'kode' => $code,
                        'point' => $point,
                        'label' => $label,
                        'expired_date' => $expired,
                    ];

                 
                    Uniquecode::create($entry);

                  
                    $generatedCodes[] = $entry;
                }



                return redirect()->route("onepoint_uniquecode.index")
                ->with("success","Onepoint_uniquecode created successfully");
            
            }
        
        
            /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
        
                public function show($id)
                {
                    $Onepoint_uniquecode = Uniquecode::find($id);
                    return view("back.Onepoint_uniquecode.show",compact("Onepoint_uniquecode"));
                }
            

            
                /**
                 * Show the form for editing the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
            
                public function edit($id)
                {
                    $Onepoint_uniquecode = Uniquecode::find($id);
                    return view("back.Onepoint_uniquecode.edit",compact("Onepoint_uniquecode"));
                }
            

            
                /**
                 * Update the specified resource in storage.
                 *
                 * @param  \Illuminate\Http\Request  $request
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
            
                public function update(Request $request, $id)
                {
                    $input = $request->all();

                    $Onepoint_uniquecode = Uniquecode::find($id);
                    $Onepoint_uniquecode->update($input);
                
                    return redirect()->route("onepoint_uniquecode.index")
                    ->with("success","Onepoint_uniquecode updated successfully");
                
                }
            

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
            
                public function destroy($id)
                {
                    
                    $Onepoint_uniquecode = Uniquecode::find($id);                   
                    $input["deleted"] =  'true';
                    $Onepoint_uniquecode->update($input);        
                  
                    return redirect()->route("onepoint_uniquecode.index")
                    ->with("success","Onepoint_uniquecode deleted successfully");
                
                }




            
                private function generateCode()
                {
                    // Generate a string of 16 characters with a dash every 4 characters
                    $code = '';
                    for ($i = 0; $i < 16; $i++) {
                        // Add a dash every 4 characters
                        if ($i > 0 && $i % 4 === 0) {
                            $code .= '-';
                        }
            
                        // Ensure the first character of each block is a letter
                        if ($i % 5 === 0) {
                            $code .= $this->generateRandomChar(true); // Pass true to generate letters
                        } else {
                            $code .= $this->generateRandomChar();
                        }
                    }
            
                    return $code;
                }
            
                private function generateRandomChar($letters = false)
                {
                    // Generate a random character (letter or number)
                    $characters = $letters ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '0123456789';
                    $randomChar = $characters[rand(0, strlen($characters) - 1)];
            
                    return $randomChar;
                }
            }
        
        ?>