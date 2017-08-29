<?php
namespace GDO\Recovery\Method;

use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Validator;
use GDO\Form\MethodForm;
use GDO\Recovery\GDO_UserRecovery;
use GDO\Type\GDT_Password;
use GDO\Util\Common;
use GDO\Util\BCrypt;

final class Change extends MethodForm
{
	/**
	 * @var GDO_UserRecovery
	 */
	private $token;
	
	public function execute()
	{
		if (!($this->token = GDO_UserRecovery::getByUIDToken(Common::getRequestString('userid'), Common::getRequestString('token'))))
		{
			return $this->error('err_token');
		}
		return parent::execute();
	}
	
	public function createForm(GDT_Form $form)
	{
		$this->title(t('ft_recovery_change', [sitename()]));
		$form->addField(GDT_Password::make('new_password')->tooltip('tt_password_according_to_security_level'));
		$form->addField(GDT_Password::make('password_retype')->tooltip('tt_password_retype'));
		$form->addField(GDT_Validator::make()->validator([$this, 'validatePasswordEqual']));
		$form->addField(GDT_Submit::make());
		$form->addField(GDT_AntiCSRF::make());
	}

	public function validatePasswordEqual(GDT_Form $form, GDT_Validator $gdoType)
	{
		return $form->getFormVar('new_password') === $form->getFormVar('password_retype') ? true : $this->error('err_password_retype');
	}
	
	public function formValidated(GDT_Form $form)
	{
		$user = $this->token->getUser();
		$user->saveVar('user_password', BCrypt::create($form->getFormVar('new_password'))->__toString());
		$this->token->delete();
		return $this->message('msg_pass_changed');
	}
}
