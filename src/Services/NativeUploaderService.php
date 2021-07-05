<?php
/**
 * Description of NativeFileUploader.php.
 *
 * @package App\Services
 * @author 
 */

namespace App\Services;



/**
 * Class NativeFileUploader
 *
 * @package App\Services
 */
class NativeUploaderService
{
      /**
    * Upload file
    *
    * @param [type] $_directory
    * @param [type] $_indexFilename
    * @return void
    */
    public function upload ($_directory,$_indexFilename)
    {
//         dump($_directory);
//         dump($_indexFilename);
// die();
         
                $this->options = array(
                    //'script_url' => $this->getFullUrl().'/',            
                    'param_name' => $_indexFilename,
                    'uploads_dir' => $_directory,
                    'folder' => '',
                    'max_file_size' => null,
                    'accept_file_types' => '/.+$/i',
                    'allowed_extensions' => array('jpg', 'jpeg', 'png','gif','pdf','xls','ppt','docx','txt','rar','zip'),
                     );
           
            if ($_FILES) 
            {
                $this->options = array_replace_recursive($this->options, $_FILES);
                $_FILES = array_merge($this->options, $_FILES);
            }
            $upload = $_FILES[$this->options['param_name']];
            $tmp_name = $upload["tmp_name"];
            $fileName = $this->trim_file_name($upload["name"]);
            
            $size = $upload["size"];
            
            if(array_key_exists('allowed_extensions', $_FILES)){
                $allowedExtensions = $_FILES['allowed_extensions'];
                // Build a regular expression like /(\.gif|\.jpg|\.jpeg|\.png)$/i
                $allowedExtensionsRegex = '/(' . implode('|', array_map(function($extension) { return '\.' . $extension; }, $allowedExtensions)) . ')$/i';
                $this->options['accept_file_types'] = $allowedExtensionsRegex;
            }
            if($upload["error"])
            {
                $file->error = $upload["error"] ;
                
            }
            elseif($this->options['max_file_size'] && $this->options['max_file_size']< $size) 
            {
                $file->error = 'maxFileSize';
            }
            elseif (!preg_match($this->options['accept_file_types'], $fileName)) 
            {
                $file->error = 'acceptFileTypes';
            }
            else
            {
                move_uploaded_file($tmp_name, $_directory.'/'.$fileName);
            }
            return $fileName;
    }

    /**
    * Upload  multiples files
    *
    * @param [type] $directory
    * @param [type] $_indexFileName
    * @return void
    */
    public function uploadMultipleFile($directory,$_indexFileName, $_retainName = false)
    {
        $file_array = $this->reArrayFiles($_FILES[$_indexFileName]);
        $fileNames = [];
        
        foreach ($file_array as $file)
        {
            
            if ($file['error'] == 0) {
                $extension = ['jpg', 'jpeg', 'png','gif','pdf','xls','ppt','docx','txt','rar','zip'];
                $fileExt = explode('.',$file['name']);
                $fileExt = end($fileExt);

                //if(in_array($fileExt,$extension))
                //{
                    if(!empty($file["name"])){
                        $fileName = $this->trim_file_name($file["name"] ,$_retainName);
                        $fileNames[] = $fileName;
                        $tmp_name = $file["tmp_name"];
                        $upload = move_uploaded_file($tmp_name, $directory.'/'.$fileName);
                    }
                    
                   
                //}
            }
        }

        return $fileNames; 
    
    }

    

    /**
     * Traitement array file
     * @param type $_filesPost
     * @return type
     */
    public function reArrayFiles($_filesPost)
    {
        
        $files = [];
        $fileCount = count($_filesPost['name']);
        $fileKeys = array_keys($_filesPost);
        
        for ($i=0; $i <$fileCount ; $i++) { 
           foreach($fileKeys as $key)
           {
                $files[$i][$key] = $_filesPost[$key][$i];
           }
        }
        
        return $files;
    }


    /**
    * Delete space filename and create new filename
    *
    * @param [type] $name
    * @return void
    */
    protected function trim_file_name($name, $_retainName = false) 
    {
        $file_name = trim(basename(stripslashes($name)), ".\x00..\x20");
        $extFile = pathinfo($file_name, PATHINFO_EXTENSION);
        $fileName = md5(uniqid()).'.'.$extFile;
        if($_retainName == true){
            $fileName = $name;
        }
         
        return $fileName;
        
    }


     /**
     * Create directory file 
     *
     * @param [type] $path
     * @return void
     */
    public function makePath($path)
	{
       // $dir = pathinfo($path , PATHINFO_DIRNAME);
        
        if (is_dir($path))
        {
           return true;
        }
        else 
        { 
            if(mkdir($path,0777,true))
            {
                return true;
            }
            else{
                return false;
            }
            
        }
		return false;
    }




}