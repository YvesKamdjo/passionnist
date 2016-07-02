<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Eventviva\ImageResize;
use finfo;
use Zend\Mvc\Controller\AbstractActionController;

class ImageController extends AbstractActionController
{
    /**
     * Retourne l'image passée en parametre
     */
    public function imageAction()
    {
        $category = $this->params()->fromRoute('category');
        $imageId = $this->params()->fromRoute('idImage');
        $width = $this->params()->fromRoute('width');
        $height = $this->params()->fromRoute('height');
        $quality = $this->params()->fromRoute('quality', 90);
        $align = $this->params()->fromRoute('align', 'top');

        $alignPosition = [
            'top' => ImageResize::CROPTOP,
            'bottom' => ImageResize::CROPBOTTOM,
            'left' => ImageResize::CROPLEFT,
            'right' => ImageResize::CROPRIGHT,
            'center' => ImageResize::CROPCENTER,
        ];

        // Vérifie que la position est valide
        if(!in_array($align, array_keys($alignPosition))){
            return $this->getResponse()->setStatusCode(404);
        }

        // Vérifie que le type demandé est valide
        if(!in_array($category, [
            'account-image',
            'certificate',
            'job-service-image',
            'qualification',
            'salon-image',
        ])){
            return $this->getResponse()->setStatusCode(404);
        }

        $imageFilePath = 'data/' . $category . '/' . $imageId;

        // Récupère une image adaptée ou génère l'image
        if($width > 0 && $height > 0){
            $imageResizePath = 'data/' . $category . '/cache_'.$width.'x'.$height.'@'.$quality.'$'.$align;
            $imageResizeFilePath = $imageResizePath.'/' . $imageId;
            if(!file_exists($imageResizeFilePath)){
                if(!is_dir($imageResizePath)){
                    mkdir($imageResizePath);
                }
                $imageResize = new ImageResize($imageFilePath);
                $imageResize->crop($width,$height, true, $alignPosition[$align]);
                $imageResize->quality_jpg = $quality;
                $imageResize->save($imageResizeFilePath);
            }
            $imageFilePath = $imageResizeFilePath;
        }

        // Vérifie si le fichier existe
        if(!file_exists($imageFilePath)){
            return $this->getResponse()->setStatusCode(404);
        }

        // Si le fichier d'image est vide ou corrompu
        $imageFileContent = file_get_contents($imageFilePath);
        if ($imageFileContent == false) {
            return $this->getResponse()->setStatusCode(404);
        }

        // Récupère le mime type du document
        $finfo = new finfo(FILEINFO_MIME);
        $contentType = $finfo->file($imageFilePath);
        
        // Retourne la réponse HTTP
        $response = $this->getResponse();
        $response->setContent($imageFileContent);
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => $contentType
        ));
        return $response;
    }
}