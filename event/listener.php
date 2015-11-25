<?php
/**
*
* @package Canonical links for topic
* @copyright BB3.Mobi 2015 (c) Anvar (http://bb3.mobi)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace bb3mobi\canonical\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $user;

	public function __construct (\phpbb\user $user)
	{
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.append_sid'	=> 'delete_forum_id',
		);
	}

	public function delete_forum_id($event)
	{
		if ($this->user->data['is_registered'] && !$this->user->data['is_bot'])
		{
			return;
		}

		$params = $event['params'];

		if (!is_array($params))
		{
			$parents = array(
				'/f=([0-9]*)&(?:amp;)t=([0-9]*)/i',
				'/f=([0-9]*)&(?:amp;)p=([0-9]*)/i'
			);

			$params = preg_replace($parents, array('t=$2', 'p=$2'), $params);
		}

		if (is_array($params) && isset($params['f']) && isset($params['t']))
		{
			unset($params['f']); 
			sort($params);
		}

		$event['params'] = $params;
	}
}
