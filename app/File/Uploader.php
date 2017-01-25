<?php

class File_Uploader
{
    protected $_maximumFileSize = 15728640; // 15Mb

    protected $_uploadPath = './';


    protected function _getHashedFileName($fileName)
    {
        $tmpName = md5($fileName . mt_rand());
        return $tmpName;
    }

    protected function _prepareResponse($fileName, $path, $size, $error = '')
    {
        return array(
            'error'     => $error,
            'filename'  => $fileName,
            'path'      => $path,
            'size'      => sprintf('%.2fKb', array_sum($size) / 1024),
            'dt'        => date('Y-m-d H:i:s')
        );
    }

    public function __construct(array $params = array())
    {
        if ($params) {
            if (isset($params['max_file_size']) && $params['max_file_size'] > 0) {
                $this->_maximumFileSize = (int) $params['max_file_size'];
            }
            if (isset($params['upload_path']) && $params['upload_path'] != '') {
                $this->_uploadPath = $params['upload_path'];
            }
        }

        $this->checkUploadDirectoryExists();

        return $this;
    }

    public function checkUploadDirectoryExists()
    {
        if (!file_exists($this->_uploadPath)) {
            mkdir($this->_uploadPath, 0777, true);
        }

        return $this;
    }

    public function upload()
    {
        $fileElementName = $_POST['file_element'];
        $error           = array();
        $fileNames       = $_FILES[$fileElementName]['name'];
        $newFileName     = array();
        $size            = array();

        foreach ($fileNames as $fileId => $name) {
            if (!empty($_FILES[$fileElementName]['error'][$fileId])) {
                switch($_FILES[$fileElementName]['error'][$fileId]) {
                    case '1':
                        $error[$fileId][] = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                        break;
                    case '2':
                        $error[$fileId][] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                        break;
                    case '3':
                        $error[$fileId][] = 'The uploaded file was only partially uploaded.';
                        break;
                    case '4':
                        $error[$fileId][] = 'No file was uploaded.';
                        break;
                    case '6':
                        $error[$fileId][] = 'Missing a temporary folder.';
                        break;
                    case '7':
                        $error[$fileId][] = 'Failed to write file to disk.';
                        break;
                    case '8':
                        $error[$fileId][] = 'File upload stopped by extension.';
                        break;
                    case '999':
                    default:
                        $error[$fileId][] = 'No error code available.';
                }
            } elseif (empty($_FILES[$fileElementName]['tmp_name'][$fileId])
                || $_FILES[$fileElementName]['tmp_name'][$fileId] == 'none'
            ) {
                $error[$fileId][] = 'No file was uploaded.';
            } else {
                $size[$fileId] = @filesize($_FILES[$fileElementName]['tmp_name'][$fileId]);

                $newFileName[$fileId] = $this->_getHashedFileName($name) . '.patch';
                if (move_uploaded_file(
                    $_FILES[$fileElementName]['tmp_name'][$fileId],
                    $this->_uploadPath . $newFileName[$fileId]
                )) {
                    $content = file_get_contents($this->_uploadPath . $newFileName[$fileId]);

                    $converter = new Patch_Converter();
                    $stripSh = (pathinfo($fileNames[$fileId], PATHINFO_EXTENSION) == 'sh');
                    $content = $converter->preparePatch($content, $stripSh);
                    if ($content) {
                        file_put_contents($this->_uploadPath . $newFileName[$fileId], $content);
                    } else {
                        @unlink($this->_uploadPath . $newFileName[$fileId]);
                        $error[$fileId][] = 'Sh script has incorrect format.';
                    }
                } else {
                    $error[$fileId][] = 'Unable to move uploaded file from tmp folder.';
                }
            }
        }

        return array(
            'result'        => $this->_prepareResponse($fileNames, $_POST['folder'], $size, $error),
            'new_file_name' => $newFileName
        );
    }
}
