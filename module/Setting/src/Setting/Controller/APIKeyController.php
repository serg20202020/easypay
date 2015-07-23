<?php
namespace Setting\Controller;


use Zend\View\Model\ViewModel;
use Setting\Form\APIKeySettingForm;
use Zend\Config\Config;

/**
 * APIKeyController
 *
 * @author
 *
 * @version
 *
 */
class APIKeyController extends BaseSettingController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('API Key setting'));
        
        $form = new APIKeySettingForm();
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
            
            // Validate the form
            if ($form->isValid()) {
                $config = new Config(array('apikey'=>$post_data['key']));
                $this->generateConfigFile($config);
            }
        }else{
           $form->setData(array('key'=>$this->getAPIKey()));
        }
        
        
        $view_page = new ViewModel(array('form'=>$form));
        
        $view_menu = new ViewModel();
        $view_menu->setTemplate('setting/common/menu');
        $view_page->addChild($view_menu,'menu');
        
        return $view_page;
    }
    
    private function generateConfigFile(Config $new_config){
    
        $config_file = 'config/autoload/local.php';
    
        // If there is a config file at that path, so merge the configuration to it.
        $config = array();
        if (file_exists($config_file)) $config = include $config_file;
    
        $reader = new Config($config);
        $reader->merge($new_config);
    
        $writer = new \Zend\Config\Writer\PhpArray();
        $writer->toFile($config_file, $reader);
    
    }
    
    private function getAPIKey() {
        
        $apikey = '';
        
        $config_file = 'config/autoload/local.php';
        $config = array();
        if (file_exists($config_file)) $config = include $config_file;
    
        $reader = new Config($config);
        if (!empty($reader->apikey)) $apikey = $reader->apikey;
        
        return $apikey;
        
    }
}