<?php
/**
 * @author: deliliu liuwenjie@e-neway.com
 * @since: 5/16/14 16:45
 */


use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TTransport;

class LTFramedTransportFactory extends \Thrift\Factory\TTransportFactory{
	/**
	 * @static
	 * @param TTransport $transport
	 * @return TTransport
	 */
	public static function getTransport(TTransport $transport) {
		return new TFramedTransport($transport);
	}
}