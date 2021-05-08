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
use GDO\UI\GDT_Link;
use GDO\User\GDT_Username;
use GDO\User\GDO_User;
use GDO\Net\GDT_IP;
use GDO\Mail\GDT_Email;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Template;
/**
 * Request Password Forgotten Token.
 * Disabled when DEBUG_MAIL is on :)
 * @author gizmore
 */
final class Form extends MethodForm
{
	public function isUserRequired() { return false; }
	public function isEnabled() { return (!GDO_DEBUG_EMAIL) || (GDT_IP::isLocal()); }
	
	public function createForm(GDT_Form $form)
	{
		if (Module_Recovery::instance()->cfgLogin())
		{
			$form->addField(GDT_Username::make('login')->tooltip('tt_recovery_login'));
		}
		if (Module_Recovery::instance()->cfgEmail())
		{
			$form->addField(GDT_Email::make('email')->tooltip('tt_recovery_email'));
		}
		if (Module_Recovery::instance()->cfgCaptcha())
		{
			$form->addField(GDT_Captcha::make());
		}
		$form->actions()->addField(GDT_Submit::make());
		$form->addField(GDT_AntiCSRF::make());

		GDT_Hook::callHook('RecoveryForm', $form);
	}
	
	public function formValidated(GDT_Form $form)
	{
		if (!($user = GDO_User::getByName($form->getFormVar('login'))))
		{
			if (!($user = GDO_User::table()->getBy('user_email', $form->getFormVar('email'))))
			{
				return $this->error('err_email_or_login')->add($this->renderPage());
			}
		}
		if (!$user->hasMail())
		{
			return $this->error('err_recovery_needs_a_mail', [$user->displayName()]);
		}
		$this->sendMail($user);
		return $this->message('msg_recovery_mail_sent');
	}

	public function sendMail(GDO_User $user)
	{
		$token = GDO_UserRecovery::blank(['pw_user_id' => $user->getID()])->replace();
		$link = GDT_Link::anchor(url('Recovery', 'Change', "&userid={$user->getID()}&token=".$token->getToken()));

		$mail = Mail::botMail();
		$mail->setSubject(t('mail_subj_recovery', [sitename()]));
		$tVars = array(
			'username' => $user->displayName(),
			'link' => $link,
		);
		$body = GDT_Template::php('Recovery', 'mail/recovery_mail.php', $tVars);
		$mail->setBody($body);
		$mail->sendToUser($user);
	}
	
	public function renderPage()
	{
		return $this->templatePHP('form.php', ['form'=>$this->getForm()]);
	}

}
