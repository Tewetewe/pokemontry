<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;



class ApiController extends Controller
{
    public function register(Request $request){

        DB::beginTransaction();
        try{
            User::create([
                'name' => $request->name,
                'created_at' => date('Y-m-d H:i:s')
            ]);
    
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => "Success Register", 
            ]);
        }

        catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => 'Some error occurs.',
                            'error'     => $e->getMessage()
                ]);
        }

    }

    public function getDesc(Request $request){

        $name = strtolower($request->name);
        $response = Http::get('https://pokeapi.co/api/v2/pokemon/'.$name);

        if(($response != 'Not Found')){
            $desc = ucfirst($response['name']).' is an '.'<'.$response['types'][0]['type']['name'].'>'.
                ' type Pokemon with '.$response['weight'].' weight'.' and '.$response['height'].' height, here is a Picture of '.ucfirst($response['name']);
            
            $image = $response['sprites']['other']['official-artwork']['front_default'];

            $result = [
                "desc" => $desc,
                "photo" => $image,
            ];
        }

        else{
            $desc = 'Sorry we dont have information for '.'<'.$name.'>';
            $image = NULL;
            $result = [
                "desc" => $desc,
                "photo" => $image,
            ];
        }

        return $result;

        
    }

    public function getPhoto(Request $request){
        $name = $request->name;
        $image = Http::get('https://pokeapi.co/api/v2/pokemon/'.$name)['sprites']['other']['official-artwork']['front_default'];
        return $image;
    }
}
