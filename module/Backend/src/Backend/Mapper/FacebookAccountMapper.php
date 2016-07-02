<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Facebook\Facebook;
use Zend\Db\Adapter\Adapter;

class FacebookAccountMapper
{
    private $db;
    
    private $facebookConfig;

    public function __construct(Adapter $db, array $facebookConfig)
    {
        $this->db = $db;
        $this->facebookConfig = $facebookConfig;
    }

    /**
     * Retourne l'adresse email de l'utilisateur
     * 
     * @param string $accessToken
     * @return string
     */
    public function findEmail($accessToken)
    {        
        $facebook = new Facebook([
            'app_id' => $this->facebookConfig['applicationId'],
            'app_secret' => $this->facebookConfig['applicationSecret'],
            'default_graph_version' => 'v2.2',
        ]);
        
        try {
            // Création de la requete
            $response = $facebook->get('/me?fields=email', $accessToken);
            
            // Execution de la requete
            $data = $response->getGraphUser();
            
            if (isset($data['email'])) {
                return $data['email'];
            }
            else {
                return null;
            }
        }
        catch (Exception $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de l'adresse email d'un compte Facebook",
                null,
                $exception
            );
        }
    }

    /**
     * Retourne l'adresse email de l'utilisateur
     * 
     * @param string $accessToken
     * @return string
     */
    public function findAccountImage($accessToken)
    {        
        $facebook = new Facebook([
            'app_id' => $this->facebookConfig['applicationId'],
            'app_secret' => $this->facebookConfig['applicationSecret'],
            'default_graph_version' => 'v2.2',
        ]);
        
        try {
            // Création de la requete
            $response = $facebook->get('/me?fields=picture', $accessToken);
            
            // Execution de la requete
            $data = $response->getGraphUser();
            
            if (isset($data['picture'])) {
                return $data->getPicture()->getUrl();
            }
            else {
                return null;
            }
        }
        catch (Exception $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de l'image de profil d'un compte Facebook",
                null,
                $exception
            );
        }
    }
}
