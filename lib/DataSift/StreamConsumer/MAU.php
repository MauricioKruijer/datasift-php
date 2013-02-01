<?php
/**
 * DataSift client
 *
 * This software is the intellectual property of MediaSift Ltd., and is covered
 * by retained intellectual property rights, including copyright.
 * Distribution of this software is strictly forbidden under the terms of this license.
 *
 * @category  DataSift
 * @package   PHP-client
 * @author    Stuart Dallas <stuart@3ft9.com>
 * @copyright 2011 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */

/**
 * The DataSift_StreamConsumer_HTTP class extends DataSift_StreamConsumer
 * and implements HTTP streaming.
 *
 * @category DataSift
 * @package  PHP-client
 * @author   Stuart Dallas <stuart@3ft9.com>
 * @license  http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link     http://www.mediasift.com
 */
class DataSift_StreamConsumer_MAU extends DataSift_StreamConsumer_HTTP
{
	private static $_intCounter = 0;
	private $_oldCSDL  = 'tag "35" { stream "48be55c61323ab3546b9b2f5f3584662"}return { stream "48be55c61323ab3546b9b2f5f3584662"}';
	/**
	 * @var resource The HTTP connection resource
	 */
	private $_conn = null;

	/**
	 * Constructor.
	 *
	 * @param DataSift_User $user          The authenticated user
	 * @param mixed         $definition    CSDL string, Definition object, or array of hashes
	 * @param mixed         $eventHandler  An object that implements IStreamConsumerEventHandler
	 *
	 * @throws DataSift_Exception_InvalidData
	 * @throws DataSift_Exceotion_CompileFailed
	 * @throws DataSift_Exception_APIError
	 * @see DataSift_StreamConsumer::__construct
	 */
	public function __construct($user, $definition, $eventHandler)
	{
		parent::__construct($user, $definition, $eventHandler);
	}
	protected function onStatus($type, $info = array()) {
	
		$strCurrentCSDL = $this->_definition->get();

		if($strCurrentCSDL !== $this->_oldCSDL && self::$_intCounter == 2) {
			self::$_intCounter = 0;

			echo "changed ".md5($strCurrentCSDL)." to " . md5($this->_oldCSDL) . "\n";

			$this->_definition = $this->_user->createDefinition($this->_oldCSDL);
			$this->connect();
			$this->_oldCSDL = $strCurrentCSDL;

			echo "\t\t reconnected\n";
		}

		self::$_intCounter++;
		echo self::$_intCounter." " . date('H:i:s ');

		parent::onStatus($type, $info);
	}
}
?>