<?php
namespace GDO\Recovery;

use GDO\Core\Module;
use GDO\Date\GDT_Duration;
use GDO\Form\GDT_Form;
use GDO\Type\GDT_Checkbox;
use GDO\UI\GDT_Button;
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
			GDT_Checkbox::make('recovery_captcha')->initial('1'),
			GDT_Duration::make('recovery_timeout')->initial(3600),
		);
	}
	public function cfgCaptcha() { return $this->getConfigValue('recovery_captcha'); }
	public function cfgTimeout() { return $this->getConfigValue('recovery_timeout'); }
	
	#############
	### Hooks ###
	#############
	/**
	 * Hook login form with link to recovery.
	 * @param GDT_Form $form
	 */
	public function hookLoginForm(GDT_Form $form)
	{
	    $this->hookRegisterForm($form);
	}
	
// 	public function hookGuestForm(GDT_Form $form)
// 	{
// // 	    $this->hookRegisterForm($form);
// 	}
	
	/**
	 * Hook register form with link to recovery.
	 * @param GDT_Form $form
	 */
	public function hookRegisterForm(GDT_Form $form)
	{
	    $form->addField(GDT_Button::make('btn_recovery')->href(href('Recovery', 'Form')));
	}
}