<?php
        namespace App\Http\Controllers\Back\Onepoint_member;
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use App\Models\Member;
        use Illuminate\Support\Facades\DB;
        use Hash;
        use App\Models\Claim_uniquecode;
        use Illuminate\Support\Arr;

        class GetLogClaimUniquecodeMemberController extends Controller
        {
            //App\Http\Controllers\Back\Onepoint_member\GetLogClaimUniquecodeMemberController
        
            public function index(Request $request, $idmember)
            {
                $reslogclaimuniquecode = DB::select('select lcu.id_uniquecode , sum(lcu.point) as point from onepoint_log_claim_uniquecode as lcu
                left join onepoint_uniquecode as u on lcu.id_uniquecode = u.kode where lcu.id_member = '.$idmember.' group by lcu.id_uniquecode');
                // dump($idmember);
                // dd($reslogclaimuniquecode);
                $resmember = DB::select('select * from onepoint_member where id='.$idmember);

                return view("back.Onepoint_member.log_claim_uniquecode.list_log_claim_uniquecode",compact('reslogclaimuniquecode','idmember', 'resmember'))
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

             public function store(Request $request)
             {
                
                 $input = $request->all();
                 $Onepoint_voucher = Claim_uniquecode::create($input);
                 return redirect()->route("logclaimuniquecodemember.index", ['idmember' => $request->id_member])
                 ->with("success","Onepoint_voucher created successfully");
             
             }

             public function show($id)
            {
                $Onepoint_voucher = Claim_uniquecode::find($id);
                return view("back.Onepoint_member.log_claim_uniquecode.show",compact("Onepoint_voucher"));
            }

            public function edit($id)
            {
                $Onepoint_voucher = Claim_uniquecode::find($id);
                return view("back.Onepoint_member.log_claim_uniquecode.edit",compact("Onepoint_voucher"));
            }

            public function update(Request $request, $id)
            {
            
                $input = $request->all();
                $Onepoint_voucher = Claim_uniquecode::find($id);
                $Onepoint_voucher->update($input);
            
                return redirect()->route("logclaimuniquecodemember'.index")
                ->with("success","Onepoint_voucher updated successfully");
            
            }

            public function destroy($id)
            {
                $Onepoint_voucher = Claim_uniquecode::find($id);                   
                $input["deleted"] =  'true';
                $Onepoint_voucher->update($input);

                return redirect()->route("logclaimuniquecodemember'.index")
                ->with("success","Onepoint_voucher deleted successfully");
            
            }
        
        

        }
        
    ?>