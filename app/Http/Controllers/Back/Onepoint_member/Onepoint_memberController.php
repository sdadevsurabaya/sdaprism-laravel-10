<?php
        namespace App\Http\Controllers\Back\Onepoint_member;
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use App\Models\Member;
        use App\Models\Uniquecode;
use App\Models\UniqueCodeMember;
use DB;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
        use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

        class Onepoint_memberController extends Controller
        {
            /**
             * Display a listing of the resource.
             *
             * @return \Illuminate\Http\Response
             */
        
            public function index(Request $request)
            {
                $data = Member::orderBy("id","DESC")->get();
                return view("back.Onepoint_member.index",compact("data"))
                    ->with("i", ($request->input("page", 1) - 1) * 5);
            }
        
            /**
             * Show the form for creating a new resource.
             *
             * @return \Illuminate\Http\Response
             */
        
            public function create()
            {
                $code = Uniquecode::where('status', 'unused')->get();
                // dd($code);
                return view("back.Onepoint_member.create", compact('code'));
            }
        
        
        
            /**
             * Store a newly created resource in storage.
             *
             * @param  \Illuminate\Http\Request  $request
             * @return \Illuminate\Http\Response
             */
        
            public function store(Request $request)
            {

                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users|max:255',
                    'telp' => 'nullable',
                    // 'uniquecode' => 'required|string|max:255',
                    // Tambahkan aturan validasi lainnya sesuai kebutuhan Anda
                ]);
            
                if ($validator->fails()) {
                    return redirect()->back()
                                    ->withErrors($validator)
                                    ->withInput();
                }
                

                // if (!$user) {

                    $user = User::create([
                        'name'  => $request->name,
                        'email' => $request->email,
                        'email_verified_at' =>   Carbon::now(),
                        'password' => Hash::make('sdamember')
                    ]);
                    $user->assignRole('Member');

                    $userName = $user->name ?? 'DF'; 
                    $formatName = strtoupper(substr($userName, 0, 2));
                    $randomNumber = str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                    // $uniqueCode =  $request->uniquecode;

                    //uniquecode for member
                    $uniqueCode =  $formatName.$randomNumber;
              
                    $member = Member::create([
                        'emailwithoutdot' => $request->email,
                        'email' => $request->email,
                        'password' => $user->password,
                        'telp' => $request->telp,
                        // 'uniquecode' => $request->uniquecode
                        'uniquecode' => $uniqueCode,
                    ]);
              

                  

                    // // Generate QR Code
                    $qrcode = QrCode::size(400)->generate($uniqueCode);
                    $qrcodePath = 'qrcode-' . $uniqueCode . '.svg';
                    $qrcodeFullPath = public_path('qrcode/' . $qrcodePath);
                    file_put_contents($qrcodeFullPath, $qrcode);

                    // // Generate Barcode
                    $generatorSVG = new BarcodeGeneratorSVG();
                    $barcodeSVG = $generatorSVG->getBarcode($uniqueCode, $generatorSVG::TYPE_CODE_128);
                    $barcodePath = 'barcode-' . $uniqueCode . '.svg';
                    $barcodeFullPath = public_path('barcode/' . $barcodePath);
                    file_put_contents($barcodeFullPath, $barcodeSVG);

                    // // Simpan informasi pada member
                    $member->barcode = $barcodePath;
                    $member->qrcode = $qrcodePath;
                    $member->save();


                    //update uniquecode member 
                    // $uniqueCodeMember = UniqueCodeMember::where('uniquecode', $request->uniquecode)->first();

                    // melakukan update 
                    // $uniqueCodeMember->status = 'used';
                    // $uniqueCodeMember->createby_id = Auth::id();
                    // $uniqueCodeMember->member_id = $member->id;
                    // $uniqueCodeMember->save();
                    // Redirect dengan pesan sukses
                    return redirect('generate-qr-admin/' . $member->id);


               
//                     $this->validate($request, ["password" => "required",
// "kodenegara" => "required",
// ]);
//                 $input = $request->all();
                
