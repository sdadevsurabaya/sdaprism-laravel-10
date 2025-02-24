<?php
        namespace App\Http\Controllers\Back\Onepoint_member;
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use App\Models\Claim_voucher;
        use Illuminate\Support\Facades\DB;
        use Illuminate\Support\Facades\Hash;
        use Illuminate\Support\Arr;

        class GetLogClaimVoucherMemberController extends Controller
        {
        
            public function index(Request $request, $idmember)
            {

               
                $reslogclaimvoucher = DB::select('select * from onepoint_log_claim_voucher as lcv
                inner join onepoint_voucher as v on lcv.id_onepoint_voucher = v.id where lcv.id_member = '.$idmember.' order by lcv.id desc');

                return view("back.Onepoint_member.log_claim_voucher.list_log_claim_voucher",compact('reslogclaimvoucher','idmember'))
                ->with("i", ($request->input("page", 1) - 1) * 5);

            }

              /**
             * Show the form for creating a new resource.
             *
             * @return \Illuminate\Http\Response
             */
        
             public function create($idmember)
             {

                $resvoucher = DB::select('select * from onepoint_voucher order by kode_voucher');

                 return view("back.Onepoint_member.log_claim_voucher.create_log_claim_voucher", compact('idmember', 'resvoucher'));
             }
         
         
         
             /**
              * Store a newly created resource in storage.
              *
              * @param  \Illuminate\Http\Request  $request
              * @return \Illuminate\Http\Response
              */
         
             public function store(Request $request)
             {
                
                 $input = $request->all();
                 $Onepoint_voucher = Claim_voucher::create($input);
                 return redirect()->route("logclaimvouchermember.index", ['idmember' => $request->id_member])
                 ->with("success","Onepoint_voucher created successfully");
             
             }

            /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
        
                 public function show($id)
                 {
                     $Onepoint_voucher = Claim_voucher::find($id);
                     return view("back.Onepoint_member.log_claim_voucher..show",compact("Onepoint_voucher"));
                 }
             
 
             
                 /**
                  * Show the form for editing the specified resource.
                  *
                  * @param  int  $id
                  * @return \Illuminate\Http\Response
                  */
             
                 public function edit($id)
                 {
                     $Onepoint_voucher = Claim_voucher::find($id);
                     return view("back.Onepoint_member.log_claim_voucher..edit",compact("Onepoint_voucher"));
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
                     $Onepoint_voucher = Claim_voucher::find($id);
                     $Onepoint_voucher->update($input);
                 
                     return redirect()->route("logclaimvouchermember'.index")
                     ->with("success","Onepoint_voucher updated successfully");
                 
                 }
             
 
                 /**
                  * Remove the specified resource from storage.
                  *
                  * @param  int  $id
                  * @return \Illuminate\Http\Response
                  */
             
                 public function destroy($id)
                 {
                     $Onepoint_voucher = Claim_voucher::find($id);                   
                     $input["deleted"] =  'true';
                     $Onepoint_voucher->update($input);
 
                     return redirect()->route("logclaimvouchermember'.index")
                     ->with("success","Onepoint_voucher deleted successfully");
                 
                 }

        }
        
    ?>