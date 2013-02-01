<?php
error_reporting(E_ALL ^ E_STRICT ^ E_NOTICE);
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('UTC');
}

// Include the DataSift library
require dirname(__FILE__).'/../lib/datasift.php';

// Include the configuration - put your username and API key in this file
require dirname(__FILE__).'/../config.php';

/**
 * This class will handle the events
 */
class EventHandler implements DataSift_IStreamConsumerEventHandler
{
	public function __construct() {	}
	/**
	 * Called when the stream is connected.
	 *
	 * @param DataSift_StreamConsumer $consumer The consumer object.
	 */
	public function onConnect($consumer)
	{
		echo 'Connected'.PHP_EOL.'--'.PHP_EOL;
	}

	/**
	 * Handle incoming data.
	 *
	 * @param DataSift_StreamConsumer $consumer The consumer object.
	 * @param array  $interaction The interaction data.
	 * @param string $hash The stream hash.
	 */
	public function onInteraction($consumer, $interaction, $hash)
	{
		echo $interaction['interaction']['content'].PHP_EOL.'--'.PHP_EOL;
	}

	/**
	 * Handle DELETE requests.
	 *
	 * @param DataSift_StreamConsumer $consumer The consumer object.
	 * @param array $interaction The interaction data.
	 * @param string $hash The stream hash.
	 */
	public function onDeleted($consumer, $interaction, $hash)
	{
		echo 'DELETE request for interaction ' . $interaction['interaction']['id']
			. ' of type ' . $interaction['interaction']['type']
			. '. Please delete it from your archive.'.PHP_EOL.'--'.PHP_EOL;
	}

	/**
	 * Called when a status message is received.
	 *
	 * @param DataSift_StreamConsumer $consumer    The consumer sending the
	 *                                             event.
	 * @param string                  $type        The status type.
	 * @param array                   $info        The data sent with the
	 *                                             status message.
	 */
	public function onStatus($consumer, $type, $info)
	{
		switch ($type) {
			default:
				echo 'STATUS: '.$type.PHP_EOL;
				break;
		}
	}

	/**
	 * Called when a warning occurs or is received down the stream.
	 *
	 * @param DataSift_StreamConsumer $consumer The consumer object.
	 * @param string $message The warning message.
	 */
	public function onWarning($consumer, $message)
	{
		echo 'WARNING: '.$message.PHP_EOL;
	}

	/**
	 * Called when a error occurs or is received down the stream.
	 *
	 * @param DataSift_StreamConsumer $consumer The consumer object.
	 * @param string $message The error message.
	 */
	public function onError($consumer, $message)
	{
		echo 'ERROR: '.$message.PHP_EOL;
	}

	/**
	 * Called when the stream is disconnected.
	 *
	 * @param DataSift_StreamConsumer $consumer The consumer object.
	 */
	public function onDisconnect($consumer)
	{
		echo 'Disconnected'.PHP_EOL;
	}

	/**
	 * Called when the consumer has stopped.
	 *
	 * @param DataSift_StreamConsumer $consumer The consumer object.
	 * @param string $reason The reason the consumer stopped.
	 */
	public function onStopped($consumer, $reason)
	{
		echo PHP_EOL.'Stopped: '.$reason.PHP_EOL.PHP_EOL;
	}
}


try {
	// Authenticate
	echo "Creating user...\n";
	$user = new DataSift_User(DS_USERNAME, DS_API_KEY);

	$CSDL = 'tag "32" { stream "d902fe86052eae0ba09a78ab66fc2b5a" } return { stream "d902fe86052eae0ba09a78ab66fc2b5a" }';

	// Create the definition
	echo "Creating definition...\n  $CSDL\n";
	$definition = new DataSift_Definition($user, $CSDL);

	// Create the consumer
	echo "Getting the consumer...\n";
	// $consumer = $definition->getConsumer(DataSift_StreamConsumer::TYPE_HTTP, new EventHandler());
	$consumer = $definition->getConsumer('MAU', new EventHandler());

	// And start consuming
	echo "Consuming...\n--\n";
	$consumer->consume();
} catch(DataSift_Exception_AccessDenied $e) {
	echo $e;
} catch(Exception $e) {
	echo "Dunno whats wrong man!\n";
	echo $e;
}