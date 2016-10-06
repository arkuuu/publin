<?php

namespace publin\src;

use finfo;
use InvalidArgumentException;
use publin\config\Config;
use publin\src\exceptions\FileHandlerException;
use publin\src\exceptions\FileInvalidTypeException;
use publin\src\exceptions\FileNotFoundException;
use publin\src\exceptions\FileNotMovableException;
use publin\src\exceptions\FileTooBigException;

/**
 * Class FileHandler
 *
 * @package publin\src
 */
class FileHandler
{

    /**
     * @param array $file
     *
     * @return string
     * @throws FileHandlerException
     * @throws FileInvalidTypeException
     * @throws FileNotMovableException
     * @throws FileTooBigException
     */
    public static function upload(array $file)
    {
        if (!(isset($file['name'])
            && isset($file['type'])
            && isset($file['tmp_name'])
            && isset($file['error'])
            && isset($file['size']))
        ) {
            throw new InvalidArgumentException('no valid file upload');
        }

        if ($file['error'] === UPLOAD_ERR_OK) {

            $extension = self::getFileExtensionIfAllowed($file['tmp_name']);
            if ($extension) {
                $name = self::generateFileName();
                $size = $file['size'];
                $success = move_uploaded_file($file['tmp_name'], Config::FILE_PATH.$name);

                if (!$success) {
                    throw new FileNotMovableException('Error while uploading file to server');
                }

                // TODO: chmod -x
                return array(
                    'name'      => $name,
                    'extension' => $extension,
                    'size'      => $size,
                );
            } else {
                throw new FileInvalidTypeException('Disallowed file type');
            }
        } else if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            throw new FileHandlerException('No file given');
        } else if ($file['error'] === UPLOAD_ERR_INI_SIZE
            || $file['error'] === UPLOAD_ERR_FORM_SIZE
            || $file['size'] > Config::FILE_MAX_SIZE
        ) {
            throw new FileTooBigException('File is too big');
        } else {
            // TODO: change this error message
            throw new FileHandlerException('Unknown error #'.$file['error']);
        }
    }


    /**
     * @param $file
     *
     * @return bool
     */
    private static function getFileExtensionIfAllowed($file)
    {
        $mime_type = self::getMimeType($file);
        $extension = self::getAllowedTypes();
        if (isset($extension[$mime_type])) {
            return $extension[$mime_type];
        } else {
            return false;
        }
    }


    /**
     * @param $file
     *
     * @return string
     */
    private static function getMimeType($file)
    {
        $file_info = new finfo();
        $mime_type = $file_info->file($file, FILEINFO_MIME_TYPE);

        return $mime_type;
    }


    /**
     * @return array
     */
    public static function getAllowedTypes()
    {
        return array(
            'application/pdf'                                                           => '.pdf',
            'application/zip'                                                           => '.zip',
            'application/gzip'                                                          => '.gz',
            'application/x-tar'                                                         => '.tar',
            'application/x-gtar'                                                        => '.gtar',
            'application/msword'                                                        => '.doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => '.docx',
            'application/mspowerpoint'                                                  => '.ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx',
            'text/plain'                                                                => '.txt',
            'image/png'                                                                 => '.png',
            'image/jpeg'                                                                => '.jpg',
        );
    }


    /**
     * @return string
     */
    private static function generateFileName()
    {
        do {
            $file_name = uniqid(Config::FILE_NAME_PREFIX, true);
        } while (file_exists(Config::FILE_PATH.$file_name));

        return $file_name;
    }


    /**
     * @param        $file_name
     * @param string $download_name
     *
     * @throws FileNotFoundException
     */
    public static function download($file_name, $download_name = 'file')
    {
        $file_name = pathinfo($file_name, PATHINFO_BASENAME);
        $file = Config::FILE_PATH.$file_name;

        if (file_exists($file)) {

            header('Content-Type: '.self::getMimeType($file));
            header('Content-Disposition: inline; filename="'.$download_name.'"');
            header('Expires: 0'); // TODO: check all this
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            readfile($file);
            exit;
        } else {
            throw new FileNotFoundException($file);
        }
    }


    /**
     * @param $file_name
     *
     * @return bool
     * @throws FileHandlerException
     * @throws FileNotFoundException
     */
    public static function delete($file_name)
    {
        $file_name = pathinfo($file_name, PATHINFO_BASENAME);
        $file = Config::FILE_PATH.$file_name;

        if (file_exists($file)) {
            if (unlink($file)) {
                return true;
            } else {
                throw new FileHandlerException('file could not be deleted');
            }
        } else {
            throw new FileNotFoundException($file);
        }
    }
}
