<?php
namespace GDO\Recovery;

use GDO\DB\GDO;
use GDO\DB\GDT_CreatedAt;
use GDO\Type\GDT_Token;
use GDO\User\GDT_User;
use GDO\User\GDO_User;

class GDO_UserRecovery extends GDO
{
	public function gdoCached() { return false; }
	public function gdoColumns()
	{
		return array(
			GDT_User::make('pw_user_id')->primary(),
			GDT_Token::make('pw_token')->notNull(),
			GDT_CreatedAt::make('pw_created_at'),
		);
	}
	
	public function getToken()
	{
		return $this->getVar('pw_token');
	}
	
	public function validateToken(string $token)
	{
		return $this->getToken() === $token;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->getValue('pw_user_id');
	}
	
	/**
	 * @param string $userid
	 * @return self
	 */
	public static function getByUserId(string $userid)
	{
		return self::getBy('pw_user_id', $userid);
	}
	
	/**
	 * 
	 * @param string $userid
	 * @param string $token
	 * @return self
	 */
	public static function getByUIDToken(string $userid, string $tok)
	{
		if ($token = self::getByUserId($userid))
		{
			if ($token->validateToken($tok))
			{
				return $token;
			}
		}
	}
		
}
