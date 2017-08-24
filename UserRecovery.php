<?php
namespace GDO\Recovery;

use GDO\DB\GDO;
use GDO\DB\GDO_CreatedAt;
use GDO\Type\GDO_Token;
use GDO\User\GDO_User;
use GDO\User\User;

class UserRecovery extends GDO
{
	public function gdoCached() { return false; }
	public function gdoColumns()
	{
		return array(
			GDO_User::make('pw_user_id')->primary(),
			GDO_Token::make('pw_token')->notNull(),
			GDO_CreatedAt::make('pw_created_at'),
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
	 * @return UserRecovery
	 */
	public static function getByUserId(string $userid)
	{
		return self::getBy('pw_user_id', $userid);
	}
	
	/**
	 * 
	 * @param string $userid
	 * @param string $token
	 * @return UserRecovery
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
