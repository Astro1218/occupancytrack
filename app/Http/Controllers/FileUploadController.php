<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\File;


class FileUploadController extends Controller
{
  public function createForm(){
    return view('file-upload');
  }

  public function fileUpload(Request $req){
      $fileinfo = explode('.', $_FILES['file']['name']);
      if($fileinfo[count($fileinfo) - 1] != 'csv') {
        return back()->with('error','File is not csv file.');
      }
      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
      // Output: 54esmdr0qf
      substr(str_shuffle($permitted_chars), 0, 10);

      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $realFilename = 'csvfile-'.substr(str_shuffle($permitted_chars), 0, 16).'.csv';

      move_uploaded_file($_FILES['file']['tmp_name'], $realFilename);

      return redirect()->back();
   }

}
