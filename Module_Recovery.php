<?php
namespace GDO\Recovery;

use GDO\Core\Module;
use GDO\Date\GDO_Duration;
use GDO\Form\GDO_Form;
use GDO\Type\GDO_Checkbox;
use GDO\UI\GDO_Button;
/**
 * Password recovery module.
 *
 * @author gizmore
 * @version 5.0
 * @since 1.0
 */
class Module_Recovery extends Module
{
	##############
	### Module ###
	##############
	public function isCoreModule() { return true; }
	public function getClasses() { return array('GDO\Recovery\UserRecovery'); }
	public function onLoadLanguage() { $this->loadLanguage('lang/recovery'); }

	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDO_Checkbox::make('recovery_captcha')->initial('1'),
			GDO_Duration::make('recovery_timeout')->initial(3600),
		);
	}
	public function cfgCaptcha() { return $this->getConfigValue('recovery_captcha'); }
	public function cfgTimeout() { return $this->getConfigValue('recovery_timeout'); }
	
	#############
	### Hooks ###
	#############
	/**
	 * Hook login form with link to recovery.
	 * @param GDO_Form $form
	 */
	public function hookLoginForm(GDO_Form $form)
	{
	    $this->hookRegisterForm($form);
	}
	
// 	public function hookGuestForm(GDO_Form $form)
// 	{
// // 	    $this->hookRegisterForm($form);
// 	}
	
	/**
	 * Hook register form with link to recovery.
	 * @param GDO_Form $form
	 */
	public function hookRegisterForm(GDO_Form $form)
	{
	    $form->addField(GDO_Button::make('btn_recovery')->href(href('Recovery', 'Form')));
	}
}
