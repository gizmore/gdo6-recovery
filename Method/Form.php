<?php
namespace GDO\Recovery\Method;

use GDO\Captcha\GDO_Captcha;
use GDO\Form\GDO_AntiCSRF;
use GDO\Form\GDO_Form;
use GDO\Form\GDO_Submit;
use GDO\Form\MethodForm;
use GDO\Mail\Mail;
use GDO\Recovery\Module_Recovery;
use GDO\Recovery\UserRecovery;
use GDO\Template\Error;
use GDO\Template\Message;
use GDO\UI\GDO_Link;
use GDO\User\GDO_Username;
use GDO\User\User;
/**
 * Request Password Forgotten Token.
 * Disabled when DEBUG_MAIL is on :)
 * @author gizmore
 */
final class Form extends MethodForm
{
    public function isUserRequired() { return false; }
	public function isEnabled() { return (!GWF_DEBUG_EMAIL); }
	
	public function createForm(GDO_Form $form)
	{
		$form->addField(GDO_Username::make('login')->tooltip('tt_recovery_login')->exists());
		if (Module_Recovery::instance()->cfgCaptcha())
		{
			$form->addField(GDO_Captcha::make());
		}
		$form->addField(GDO_AntiCSRF::make());
		$form->addField(GDO_Submit::make());
	}
	
	public function formValidated(GDO_Form $form)
	{
		$user = $form->getField('login')->gdo;
		if (!$user->hasMail())
		{
			return Error::error('err_recovery_needs_a_mail', [$user->displayName()]);
		}
		$this->sendMail($user);
		return Message::message('msg_recovery_mail_sent');
	}

	private function sendMail(User $user)
	{
		$token = UserRecovery::blank(['pw_user_id' => $user->getID()])->replace();
		$link = GDO_Link::anchor(url('Recovery', 'Change', "&userid={$user->getID()}&token=".$token->getToken()));

		$mail = Mail::botMail();
		$mail->setSubject(t('mail_subj_recovery', [sitename()]));
		$body = [$user->displayName(), sitename(), $link];
		$mail->setBody(t('mail_subj_body', $body));
		$mail->sendToUser($user);
	}
}
