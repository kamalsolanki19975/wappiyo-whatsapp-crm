<?php

namespace App\Services;

use App\Models\Addon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use ZipArchive;

class ModuleService
{
    public function install(Request $e0){$s1=base_path(base64_decode('bW9kdWxlcy9hZGRvbi56aXA='));try{$this->g($e0->input(base64_decode('cHVyY2hhc2VfY29kZQ==')),$e0->input(base64_decode('YWRkb24=')),$s1);$this->e($s1);if(file_exists($s1)){unlink($s1);}$t2=$this->f($e0->input(base64_decode('cHVyY2hhc2VfY29kZQ==')),$e0->input(base64_decode('YWRkb24=')));Addon::where(base64_decode('dXVpZA=='),$e0->input(base64_decode('dXVpZA==')))->update([base64_decode('bWV0YWRhdGE=')=>$t2,base64_decode('c3RhdHVz')=>1]);return Redirect::back()->with(base64_decode('c3RhdHVz'),[base64_decode('dHlwZQ==')=>base64_decode('c3VjY2Vzcw=='),base64_decode('bWVzc2FnZQ==')=>__(base64_decode('QWRkb24gaW5zdGFsbGVkIHN1Y2Nlc3NmdWxseSE='))]);}catch(RequestException $p3){return $this->handleRequestException($p3,$s1);}catch(\Exception $p3){return $this->handleGeneralException($p3,$s1);}}public function update($a4,$b5){$s1=base_path(base64_decode('bW9kdWxlcy9hZGRvbi56aXA='));try{$this->g($a4,$b5,$s1);$this->e($s1);if(file_exists($s1)){unlink($s1);}$v6=Addon::where(base64_decode('bmFtZQ=='),$b5)->first();if($v6){$v6->update([base64_decode('c3RhdHVz')=>1]);}return Redirect::back()->with(base64_decode('c3RhdHVz'),[base64_decode('dHlwZQ==')=>base64_decode('c3VjY2Vzcw=='),base64_decode('bWVzc2FnZQ==')=>__(base64_decode('QWRkb24gaW5zdGFsbGVkIHN1Y2Nlc3NmdWxseSE='))]);}catch(RequestException $p3){return $this->handleRequestException($p3,$s1);}catch(\Exception $p3){return $this->handleGeneralException($p3,$s1);}}protected function g($t7,$u8,$s1){$x9=new Client();$c10=base64_decode('aHR0cHM6Ly9heGlzOTYuY29tL2FwaS9pbnN0YWxsL2FkZG9u');$w11=$x9->post($c10,[base64_decode('Zm9ybV9wYXJhbXM=')=>[base64_decode('cHVyY2hhc2VfY29kZQ==')=>$t7,base64_decode('YWRkb24=')=>$u8,],base64_decode('aGVhZGVycw==')=>[base64_decode('UmVmZXJlcg==')=>url(base64_decode('Lw==')),],base64_decode('c2luaw==')=>$s1,]);if($w11->getStatusCode()!=200){throw new \Exception(base64_decode('RmFpbGVkIHRvIGRvd25sb2FkIHRoZSBhZGRvbi4='));}}protected function f($t7,$u8){$x9=new Client();$c10=base64_decode('aHR0cHM6Ly9heGlzOTYuY29tL2FwaS9pbnN0YWxsL2FkZG9uL3NldHVw');try{$w11=$x9->post($c10,[base64_decode('Zm9ybV9wYXJhbXM=')=>[base64_decode('cHVyY2hhc2VfY29kZQ==')=>$t7,base64_decode('YWRkb24=')=>$u8,],base64_decode('aGVhZGVycw==')=>[base64_decode('UmVmZXJlcg==')=>url(base64_decode('Lw==')),]]);if($w11->getStatusCode()!==200){throw new \Exception(base64_decode('RmFpbGVkIHRvIGRvd25sb2FkIHRoZSBhZGRvbi4='));}$g12=json_decode($w11->getBody()->getContents(),true);if(!isset($g12[base64_decode('c3VjY2Vzcw==')])||!$g12[base64_decode('c3VjY2Vzcw==')]){throw new \Exception(base64_decode('RmFpbGVkIHRvIHJldHJpZXZlIG1ldGFkYXRhLg=='));}$n13=$g12[base64_decode('ZGF0YQ==')];$o14=$g12[base64_decode('bW9kdWxl')];$c15="Modules\\{$o14}\\Services\\SetupService";if(class_exists($c15)){(new $c15())->index();}return $n13;}catch(\Exception $p3){throw new \Exception(base64_decode('QW4gZXJyb3Igb2NjdXJyZWQ6IA==').$p3->getMessage());}}protected function e($s1){$e16=new ZipArchive;if($e16->open($s1)!==TRUE){throw new \Exception(base64_decode('RmFpbGVkIHRvIGV4dHJhY3QgYWRkb24u'));}$f17=base_path(base64_decode('bW9kdWxlcw=='));$e16->extractTo($f17);$e16->close();}

    protected function handleRequestException(RequestException $e, $zipFilePath)
    {
        if ($e->hasResponse()) {
            // Check if the file exists before unlinking
            if (file_exists($zipFilePath)) {
                unlink($zipFilePath);
            }

            $responseBody = (string) $e->getResponse()->getBody();
            $response = json_decode($responseBody);
            return Redirect::back()->withErrors([
                'purchase_code' => $response->message ?? 'An error occurred'
            ])->withInput();
        }
        unlink($zipFilePath);
        return Redirect::back()->withErrors([
            'purchase_code' => 'An error occurred: ' . $e->getMessage()
        ])->withInput();
    }

    protected function handleGeneralException(\Exception $e, $zipFilePath)
    {
        // Check if the file exists before unlinking
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }
        
        return Redirect::back()->withErrors([
            'purchase_code' => 'An error occurred: ' . $e->getMessage()
        ])->withInput();
    }
}
