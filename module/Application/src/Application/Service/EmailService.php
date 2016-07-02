<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Service;

use Zend\Log\Logger;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer;

class EmailService
{
    /* @var $logger Logger */
    private $logger;
    
    /* @var $renderer Renderer */
    private $renderer;
    
    /* @var $mailerConfig array */
    private $mailerConfig;
    
    private $subject;
    private $toList;
    private $templateName;
    private $templateVariables = [];

    public function __construct($renderer, $logger, $mailerConfig)
    {
        $this->renderer = $renderer;
        $this->logger = $logger;
        $this->mailerConfig = $mailerConfig;
    }

    /**
     * Envoi de l'email configuré avec la méthode sendmail
     */
    public function send()
    {
        $mail = new Message();
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setTemplate('email/'.$this->templateName.'.phtml');
        $viewModel->setVariables($this->mailerConfig);
        $viewModel->setVariable('urlDomain', $this->mailerConfig['urlDomain']);
        $viewModel->setVariables($this->templateVariables);
        
        $render = $this->renderer->render($viewModel);
        
        $viewLayout = new ViewModel();
        $viewLayout->setTemplate('layout/email');
        $viewLayout->setVariable('content', $render);
        $viewLayout->setVariable('urlDomain', $this->mailerConfig['urlDomain']);
        
        $html = new Part($this->renderer->render($viewLayout));
        $html->type = 'text/html';
        $body = new MimeMessage();
        $body->setParts(array($html));

        $mail->setBody($body);
        $mail->setFrom($this->mailerConfig['fromEmailAddress'], $this->mailerConfig['fromName']);
        
        // Si une surcharge est présente dans les conf
        if (isset ($this->mailerConfig['toEmailAddress'])) {
            foreach ($this->mailerConfig['toEmailAddress'] as $email) {
                $mail->addTo($email);
            }
        }
        else {
            foreach ($this->toList as $toEmail => $toName) {
                $mail->addTo($toEmail, $toName);
            }
        }
        
        $mail->setSubject($this->subject);
        
        $transport = new Sendmail();
        $transport->send($mail);
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function addTo($toEmail, $toName = null)
    {
        $this->toList[$toEmail] = $toName;
        return $this;
    }

    /**
     * Permet de définir un template à appliquer à l'email
     * La valeur attendue est le nom du template sans l'extension, ni le dossier
     * parent 'email'.
     * 
     * @param string $templateName
     * @return \Application\Service\EmailService
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
        return $this;
    }

    /**
     * Permet de définir un tableau de variables pour la vue
     * Attend un tableau au format: ['nomDeLaVariable' => 'valeur']
     * 
     * @param array $templateVariables
     * @return \Application\Service\EmailService
     */
    public function setTemplateVariables(array $templateVariables)
    {
        $this->templateVariables = $templateVariables;
        return $this;
    }
}
