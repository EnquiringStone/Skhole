<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 05-May-16
 * Time: 18:38
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseResourcesValidator extends Validator
{
    public function validateGoogleDriveUrl($googleDriveUrl)
    {
        if(ValidatorHelper::isStringNullOrEmpty($googleDriveUrl))
            return;

        if(!ValidatorHelper::isGoogleDriveUrl($googleDriveUrl))
            throw new FrontEndException('course.resources.invalid.google.drive', $this->domain);
    }

    public function validateDropboxUrl($dropboxUrl)
    {
        if(ValidatorHelper::isStringNullOrEmpty($dropboxUrl))
            return;

        if(!ValidatorHelper::isDropboxUrl($dropboxUrl))
            throw new FrontEndException('course.resources.invalid.dropbox', $this->domain);
    }
}