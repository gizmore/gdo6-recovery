<?php
namespace GDO\Recovery\Method;

use GDO\Captcha\GDT_Captcha;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Mail\Mail;
use GDO\Recovery\Module_Recovery;
use GDO\Recovery\GDO_UserRecovery;
use GDO\Template\Error;
use GDO\Template\Message;
use GDO\UI\GDT_Link;
use GDO\User\GDT_Username;
use GDO\User\GDO_User;
use GDO\Net\GDT_IP;
/**
 * Request Password Forgotten Token.
 * Disabled when DEBUG_MAIL is on :)
 * @author gizmore
 */
final class Form extends MethodForm
{
    public function isUserRequired() { return false; }
	public function isEnabled() { return (!GWF_DEBUG_EMAIL) || (GDT_IP::isLocal()); }
	
	public function createForm(GDT_Form $form)
	{
		$form->addField(GDT_Username::make('login')->tooltip('tt_recovery_login')->exists());
		if (Module_Recovery::instance()->cfgCaptcha())
		{
			$form->addField(GDT_Captcha::make());
		}
		$form->addField(GDT_AntiCSRF::make());
		$form->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$user = $form->getField('login')->gdo;
		if (!$user->hasMail())
		{
			return Error::error('err_recovery_needs_a_mail', [$user->displayName()]);
		}
		$this->sendMail($user);
		return Message::message('msg_recovery_mail_sent');
	}

	private function sendMail(GDO_User $user)
	{
		$token = GDO_UserRecovery::blank(['pw_user_id' => $user->getID()])->replace();
		$link = GDT_Link::anchor(url('Recovery', 'Change', "&userid={$user->getID()}&token=".$token->getToken()));

		$mail = Mail::botMail();
		$mail->setSubject(t('mail_subj_recovery', [sitename()]));
		$body = [$user->displayName(), sitename(), $link];
		$mail->setBody(t('mail_subj_body', $body));
		$mail->sendToUser($user);
	}
}
