<?php
        namespace App\Http\Controllers\Back\Onepoint_merchant;
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use App\Models\Merchant;
        use DB;
        use Hash;
        use Illuminate\Support\Arr;

        class Onepoint_merchantController extends Controller
        {
            /**
             * Display a listing of the resource.
             *
             * @return \Illuminate\Http\Response
             */
        
            public function index(Request $request)
            {
                $data = Merchant::orderBy("id","DESC")
                ->get();
                return view("back.Onepoint_merchant.index",compact("data"))
                    ->with("i", ($request->input("page", 1) - 1) * 5);
            }
        
            /**
             * Show the form for creating a new resource.
             *
             * @return \Illuminate\Http\Response
             */
        
            public function create()
            {
                return view("back.Onepoint_merchant.create");
            }
        
        
        
            /**
             * Store a newly created resource in storage.
             *
             * @param  \Illuminate\Http\Request  $request
             * @return \Illuminate\Http\Response
             */
        
             public function store(Request $request)
             {
                 // Validasi input
                 $request->validate([
                     'merchant_name' => 'required|string|max:255',
                     'label' => 'required|string|max:255',
                     'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                     'url' => 'required|url|max:255',
                 ]);
             
               
                 $input = $request->all();
             
                
                 if ($request->hasFile('image')) {
                     $image = $request->file('image');
                     $imageName = time() . '.' . $image->getClientOriginalExtension();
                     $image->move(public_path('merchant'), $imageName);
                     $input['image'] = $imageName;
                 }
             

                 $onpointMerchant = Merchant::create($input);
             

                 return redirect()->route("onepoint_merchant.index")
                     ->with("success", "Onepoint_merchant created successfully");
             }
             
        
        
            /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
        
                public function show($id)
                {
                    $Onepoint_merchant = Merchant::find($id);
                    return view("back.Onepoint_merchant.show",compact("Onepoint_merchant"));
                }
            

            
                /**
                 * Show the form for editing the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
            
                public function edit($id)
                {
                    $Onepoint_merchant = Merchant::find($id);
                    return view("back.Onepoint_merchant.edit",compact("Onepoint_merchant"));
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
                     // Validasi input
                     $request->validate([
                         'merchant_name' => 'required|string|max:255',
                         'label' => 'required|string|max:255',
                         'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                         'url' => 'required|url|max:255',
                     ]);
                 
                     // Ambil data merchant berdasarkan ID
                     $onpointMerchant = Merchant::findOrFail($id);
                 
                     // Mengambil semua input
                     $input = $request->all();
                 
                     // Mengelola file gambar jika diunggah
                     if ($request->hasFile('image')) {
                         // Menghapus gambar lama jika ada
                         if ($onpointMerchant->image) {
                            $oldImagePath = public_path('merchant/' . $onpointMerchant->image);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                 
                         // Mengunggah gambar baru
                         $image = $request->file('image');
                         $imageName = time() . '.' . $image->getClientOriginalExtension();
                         $image->move(public_path('merchant'), $imageName);
                         $input['image'] = $imageName;
                     }
                 
                     // Memperbarui record di database
                     $onpointMerchant->update($input);
                 
                     // Mengarahkan kembali ke index dengan pesan sukses
                     return redirect()->route("onepoint_merchant.index")
                         ->with("success", "Onepoint_merchant updated successfully");
                 }
                 
            

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
            
                public function destroy($id)
                {
                    $Onepoint_merchant = Merchant::find($id);                   
                    $input["deleted"] =  'true';
                    $Onepoint_merchant->update($input);        
                  
                    return redirect()->route("onepoint_merchant.index")
                    ->with("success","Onepoint_merchant deleted successfully");            
                }
            }
        
        ?>