//               if ($request->hasfile("image")) {
//                 $fileName = time() . rand(1, 100) . "." . $request->file("image")->extension();
//                 $file = $request->file("image");
//                 $file->move(public_path("images/Onepoint_member"), $fileName);
//                 dump("images");
//             }
//             if(!empty($fileName)){ 
//                 $input["image"] = $fileName;
//             }else{
//                 $input["image"] = "";
//             }
              
                
//                 $Onepoint_member = Member::create($input);
               
            
//                 return redirect()->route("onepoint_member.index")
//                 ->with("success","Onepoint_member created successfully");
            
            }
        
        
            /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
        
                public function show($id)
                {
                    $Onepoint_member = Member::find($id);
                    return view("back.Onepoint_member.show",compact("Onepoint_member"));
                }
            

            
                /**
                 * Show the form for editing the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
            
                public function edit($id)
                {
                    $member = Member::find($id);
                    return view("back.Onepoint_member.edit",compact("member"));
                }


                public function GenerateQr($id)
                {
                    $member = Member::find($id);
                    // $member = Member::find(844);

                    $user = User::where('email', $member->email)->first();
                     // Format unique code
                    $userName = $user->name ?? 'DF'; 
                    $formatName = strtoupper(substr($userName, 0, 2));
                    $randomNumber = str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                    $uniqueCode =  $formatName . $randomNumber;

                    // Generate QR Code
                    $qrcode = QrCode::size(400)->generate($uniqueCode);
                    $qrcodePath = 'qrcode-' . $uniqueCode . '.svg';
                    $qrcodeFullPath = public_path('qrcode/' . $qrcodePath);
                    file_put_contents($qrcodeFullPath, $qrcode);

                    // Generate Barcode
                    $generatorSVG = new BarcodeGeneratorSVG();
                    $barcodeSVG = $generatorSVG->getBarcode($uniqueCode, $generatorSVG::TYPE_CODE_128);
                    $barcodePath = 'barcode-' . $uniqueCode . '.svg';
                    $barcodeFullPath = public_path('barcode/' . $barcodePath);
                    file_put_contents($barcodeFullPath, $barcodeSVG);

                    // Simpan informasi pada member
                    $member->barcode = $barcodePath;
                    $member->qrcode = $qrcodePath;
                    $member->uniquecode = $uniqueCode;
                    $member->save();

                    // Redirect dengan pesan sukses
                    return redirect()->route("onepoint_member.index")->with("success", "Generate Qrcode And Barcode created successfully");
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
                
                   
//                         $this->validate($request, ["password" => "required",
// "kodenegara" => "required",
// ]);
                        

                    $input = $request->all();

                    
              if ($request->hasfile("image")) {
                $fileName = time() . rand(1, 100) . "." . $request->file("image")->extension();
                $file = $request->file("image");
                $file->move(public_path("images/Onepoint_member"), $fileName);
                dump("images");
            }
            if(!empty($fileName)){ 
                $input["image"] = $fileName;
            }else{
                $input["image"] = "";
            }
              
            // dd($request->deleted);
                    
                    $Onepoint_member = Member::find($id);
                    $Onepoint_member->update($input);
                
                    return redirect()->route("onepoint_member.index")
                    ->with("success","Onepoint_member updated successfully");
                
                }
            

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
            
                public function destroy($id)
                {                   
                    $Onepoint_member = Member::find($id);                   
                    $input["deleted"] =  'true';
                    $Onepoint_member->update($input);        
                  
                    return redirect()->route("onepoint_member.index")
                    ->with("success","Onepoint_member deleted successfully");    
                
                }

                public function GenerateQrAll(){
                    $members = Member::whereNull('uniquecode')->get();
                    $generatorSVG = new BarcodeGeneratorSVG();
                    
                    foreach ($members as $member) {
                        $user = User::where('email', $member->email)->first();
                        $userName = $user->name ?? 'DF'; 
                        $formatName = strtoupper(substr($userName, 0, 2));
                        $randomNumber = str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                        $uniqueCode =  $formatName . $randomNumber;
                    
                        // Generate QR Code
                        $qrcode = QrCode::size(400)->generate($uniqueCode, public_path('qrcode/' . 'qrcode-' . $uniqueCode . '.svg'));
                        $member->qrcode = 'qrcode-' . $uniqueCode . '.svg';
                    
                        // Generate Barcode
                        $image = $generatorSVG->getBarcode($uniqueCode, $generatorSVG::TYPE_CODE_128);
                        file_put_contents(public_path('barcode/' . 'barcode-' . $uniqueCode . '.svg'), $image);
                        $member->barcode = 'barcode-' . $uniqueCode . '.svg';
                    
                        $member->uniquecode = $uniqueCode;
                        $member->save();
                    }

                    return redirect()->route("onepoint_member.index")->with("success", "Generate Qrcode All And Barcode created successfully");
                }
            }
        
        ?>