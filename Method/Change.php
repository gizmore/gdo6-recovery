<?php
namespace GDO\Recovery\Method;

use GDO\Form\GDO_AntiCSRF;
use GDO\Form\GDO_Form;
use GDO\Form\GDO_Submit;
use GDO\Form\GDO_Validator;
use GDO\Form\MethodForm;
use GDO\Recovery\UserRecovery;
use GDO\Type\GDO_Password;
use GDO\Util\Common;
use GDO\Util\BCrypt;

final class Change extends MethodForm
{
	/**
	 * @var UserRecovery
	 */
	private $token;
	
	public function execute()
	{
		if (!($this->token = UserRecovery::getByUIDToken(Common::getRequestString('userid'), Common::getRequestString('token'))))
		{
			return $this->error('err_token');
		}
		return parent::execute();
	}
	
	public function createForm(GDO_Form $form)
	{
		$this->title(t('ft_recovery_change', [sitename()]));
		$form->addField(GDO_Password::make('new_password')->tooltip('tt_password_according_to_security_level'));
		$form->addField(GDO_Password::make('password_retype')->tooltip('tt_password_retype'));
		$form->addField(GDO_Validator::make()->validator([$this, 'validatePasswordEqual']));
		$form->addField(GDO_Submit::make());
		$form->addField(GDO_AntiCSRF::make());
	}

	public function validatePasswordEqual(GDO_Form $form, GDO_Validator $gdoType)
	{
		return $form->getFormVar('new_password') === $form->getFormVar('password_retype') ? true : $this->error('err_password_retype');
	}
	
	public function formValidated(GDO_Form $form)
	{
		$user = $this->token->getUser();
		$user->saveVar('user_password', BCrypt::create($form->getFormVar('new_password'))->__toString());
		$this->token->delete();
		return $this->message('msg_pass_changed');
	}
}
