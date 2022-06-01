<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\model\logins;
use App\model\User;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use Session;
use Auth;
use DB;
use Config;

class UserController extends Controller
{
    public function signup() {
        $usersTable = new User;
        $getrow  = $usersTable->where(['name' => $_POST['username'], 'email' => $_POST['email']])->get();

        if(count(json_decode($getrow)) > 0) {
            Session::flash('error', "This user has already exist.");
            return redirect()->back();
        }
        
        if((int)$_POST['community_id'] == 0) {
            $_POST['community_id'] = (int)$_POST['community_id'] + 1;
        }

        $user = array(
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'position' => $_POST['position'],
            'community_id' => $_POST['community_id'],
            'company_id' => auth()->user()->company_id,
            'leveledit' => $_POST['leveledit'],
            'levelreport' => $_POST['levelreport'],
            'levelcompany' => $_POST['levelcompany'],
            'leveluser' => $_POST['leveluser'],
            'leveladd' => $_POST['leveladd'],
            'levelreportm' => $_POST['levelreportm'],
            'password' => Hash::make($_POST['password']),
            'active' => '0',
            'username' => $_POST['username'],
            'created_date' => date("Y-m-d"),
            'last_login' => date("Y-m-d")
        );

        $usersTable->insert($user);

        $id = json_decode($usersTable->where(['email' => $_POST['email'], 'name' => $_POST['name']])->get())[0]->id;

        Session::flash('result', 'Login Success!');
        if($_POST['checkthisonlyadd']) {
            return redirect('/usermanage');
        }
        return redirect('/');
    }

    public function updatePass() {
        //$this->checkDB();
        // $salt = $this->get_new_salt();
        // $password = $this->encryptPassword($_POST['changePass'], $salt);
        $password = Hash::make($_POST['changePass']);
        DB::table('users')->where('id', $_POST['mainId'])->update(['password' => $password]);
        return redirect('/usermanage');
    }

    public function changepass() {
        // $this->checkDB();
        // $salt = $this->get_new_salt();
        // $password = $this->encryptPassword($_POST['changePass'], $salt);
        $password = Hash::make($_POST['changePass']);
        Session::put('session', $_POST['username']. ','. $password);
        DB::table('users')->where('id', $_POST['mainId'])->update(['password' => $password]);
        return redirect('/profile');
    }

    public function changeStatus() {
        // $this->checkDB();
        DB::table('users')->where('id', $_POST['id'])->update(['active' => $_POST['statu']]);
        return redirect('/usermanage');
    }

    public function update() {
        $user = array(
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'position' => $_POST['position'],
            'community_id' => $_POST['community_id'],
            'leveledit' => $_POST['leveledit'],
            'levelreport' => $_POST['levelreport'],
            'levelcompany' => $_POST['levelcompany'],
            'leveladd' => $_POST['leveladd'],
            'leveluser' => $_POST['leveluser'],
            'levelreportm' => $_POST['levelreportm'],
        );
        DB::table('users')->where('id', $_POST['mainId'])->update($user);

        return redirect('/usermanage');
    }

    // public function encryptPassword($password, $hashsalt) {
    //     // $password = $_POST['password']
    //     // $hashsalf =

    //     $HASH_SALT_LENGTH = 10;
    //     $HASH_KEY = "9169779b65ca061622d77f6a12c49a36eb3c3110efa8fd508d1c0c3b42e7f694";
    //     $HASH_ITERATION = 160;
    //     $HASH_ALGO = "sha256";

    //     for($i=0;$i<$HASH_ITERATION;$i++){
    //         $password = hash_hmac($HASH_ALGO, $password . $hashsalt, $HASH_KEY);
    //     }
    //     return $password;
    // }

    // private function get_new_salt(){
	// 	$HASH_SALT_LENGTH = 10;
	// 	return substr(sha1(rand()), 0, $HASH_SALT_LENGTH);
    // }

}
