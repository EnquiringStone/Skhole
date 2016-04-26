<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 13-Mar-16
 * Time: 21:54
 */

namespace AppBundle\Util;


use AppBundle\Entity\User;
use AppBundle\Exception\FrontEndException;
use Symfony\Component\HttpFoundation\File\File;

class FileHelper
{
    public static $maxSize = 3000000;

    public static $webDir = __DIR__ . '/../../../web/';

    public static $acceptedPictureExtensions = array('jpeg', 'jpg', 'png');

    private $user;
    private $name;
    private $absolutePath;
    private $relativePath;
    private $file;

    public function __construct(File $file = null, User $user = null, $prefix = '')
    {
        $this->user = $user;
        if($file != null)
        {
            $this->file = $file;
            $this->Validate($file);
            $this->generateName($file->guessExtension(), $prefix);
            $this->setRelativePath($user, $prefix);
            $this->setAbsolutePath();
        }
    }

    public function generateName($extension, $prefix = '')
    {
        $this->name = uniqid($prefix) .'.'. $extension;
    }

    public function setRelativePath(User $user = null, $subFolder = '')
    {
        $userDirectory = $user == null ? 'public' : $user->getUsername();

        $this->relativePath = $subFolder . '/' . $userDirectory . '/';
    }

    public function setAbsolutePath()
    {
        $this->absolutePath = FileHelper::$webDir . $this->relativePath;
    }

    public function getTotalPath()
    {
        return $this->absolutePath . $this->name;
    }

    public function remove()
    {
        if($this->file != null && $this->file->isWritable() && $this->file->isFile() && $this->absolutePath != null && $this->getName() != null)
            unlink($this->getTotalPath());
    }

    public function Validate(File $file, array $acceptedExtension = array())
    {
        if($file->getSize() > FileHelper::$maxSize)
            throw new FrontEndException('course.edit.picture.size', 'ajaxerrors');

        if($file->guessExtension() == null)
            throw new FrontEndException('course.edit.picture.unrecognized', 'ajaxerrors');

        if(!in_array($file->guessExtension(), sizeof($acceptedExtension) > 0 ? $acceptedExtension : FileHelper::$acceptedPictureExtensions))
            throw new FrontEndException('course.edit.picture.invalid.extension', 'ajaxerrors',
                array('%extensions%' => join(', ', sizeof($acceptedExtension) > 0 ? $acceptedExtension : FileHelper::$acceptedPictureExtensions)));
    }

    /**
     * @return mixed
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * @return mixed
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}