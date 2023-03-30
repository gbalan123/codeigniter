<?php

namespace App\Controllers;
use App\Controllers\BaseController;

class InternalPDF extends BaseController
{
    public $Engage = 'public/engage/';

    public function InternalPDFUpload()
    {
        if($this->request->getMethod() == 'get')
        {
            $data = array('list_files' => []);
            foreach(scandir($this->Engage,1) as $file) {
                if($file != ".." && $file != "." && (stristr(strtolower($file), '.pdf') || stristr(strtolower($file), '.html')))
                $data['list_files'][] = base_url("public/engage/$file");
            }
            return view('engage_filedownload', $data);
        }
        else if($this->request->getMethod() == 'post')
        {
            $rules =['cefr_ability' => 'uploaded[cefr_ability]|max_size[cefr_ability,10000]|ext_in[cefr_ability,pdf,html]'];
            if (!$this->validate($rules))
                return redirect()->to('InternalPDF/InternalPDFUpload')->with('error', (\Config\Services::validation())->listErrors());

            $file_details = $this->request->getFile('cefr_ability');
            $newName = $file_details->getClientName();
            // $this->PDF_FileDelete(); // For Delete old files

            if($file_details->move($this->Engage, $newName))
                return redirect()->to('InternalPDF/InternalPDFUpload')->with('success', 'success');
            else
                return redirect()->to('InternalPDF/InternalPDFUpload')->with('error', 'something went wrong! please try again.');
        }
    }

    public function PDF_FileDelete()
    {
        $remove_lists = scandir($this->Engage,1);
        foreach($remove_lists as $remove_list) {
            if($remove_list != ".." && $remove_list != ".") {
                unlink($this->Engage."/$remove_list");
            }
        }
        return true;
    }
}